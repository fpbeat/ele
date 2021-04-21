<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\SettingOperation;
use App\Http\Requests\SettingRequest;
use App\Models\Setting as SettingModel;
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

        $this->crud->addFields([
            [
                'type' => 'address_algolia',
                'name' => 'contact__address',
                'label' => 'Адрес магазина',
                'tab' => trans('backpack::crud.form_tab_contacts'),
                'store_as_json' => true
            ],
//            [
//                'type' => 'text',
//                'name' => 'Описание',
//                'tab' => trans('backpack::crud.form_tab_contacts'),
//            ]
        ]);
    }
}
