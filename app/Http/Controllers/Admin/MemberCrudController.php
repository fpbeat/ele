<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MemberRequest;
use App\Models\Coupon;
use App\Models\Member;
use App\Models\Subscription;
use App\Repositories\MemberRepository;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\{DeleteOperation, ListOperation, ShowOperation, UpdateOperation};
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class MemberCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MemberCrudController extends CrudController
{
    use ListOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    /**
     * @var MemberRepository
     */
    private $memberRepository;

    /**
     * @param MemberRepository $memberRepository
     */
    public function __construct(MemberRepository $memberRepository)
    {
        parent::__construct();

        $this->memberRepository = $memberRepository;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function setup(): void
    {
        CRUD::setModel(\App\Models\Member::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/member');
        CRUD::setEntityNameStrings('member', 'members');
    }

    /**
     * @return void
     */
    protected function setupListOperation(): void
    {
        $this->crud->addClause('daysLeft');

        $this->crud->setColumns([
            [
                'name' => 'first_last_name',
                'label' => 'Member name',
                'type' => 'text',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->where('first_last_name', 'like', '%' . $searchTerm . '%');
                },
            ],
            [
                'name' => 'subscription',
                'type' => 'relationship',
                'wrapper' => [
                    'href' => function ($crud, $column, $entry, $key) {
                        return backpack_url(sprintf('subscription/%d/show', $key));
                    },
                    'class' => 'btn btn-sm btn-light',
                ],
                'orderable' => true,
                'orderLogic' => function ($query, $column, $columnDirection) {
                    return $query
                        ->leftJoin('subscriptions', 'members.subscription_id', '=', 'subscriptions.id')
                        ->orderBy('subscriptions.name', $columnDirection);
                }
            ],
            [
                'name' => 'coupon',
                'type' => 'relationship',
                'attribute' => 'value',
                'wrapper' => [
                    'href' => function ($crud, $column, $entry, $key) {
                        return backpack_url(sprintf('coupon/%d/show', $key));
                    },
                    'class' => 'btn btn-sm btn-light',
                ],
            ],
            [
                'name' => 'active',
                'type' => 'check'
            ],
            [
                'name' => 'up',
                'type' => 'date',
                'format' => 'D MMM YYYY HH:mm'
            ],
            [
                'name' => 'till',
                'type' => 'date',
                'format' => 'D MMM YYYY HH:mm'
            ],
            [
                'name' => 'days_left',
                'type' => 'number',
                'orderable' => true,
                'orderLogic' => function ($query, $column, $columnDirection) {
                    return $query->orderBy('days_left', $columnDirection);
                }
            ]
        ]);

        $this->setupListFilters();
    }

    /**
     * @return void
     */
    protected function setupShowOperation(): void
    {
        $this->crud->set('show.setFromDb', false);

        $this->crud->addColumns([
            [
                'name' => 'order_no',
                'type' => 'text',
            ],
            [
                'name' => 'first_last_name',
                'label' => 'Member Name',
                'type' => 'text',
            ],
            [
                'name' => 'username',
                'label' => 'Username',
                'type' => 'closure',
                'function' => function ($entry) {
                    return $entry->username ?? '-';
                },
            ],
            [
                'name' => 'user_id',
                'label' => 'User ID',
                'type' => 'text',
            ],
            [
                'name' => 'subscription',
                'type' => 'relationship',
                'wrapper' => [
                    'href' => function ($crud, $column, $entry, $key) {
                        return backpack_url(sprintf('subscription/%d/show', $key));
                    },
                    'class' => 'btn btn-sm btn-light',
                ],
            ],
            [
                'name' => 'coupon',
                'type' => 'relationship',
                'attribute' => 'value',
                'wrapper' => [
                    'href' => function ($crud, $column, $entry, $key) {
                        return backpack_url(sprintf('coupon/%d/show', $key));
                    },
                    'class' => 'btn btn-sm btn-light',
                ],
            ],
            [
                'name' => 'active',
                'type' => 'boolean'
            ],
            [
                'name' => 'up',
                'type' => 'date',
                'format' => 'D MMM YYYY HH:mm'
            ],
            [
                'name' => 'till',
                'type' => 'date',
                'format' => 'D MMM YYYY HH:mm'
            ],
            [
                'name' => 'user_id',
                'label' => 'User ID',
                'type' => 'text',
            ],
            [
                'name' => 'days_left',
                'type' => 'closure',
                'function' => function () {
                    $member = $this->memberRepository->find($this->crud->getCurrentEntryId());

                    return $member->days_left;
                },
            ],
            [
                'name' => 'invite_link',
                'type' => 'string'
            ]
        ]);

        $this->crud->addButtonFromModelFunction('line', 'telegram_link', 'telegramLink', 'end');
    }

    /**
     * @return void
     */
    protected function setupListFilters(): void
    {
        $this->crud->addFilter([
            'label' => 'Member name',
            'type' => 'text',
            'name' => 'username',
        ], false, function ($value) {
            $this->crud->addClause('where', 'first_last_name', 'LIKE', "%$value%");
        });

        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'subscription',
        ], Subscription::listAll()->toArray(), function ($value) {
            $this->crud->query->whereHas('subscription', function ($query) use ($value) {
                $query->where('id', $value);
            });
        });


        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'coupon',
        ], Coupon::listAll()->toArray(), function ($value) {
            $this->crud->query->whereHas('coupon', function ($query) use ($value) {
                $query->where('id', $value);
            });
        });

        $this->crud->addFilter([
            'type' => 'dropdown',
            'name' => 'active',
        ], [Member::STATUS_ACTIVE => 'Yes', Member::STATUS_INACTIVE => 'No'], function ($value) {
            $this->crud->addClause('where', 'active', $value);
        });

        $this->crud->addFilter([
            'type' => 'date_range',
            'name' => 'up',
        ], false, function ($value) {
            $dates = json_decode($value);

            $this->crud->addClause('where', 'up', '>=', $dates->from);
            $this->crud->addClause('where', 'up', '<=', $dates->to);
        });

        $this->crud->addFilter([
            'type' => 'date_range',
            'name' => 'till',
        ], false, function ($value) {
            $dates = json_decode($value);

            $this->crud->addClause('where', 'till', '>=', $dates->from);
            $this->crud->addClause('where', 'till', '<=', $dates->to);
        });

    }

    /**
     * @return void
     */
    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(MemberRequest::class);

        $this->crud->addFields([
            [
                'name' => 'first_last_name',
                'label' => 'Member name',
                'type' => 'text'
            ],
            [
                'name' => 'active',
                'type' => 'select2_from_array',
                'options' => [Member::STATUS_INACTIVE => 'No', Member::STATUS_ACTIVE => 'Yes']
            ],
            [
                'name' => 'subscription',
                'type' => 'select2'
            ],
            [
                'name' => 'coupon',
                'type' => 'select2',
                'attribute' => 'value'
            ],
            [
                'name' => 'up',
                'type' => 'datetime_picker'
            ],
            [
                'name' => 'till',
                'type' => 'datetime_picker'
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

    /**
     * @param $id
     * @return array[]|bool|string
     */
    public function destroy(int $id)
    {
        $member = $this->memberRepository->find($id);

        if ($member->not_operated) {
            return ['error' => [trans('backpack::crud.member_delete_not_allowed')]];
        }

        return $this->crud->delete($id);
    }
}
