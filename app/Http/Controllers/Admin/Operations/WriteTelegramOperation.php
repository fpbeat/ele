<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Exceptions\Botman\TelegramSendException;
use App\Facades\Message;
use App\Facades\TelegramClient;
use App\Repositories\TelegramUserMessageRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

trait WriteTelegramOperation
{
    /**
     * @var TelegramUserMessageRepository
     */
    private TelegramUserMessageRepository $telegramUserMessageRepository;

    /**
     * @param TelegramUserMessageRepository $telegramUserMessageRepository
     */
    public function __construct(TelegramUserMessageRepository $telegramUserMessageRepository)
    {
        $this->telegramUserMessageRepository = $telegramUserMessageRepository;
    }

    /**
     * @param string $segment
     * @param string $routeName
     * @param string $controller
     */
    protected function setupWriteTelegramRoutes(string $segment, string $routeName, string $controller): void
    {
        Route::get($segment . '/{id}/write', [
            'as' => $routeName . '.getWriteTelegram',
            'uses' => $controller . '@getWriteTelegramForm',
            'operation' => 'write',
        ]);
        Route::post($segment . '/{id}/write', [
            'as' => $routeName . '.postWriteTelegram',
            'uses' => $controller . '@postWriteTelegramForm',
            'operation' => 'write',
        ]);
    }

    /**
     * @return void
     */
    protected function setupWriteTelegramDefaults(): void
    {
        $this->crud->allowAccess('write');

        $this->crud->operation('write', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
            $this->crud->setupDefaultSaveActions();
        });

        $this->crud->operation('list', function () {
            $this->crud->addButton('line', 'write_telegram', 'view', 'crud::buttons.write_telegram');
        });

        $this->crud->operation('show', function () {
            $this->crud->addButton('line', 'write_telegram', 'view', 'crud::buttons.write_telegram');
        });
    }

    /**
     * @param int $id
     * @return View
     */
    public function getWriteTelegramForm(int $id): View
    {
        $this->crud->hasAccessOrFail('write');

        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.write_message');

        $id = $this->crud->getCurrentEntryId() ?? $id;

        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['saveAction'] = $this->crud->getSaveAction();

        return view("crud::write_telegram", $this->data);
    }

    /**
     * @return RedirectResponse
     */
    public function postWriteTelegramForm(): RedirectResponse
    {
        $this->crud->hasAccessOrFail('write');

        $this->crud->validateRequest();

        $message = $this->telegramUserMessageRepository->store($this->crud->getStrippedSaveRequest());

        try {
            TelegramClient::sendMessage($message->user->user_id, Message::get('writeTelegramUser') . "\n\n" . $message->message);
            $this->telegramUserMessageRepository->setSentStatus($message->id);

            \Alert::success(trans('backpack::crud.telegram_write_success'))->flash();
        } catch (TelegramSendException $e) {
            \Alert::error(trans('backpack::crud.telegram_write_failed'))->flash();
        }

        return \Redirect::to($this->crud->route . '/' . $message->user->id . '/write');
    }
}
