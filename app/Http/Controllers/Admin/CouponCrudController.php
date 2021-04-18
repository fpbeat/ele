<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CouponRequest;
use Carbon\Carbon;
use Backpack\CRUD\app\{Http\Controllers\CrudController, Http\Controllers\Operations\CreateOperation, Http\Controllers\Operations\DeleteOperation, Http\Controllers\Operations\ListOperation, Http\Controllers\Operations\ShowOperation, Http\Controllers\Operations\UpdateOperation, Library\CrudPanel\CrudPanelFacade as CRUD};

/**
 * Class CouponCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CouponCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Coupon::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/coupon');
        CRUD::setEntityNameStrings('coupon', 'coupons');
    }

    /**
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->removeButton('show');

        $this->crud->query->withCount('members');
        $this->crud->setColumns([
            [
                'name' => 'value',
                'label' => 'Coupon',
                'type' => 'string',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->where('value', 'like', '%' . $searchTerm . '%');
                },
            ],
            [
                'name' => 'sale',
                'type' => 'number',
                'searchLogic' => false
            ],
            [
                'name' => 'subscriptions',
                'type' => 'select_multiple',
                'attribute' => 'name',
                'limit' => 30,
                'wrapper' => [
                    'href' => function ($crud, $column, $entry, $key) {
                        return backpack_url(sprintf('subscription/%d/show', $key));
                    },
                    'class' => 'btn btn-sm btn-light',
                ],
                'searchLogic' => false
            ],
            [
                'name' => 'from',
                'type' => 'closure',
                'function' => function ($entry) {
                    return $entry->from ? Carbon::parse($entry->from)->isoFormat('D MMM YYYY') : '-';
                },
                'searchLogic' => false
            ],
            [
                'name' => 'till',
                'type' => 'closure',
                'function' => function ($entry) {
                    return $entry->till ? Carbon::parse($entry->till)->isoFormat('D MMM YYYY') : '-';
                },
                'searchLogic' => false
            ],
            [
                'label' => 'Used',
                'name' => 'members_count',
                'type' => 'text',
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
                'name' => 'value',
                'label' => 'Coupon',
                'type' => 'string'
            ],
            [
                'name' => 'sale',
                'type' => 'number',
                'suffix' => '%'
            ],
            [
                'name' => 'subscriptions',
                'type' => 'select_multiple',
                'attribute' => 'name',
                'limit' => 30,
                'wrapper' => [
                    'href' => function ($crud, $column, $entry, $key) {
                        return backpack_url(sprintf('subscription/%d/show', $key));
                    },
                    'class' => 'btn btn-sm btn-light',
                ]
            ],
            [
                'name' => 'from',
                'type' => 'closure',
                'function' => function ($entry) {
                    return $entry->from ? Carbon::parse($entry->from)->isoFormat('D MMM YYYY') : '-';
                }
            ],
            [
                'name' => 'till',
                'type' => 'closure',
                'function' => function ($entry) {
                    return $entry->till ? Carbon::parse($entry->till)->isoFormat('D MMM YYYY') : '-';
                }
            ]
        ]);
    }

    /**
     * @return void
     */
    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(CouponRequest::class);

        $this->crud->addFields([
            [
                'name' => 'value',
                'label' => 'Coupon',
                'type' => 'text'
            ],
            [
                'name' => 'sale',
                'label' => 'Sale, %',
                'type' => 'number'
            ],
            [
                'name' => 'subscriptions',
                'type' => 'select2_multiple'
            ],
            [
                'name' => 'from',
                'type' => 'date_picker',
                'attributes' => [
                    'data-date-clear-btn' => 'true'
                ]
            ],
            [
                'name' => 'till',
                'type' => 'date_picker',
                'attributes' => [
                    'data-date-clear-btn' => 'true'
                ]
            ],
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
