<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Setting;
use App\Repositories\SettingRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

trait SettingOperation
{
    /**
     * @var SettingRepository
     */
    private SettingRepository $settingRepository;

    /**
     * @param SettingRepository $settingRepository
     */
    public function __construct(SettingRepository $settingRepository)
    {
        parent::__construct();

        $this->settingRepository = $settingRepository;
    }

    /**
     * @param string $segment Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupSettingRoutes(string $segment, string $routeName, string $controller): void
    {
        Route::get($segment . '/', [
            'as' => $routeName . '.getSetting',
            'uses' => $controller . '@getSetting',
            'operation' => 'setting',
        ]);

        Route::post($segment . '/', [
            'as' => $routeName . '.postSetting',
            'uses' => $controller . '@postSetting',
            'operation' => 'setting',
        ]);
    }

    /**
     * @return void
     */
    protected function setupSettingDefaults(): void
    {
        $this->crud->allowAccess('setting');

        $this->crud->addSaveAction([
            'name' => 'save_and_stay',
            'button_text' => trans('backpack::crud.save_action_save_and_stay'),
        ]);

        $this->crud->operation('setting', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
            $this->crud->setupDefaultSaveActions();
        });
    }

    /**
     * @return View
     */
    public function getSetting(): View
    {
        $this->crud->hasAccessOrFail('setting');

        $this->data['entry'] = $this->crud->getEntry($this->settingRepository->getSettingRecord()->getAttribute('id'));

        $this->crud->setOperationSetting('fields', $this->crud->getUpdateFields());
        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.setting');
        $this->data['saveAction'] = $this->crud->getSaveAction();

        return view("crud::operations.setting", $this->data);
    }

    /**
     * @return RedirectResponse
     */
    public function postSetting(): RedirectResponse
    {
        $this->crud->hasAccessOrFail('setting');

        $this->crud->validateRequest();

        $this->settingRepository->store($this->settingRepository->getSettingRecord(), $this->crud->getStrippedSaveRequest());

        \Alert::success(trans('backpack::crud.setting_update_success'))->flash();

        return \Redirect::to($this->crud->route);
    }
}
