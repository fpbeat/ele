<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Message;
use App\Http\Controllers\Admin\Operations\WriteTelegramOperation;
use App\Http\Requests\WriteTelegramUserRequest;
use App\Models\Feedback;
use Illuminate\Support\Str;
use App\Repositories\{FeedbackRepository, TelegramUserMessageRepository};
use Backpack\CRUD\app\Http\Controllers\{CrudController, Operations\ListOperation, Operations\ShowOperation};
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Auth;

/**
 * Class TelegramUserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FeedbackCrudController extends CrudController
{
    use ListOperation;
    use ShowOperation;
    use WriteTelegramOperation;

    /**
     * @var int
     */
    const MESSAGE_WRAP_LENGTH = 45;

    /**
     * @var TelegramUserMessageRepository
     */
    private TelegramUserMessageRepository $telegramUserMessageRepository;

    /**
     * @var FeedbackRepository
     */
    private FeedbackRepository $feedbackRepository;

    /**
     * @param TelegramUserMessageRepository $telegramUserMessageRepository
     * @param FeedbackRepository $feedbackRepository
     */
    public function __construct(TelegramUserMessageRepository $telegramUserMessageRepository, FeedbackRepository $feedbackRepository)
    {
        parent::__construct();

        $this->telegramUserMessageRepository = $telegramUserMessageRepository;
        $this->feedbackRepository = $feedbackRepository;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function setup(): void
    {
        CRUD::setModel(Feedback::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/feedback');
        CRUD::setEntityNameStrings('записи', 'обратная связь');
    }

    /**
     * @return void
     */
    protected function setupListOperation(): void
    {
        $this->setupListFilters();
        $this->crud->addButtonFromView('show', 'moderate', 'moderate', 'beginning');

        $this->crud->setColumns([
            [
                'name' => 'message',
                'label' => 'Сообщение',
                'type' => 'closure',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhere('message', 'like', '%' . $searchTerm . '%');
                },
                'function' => function ($entry) {
                    $message = Str::wordwrap($entry->message_brief, self::MESSAGE_WRAP_LENGTH);

                    return nl2br($message);
                },
            ],
            [
                'name' => 'user',
                'label' => 'Пользователь',
                'type' => 'user',
                'orderable' => true,
                'function' => function ($entry) {
                    return optional($entry->user)->full_name;
                },
                'orderLogic' => function ($query, $column, $columnDirection) {
                    return $query
                        ->select('feedback.*')
                        ->leftJoin('telegram_users', 'telegram_users.id', '=', 'feedback.user_id')
                        ->orderBy('telegram_users.full_name', $columnDirection);
                },
                'link' => function ($crud, $column, $entry) {
                    return backpack_url(sprintf('telegram/user/%d/show', $entry->user->id ?? null));
                }
            ],
            [
                'name' => 'type',
                'label' => 'Тип',
                'type' => 'closure',
                'function' => function ($entry) {
                    return trans('backpack::crud.feedback_type_' . $entry->type);
                },
                'wrapper' => [
                    'element' => 'button',
                    'class' => function ($crud, $column, $entry) {
                        return $entry->type === Feedback::TYPE_REVIEW ? 'btn btn-sm btn-no-active btn-outline-success' : 'btn btn-sm btn-no-active btn-outline-danger';
                    }
                ],
            ],
            [
                'name' => 'created_at',
                'label' => 'Дата создания',
                'type' => 'datetime'
            ],
        ]);
    }

    /**
     * @return void
     */
    public function setupWriteOperation(): void
    {
        $this->crud->setValidation(WriteTelegramUserRequest::class);

        $this->crud->addFields([
            [
                'type' => 'messages',
                'name' => 'messages',
                'values' => $this->telegramUserMessageRepository->getAllByUserId($this->crud->getCurrentEntry()->user->id ?? 0)
            ],
            [
                'name' => 'message',
                'label' => 'Сообщение',
                'type' => 'summernote',
                'options' => config('backpack.fields.summernote.options'),
            ]
        ]);
    }

    /**
     * @return void
     */
    protected function setupShowOperation(): void
    {
        $this->crud->set('show.setFromDb', false);

        $this->crud->setColumns([
            [
                'name' => 'user',
                'attribute' => 'full_name',
                'label' => 'Пользователь',
                'type' => 'relationship'
            ],
            [
                'name' => 'created_at',
                'label' => 'Создано',
                'type' => 'datetime'
            ],
            [
                'name' => 'type',
                'label' => 'Тип',
                'type' => 'radio',
                'options' => [Feedback::TYPE_REVIEW => 'Отзыв', Feedback::TYPE_PROPOSAL => 'Предложение']
            ],
            [
                'name' => 'message',
                'escaped' => false,
                'label' => 'Сообщение',
                'type' => 'text'
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function getWriteTelegramRequest(array $request): array
    {
        $entry = $this->crud->getCurrentEntry();

        return array_merge($request, [
            'author_id' => Auth::guard('backpack')->user()->getAuthIdentifier(),
            'user_id' => optional($entry->user)->id,
            'caption_text' => Message::get(sprintf('writeTelegramUser%s', ucfirst($entry->type))),
            'event' => $entry->type
        ]);
    }

    /**
     * @return void
     */
    protected function setupListFilters(): void
    {
        $this->crud->addFilter([
            'label' => 'Сообщение',
            'type' => 'text',
            'name' => 'message',
        ], false, function ($value) {
            $this->crud->addClause('where', 'message', 'LIKE', '%' . $value . '%');
        });

        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'user_id',
            'label' => 'Пользователь'
        ], $this->feedbackRepository->getGroupedUsersArray());

        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'type',
            'label' => 'Тип'
        ], function () {
            return [Feedback::TYPE_REVIEW => 'Отзыв', Feedback::TYPE_PROPOSAL => 'Предложение'];
        }, function ($value) {
            $this->crud->addClause('where', 'type', '=', $value);
        });

        $this->crud->addFilter([
            'type' => 'date_range',
            'name' => 'created_at',
            'label' => 'Дата создания'
        ], false, function ($value) {
            $dates = json_decode($value);

            $this->crud->addClause('where', 'created_at', '>=', $dates->from);
            $this->crud->addClause('where', 'created_at', '<=', $dates->to);
        });
    }
}
