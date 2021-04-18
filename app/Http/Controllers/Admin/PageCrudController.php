<?php

namespace App\Http\Controllers\Admin;

use App\Backpack\ImageUploader;
use App\Http\Requests\CreatePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Page;
use App\Repositories\PageRepository;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PageCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PageCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ReorderOperation;

    /**
     * @var PageRepository
     */
    private PageRepository $pageRepository;

    /**
     * @param PageRepository $pageRepository
     */
    public function __construct(PageRepository $pageRepository)
    {
        parent::__construct();

        $this->pageRepository = $pageRepository;
    }

    /**
     * @return void
     */
    protected function setupReorderOperation(): void
    {
        $this->crud->set('reorder.label', 'name');
        $this->crud->set('reorder.max_level', 0);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function setup(): void
    {
        CRUD::setModel(Page::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/page');
        CRUD::setEntityNameStrings('страницу', 'страницы');
    }

    /**
     * @return void
     */
    protected function setupListOperation(): void
    {
        $this->crud->setColumns([
            [
                'name' => 'name',
                'label' => 'Название',
                'type' => 'closure',
                'function' => function ($entry) {
                    $all = $entry->ancestorsAndSelf($entry->id)->pluck('name');

                    return $all->join(' > ');
                },
                'orderable' => true,
                'orderLogic' => function ($query, $column, $columnDirection) {
                    return $query->orderBy($this->crud->model->getLftName(), $columnDirection);
                }
            ],
            [
                'name' => 'type',
                'label' => 'Тип страницы',
                'type' => 'relationship'
            ]
        ]);
    }

    /**
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->crud->setValidation(CreatePageRequest::class);

        $this->crud->addFields([
            [
                'name' => 'name',
                'label' => 'Имя страницы',
                'type' => 'text'
            ],
            [
                'name' => 'parent_id',
                'label' => 'Родитель',
                'type' => 'select2_from_array',
                'options' => $this->pageRepository->getTreeArray(),
                'attributes' => [
                    'data-minimum-results-for-search' => -1
                ]
            ],
            [
                'name' => 'type_id',
                'label' => 'Тип страницы',
                'type' => 'select2',
                'attributes' => [
                    'data-minimum-results-for-search' => -1
                ],
            ],
            [
                'name' => 'description',
                'label' => 'Текст',
                'type' => 'summernote',
                'options' => config('backpack.fields.summernote.options'),
            ],
            [
                'label' => "Изображение",
                'name' => "image",
                'type' => 'image',
                'crop' => true,
                'disk' => ImageUploader::STORAGE_DISK
            ],
            [
                'name' => 'buttons',
                'label' => 'Кнопки',
                'type' => 'repeatable',
                'init_rows' => 0,
                'new_item_label' => 'Добавить кнопку',
                'fields' => [
                    [
                        'type' => 'text',
                        'name' => 'name',
                        'label' => 'Название'
                    ],
                    [
                        'name' => ['type', 'link', 'page_id'],
                        'label' => 'Тип',
                        'type' => 'page_or_link',
                        'page_data' => $this->pageRepository->getTreeArray()
                    ],
                ]
            ],
            [
                'name' => 'buttons_per_row',
                'label' => 'Расположение кнопок',
                'type' => 'select2_from_array',
                'options' => [
                    1 => 'Один ряд',
                    2 => 'Два ряда',
                ],
                'attributes' => [
                    'data-minimum-results-for-search' => -1
                ],
                'allows_null' => false
            ]
        ]);
    }

    /**
     * @return void
     */
    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();

        $this->crud->unsetValidation();
        $this->crud->setValidation(UpdatePageRequest::class);

        $this->crud->modifyField('parent_id', [
            'attributes' => [
                'disabled' => true
            ]
        ]);
    }

    /**
     * @param int $id
     * @return array[]|bool|null
     * @throws \Exception
     */
    public function destroy(int $id)
    {
        $entry = $this->crud->getCurrentEntry() ?? $this->crud->getEntry($id);

        if ($entry->isRoot()) {
            return ['error' => [trans('validation.custom.root_delete')]];
        }

        return $entry->delete();
    }
}
