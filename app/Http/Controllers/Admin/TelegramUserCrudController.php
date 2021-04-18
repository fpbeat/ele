<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\WriteTelegramOperation;
use App\Http\Requests\WriteTelegramUserRequest;
use App\Models\TelegramUser;
use App\Repositories\{TelegramUserMessageRepository, TelegramUserRepository};
use Backpack\CRUD\app\Http\Controllers\{CrudController, Operations\ListOperation, Operations\ShowOperation, Operations\UpdateOperation};
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Auth;

/**
 * Class TelegramUserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TelegramUserCrudController extends CrudController
{
    use ListOperation;
    use ShowOperation;
    use UpdateOperation;
    use WriteTelegramOperation;

    /**
     * @var TelegramUserRepository
     */
    private TelegramUserRepository $telegramUserRepository;

    /**
     * @var TelegramUserMessageRepository
     */
    private TelegramUserMessageRepository $telegramUserMessageRepository;

    /**
     * @param TelegramUserRepository $telegramUserRepository
     * @param TelegramUserMessageRepository $telegramUserMessageRepository
     */
    public function __construct(TelegramUserRepository $telegramUserRepository, TelegramUserMessageRepository $telegramUserMessageRepository)
    {
        parent::__construct();

        $this->telegramUserRepository = $telegramUserRepository;
        $this->telegramUserMessageRepository = $telegramUserMessageRepository;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function setup(): void
    {
        CRUD::setModel(TelegramUser::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/telegram/user');
        CRUD::setEntityNameStrings('посетителей', 'посетители');
    }

    /**
     * @return void
     */
    protected function setupListOperation(): void
    {
        $this->setupListFilters();
        $this->crud->removeButton('show');
        $this->crud->addButtonFromView('show', 'moderate', 'moderate', 'beginning');
        $this->crud->setColumns([
            [
                'name' => 'full_name',
                'label' => 'Пользователь',
                'type' => 'text',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhere('full_name', 'like', '%' . $searchTerm . '%');
                }
            ],
            [
                'name' => 'username',
                'label' => 'Логин',
                'type' => 'closure',
                'function' => function ($entry) {
                    return $entry->username ?? '-';
                },
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhere('username', 'like', '%' . $searchTerm . '%');
                }
            ],
            [
                'name' => 'lastPage',
                'label' => 'Последня страница',
                'type' => 'relationship',
                'orderable' => true,
                'orderLogic' => function ($query, $column, $columnDirection) {
                    return $query
                        ->leftJoin('pages', 'pages.id', '=', 'telegram_users.last_page_id')
                        ->orderBy('pages.name', $columnDirection);
                }
            ],
            [
                'name' => 'updated_at',
                'label' => 'Время активности',
                'type' => 'datetime'
            ],
            [
                'name' => 'is_active',
                'label' => 'Активен',
                'type' => 'check'
            ]
        ]);
    }

    protected function setupUpdateOperation(): void
    {
        $this->crud->addFields([
            [
                'name' => 'user_id',
                'label' => 'ID пользователя',
                'type' => 'text',
                'attributes' => [
                    'disabled' => true
                ]
            ],
            [
                'name' => 'username',
                'label' => 'Логин',
                'type' => 'text',
                'attributes' => [
                    'disabled' => true
                ]
            ],
            [
                'name' => 'full_name',
                'label' => 'Пользователь',
                'type' => 'text',
                'attributes' => [
                    'disabled' => true
                ]
            ],
            [
                'name' => 'created_at',
                'label' => 'Создано',
                'type' => 'datetime',
                'attributes' => [
                    'disabled' => true
                ]
            ],
            [
                'name' => 'updated_at',
                'label' => 'Обновлено',
                'type' => 'datetime',
                'attributes' => [
                    'disabled' => true
                ]
            ],
            [
                'name' => 'locked',
                'label' => 'Статус пользователя',
                'type' => 'select2_from_array',
                'options' => ['Активен', 'Заблокирован'],
                'attributes' => [
                    'data-minimum-results-for-search' => -1
                ]
            ]
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
                'name' => 'super',
                'values' => $this->telegramUserMessageRepository->getAllByUserId($this->crud->getCurrentEntry()->id)
            ],
            [
                'type' => 'hidden',
                'name' => 'author_id',
                'value' => Auth::guard('backpack')->user()->getAuthIdentifier()
            ],
            [
                'type' => 'hidden',
                'name' => 'user_id',
                'value' => $this->crud->getCurrentEntry()->id
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
                'name' => 'user_id',
                'label' => 'ID пользователя',
                'type' => 'text'
            ],
            [
                'name' => 'username',
                'label' => 'Логин',
                'type' => 'text'
            ],
            [
                'name' => 'full_name',
                'label' => 'Пользователь',
                'type' => 'text'
            ],
            [
                'name' => 'lastPage',
                'label' => 'Последня страница',
                'type' => 'relationship',
            ],
            [
                'name' => 'language_code',
                'label' => 'Язык',
                'type' => 'text'
            ],
            [
                'name' => 'created_at',
                'label' => 'Создано',
                'type' => 'datetime'
            ],
            [
                'name' => 'updated_at',
                'label' => 'Обновлено',
                'type' => 'datetime'
            ],
            [
                'name' => 'locked',
                'label' => 'Статус пользователя',
                'type' => 'boolean',
                'options' => ['Активен', 'Заблокирован']
            ]
        ]);
    }

    /**
     * @return void
     */
    protected function setupListFilters(): void
    {
        $this->crud->addFilter([
            'label' => 'Пользователь',
            'type' => 'text',
            'name' => 'full_name',
        ], false, function ($value) {
            $this->crud->addClause('where', 'full_name', 'LIKE', '%' . $value . '%');
        });

        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'last_page_id',
            'label' => 'Последня страница'
        ], $this->telegramUserRepository->getGroupedPagesArray());

        $this->crud->addFilter([
            'type' => 'date_range',
            'name' => 'updated_at',
            'label' => 'Время активности'
        ], false, function ($value) {
            $dates = json_decode($value);

            $this->crud->addClause('where', 'telegram_users.updated_at', '>=', $dates->from);
            $this->crud->addClause('where', 'telegram_users.updated_at', '<=', $dates->to);
        });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'locked',
            'label' => 'Активен'
        ], false, function ($value) {
            $this->crud->addClause('where', 'telegram_users.locked', '=', $value);
        });
    }
}
