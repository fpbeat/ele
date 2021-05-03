<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\SettingOperation;
use App\Http\Requests\SettingRequest;
use App\Models\Setting as SettingModel;
use App\Notifications\SendFeedback;
use App\Repositories\PageRepository;
use App\Repositories\SettingRepository;
use Backpack\CRUD\app\Http\Controllers\{CrudController, Operations\ListOperation};
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SubscriptionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SettingCrudController extends CrudController
{
    use ListOperation;
    use SettingOperation;

    /**
     * @var PageRepository
     */
    private PageRepository $pageRepository;

    /**
     * @var SettingRepository
     */
    protected SettingRepository $settingRepository;

    public function __construct(SettingRepository $settingRepository, PageRepository $pageRepository)
    {
        parent::__construct();

        $this->settingRepository = $settingRepository;
        $this->pageRepository = $pageRepository;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function setup(): void
    {
        CRUD::setModel(SettingModel::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/setting');
        CRUD::setEntityNameStrings('настройки', 'настройки');
    }

    /**
     * @return void
     */
    public function setupSettingOperation(): void
    {
        $this->crud->setValidation(SettingRequest::class);

        $this->contactsFields();
        $this->feedbackFields();
        $this->notificationsFields();
    }

    /**
     * @return void
     */
    private function contactsFields()
    {
        $this->crud->addFields([
            [
                'type' => 'address_algolia',
                'name' => 'contact__address',
                'label' => 'Адрес магазина',
                'tab' => trans('backpack::crud.form_tab_contacts'),
                'store_as_json' => true
            ]
        ]);
    }

    /**
     * @return void
     */
    private function notificationsFields()
    {
        $this->crud->addFields([
            [
                'type' => 'select2_from_array',
                'name' => 'notifications__events',
                'label' => 'Уведомлять о событиях',
                'options' => [
                    'order' => 'Новый заказ',
                    'review' => 'Новый отзыв',
                    'proposal' => 'Новое предложение',
                ],
                'allows_null' => false,
                'allows_multiple' => true,
                'tab' => trans('backpack::crud.form_tab_notifications'),
            ],
            [
                'type' => 'table',
                'name' => 'notifications__groups',
                'label' => 'ID Telegram групп',
                'entity_singular' => 'группу',
                'columns' => [
                    'id' => '',
                ],
                'min' => 1,
                'tab' => trans('backpack::crud.form_tab_notifications'),
            ],
            [
                'tab' => trans('backpack::crud.form_tab_notifications'),
                'name'  => 'notifications__time_range',
                'label' => 'Время рассылки',
                'type'  => 'time_range'
            ]
        ]);
    }

    /**
     * @return void
     */
    private function feedbackFields(): void
    {
        $this->crud->addFields([
            [
                'type' => 'select2_from_array',
                'name' => 'feedback__redirect',
                'label' => 'Открыть после действия',
                'options' => $this->pageRepository->getTreeArray(),
                'allows_null' => false,
                'tab' => trans('backpack::crud.form_tab_feedback'),
            ],
            [

                'type' => 'number',
                'name' => 'feedback__redirect_delay',
                'label' => 'Задержка перед открытием, сек.',
                'default' => 2,
                'tab' => trans('backpack::crud.form_tab_feedback'),
            ]
        ]);
    }
}
