<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ChannelRequest;
use Backpack\CRUD\{app\Http\Controllers\CrudController, app\Http\Controllers\Operations\CreateOperation, app\Http\Controllers\Operations\DeleteOperation, app\Http\Controllers\Operations\ListOperation, app\Http\Controllers\Operations\ShowOperation, app\Http\Controllers\Operations\UpdateOperation, app\Library\CrudPanel\CrudPanelFacade as CRUD};

/**
 * Class ChannelCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ChannelCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    /**
     * @return void
     * @throws \Exception
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Channel::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/channel');
        CRUD::setEntityNameStrings('channel', 'channels');
    }

    /**
     * @return void
     */
    protected function setupListOperation(): void
    {
        $this->crud->removeButton('show');

        $this->crud->setColumns([
            [
                'name' => 'name',
                'label' => 'Channel name',
                'type' => 'string',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%');
                },
            ]
        ]);
    }

    /**
     * @return void
     */
    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(ChannelRequest::class);

        $this->crud->addFields([
            [
                'name' => 'name',
                'label' => 'Channel name',
                'type' => 'text'
            ],
            [
                'name' => 'channel_id',
                'label' => 'Channel ID',
                'type' => 'text'
            ]
        ]);
    }

    /**
     * @return void
     */
    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();
    }
}
