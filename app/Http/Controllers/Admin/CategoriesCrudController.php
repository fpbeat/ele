<?php

namespace App\Http\Controllers\Admin;

use App\Backpack\ImageUploader;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repositories\CategoryRepository;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\{CreateOperation, DeleteOperation, ListOperation, ReorderOperation, UpdateOperation};
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CategoriesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CategoriesCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ReorderOperation;

    /**
     * @var CategoryRepository
     */
    private CategoryRepository $categoryRepository;

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        parent::__construct();

        $this->categoryRepository = $categoryRepository;
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
        CRUD::setModel(\App\Models\Category::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/categories');
        CRUD::setEntityNameStrings('категорию', 'категории');
    }

    /**
     * @return void
     */
    protected function setupListOperation(): void
    {
        $this->crud->setDefaultPageLength(-1);

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
                },
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhere('name', 'like', '%' . $searchTerm . '%');
                },
            ]
        ]);
    }

    /**
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->crud->setValidation(CreateCategoryRequest::class);

        $this->crud->addFields([
            [
                'name' => 'name',
                'label' => 'Имя категории',
                'type' => 'text'
            ],
            [
                'name' => 'parent_id',
                'label' => 'Родитель',
                'type' => 'select2_from_array',
                'options' => $this->categoryRepository->getTreeArray()
            ],
            [
                'name' => 'description',
                'label' => 'Текст',
                'type' => 'summernote',
                'options' => config('backpack.fields.summernote.options')
            ],
            [
                'label' => "Изображение",
                'name' => "image",
                'type' => 'image',
                'crop' => true,
                'disk' => ImageUploader::STORAGE_DISK,
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
        $this->crud->setValidation(UpdateCategoryRequest::class);

        $this->crud->modifyField('parent_id', [
            'attributes' => [
                'disabled' => true
            ]
        ]);
    }

    /**
     * @param int $id
     * @return bool|null
     */
    public function destroy(int $id)
    {
        $entry = $this->crud->getCurrentEntry() ?? $this->crud->getEntry($id);

        return !$entry->isRoot() ? $entry->delete() : false;
    }
}
