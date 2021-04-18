<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SubscriptionRequest;
use Backpack\CRUD\app\Http\Controllers\{CrudController, Operations\CreateOperation, Operations\DeleteOperation, Operations\ListOperation, Operations\ShowOperation, Operations\UpdateOperation};
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SubscriptionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SubscriptionCrudController extends CrudController
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
    public function setup(): void
    {
        CRUD::setModel(\App\Models\Subscription::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/subscription');
        CRUD::setEntityNameStrings('subscription', 'subscriptions');
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
                'type' => 'string',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%');
                },
            ],
            [
                'name' => 'channel_id',
                'type' => 'relationship',
                'wrapper' => [
                    'href' => function ($crud, $column, $entry, $key) {
                        return backpack_url(sprintf('channel/%d/show', $key));
                    },
                    'class' => 'btn btn-sm btn-light',
                ],
            ],
            [
                'name' => 'duration',
                'type' => 'number',
                'searchLogic' => false
            ],
            [
                'name' => 'price',
                'type' => 'number',
                'decimals' => 2,
                'dec_point' => '.',
                'thousands_sep' => ' ',
                'suffix' => ' €',
                'searchLogic' => false
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
                'name' => 'name',
                'type' => 'string',
            ],
            [
                'name' => 'description',
                'type' => 'text',
            ],
            [
                'name' => 'channel_id',
                'type' => 'relationship',
                'wrapper' => [
                    'href' => function ($crud, $column, $entry, $key) {
                        return backpack_url(sprintf('channel/%d/show', $key));
                    },
                    'class' => 'btn btn-sm btn-light',
                ],
            ],
            [
                'name' => 'duration',
                'type' => 'number',
                'suffix' => ' days'
            ],
            [
                'name' => 'price',
                'type' => 'number',
                'decimals' => 2,
                'dec_point' => '.',
                'thousands_sep' => ' ',
                'suffix' => ' €'
            ]
        ]);
    }


    /**
     * @return void
     */
    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(SubscriptionRequest::class);

        $this->crud->addFields([
            [
                'name' => 'name',
                'type' => 'text'
            ],
            [
                'name' => 'description',
                'type' => 'textarea'
            ],
            [
                'type' => 'select2',
                'name' => 'channel_id',
            ],
            [
                'name' => 'duration',
                'label' => 'Duration (days)',
                'type' => 'number'
            ],
            [
                'name' => 'price',
                'type' => 'number',
                'attributes' => [
                    'step' => 'any'
                ]
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
