<?php

namespace App\Http\Controllers\Admin;

use App\Backpack\ImageUploader;
use App\Http\Requests\CreateCatalogRequest;
use App\Models\Catalog;
use App\Repositories\CategoryRepository;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\{CreateOperation,
    DeleteOperation,
    ListOperation,
    ShowOperation,
    UpdateOperation};
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CatalogCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CatalogCrudController extends CrudController
{
    use ListOperation;
    use CreateOperation;
    use UpdateOperation;
    use DeleteOperation;
    use ShowOperation;

    /**
     * @var CategoryRepository
     */
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        parent::__construct();
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function setup(): void
    {
        CRUD::setModel(Catalog::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/catalog');
        CRUD::setEntityNameStrings('продукт', 'продукты');
    }

    /**
     * @return void
     */
    protected function setupListOperation(): void
    {
        $this->setupListFilters();
        $this->crud->removeButton('show');

        $this->crud->setColumns([
            [
                'name' => 'image',
                'label' => 'Фото',
                'type' => 'image',
                'disk' => ImageUploader::STORAGE_DISK,

                'height' => 'auto',
                'width' => '50px',
                'orderable' => false,
            ],
            [
                'name' => 'name',
                'label' => 'Название',
                'type' => 'text',
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhere('name', 'like', '%' . $searchTerm . '%');
                },
            ],
            [
                'name' => 'created_at',
                'label' => 'Категории',
                'type' => 'closure',
                'function' => fn($entry) => $entry->categories->pluck('name')->join('; ')
            ],
            [
                'name' => 'price',
                'label' => 'Цена',
                'type' => 'number',

                'decimals' => 2,
                'dec_point' => '.',
                'thousands_sep' => ' ',
                'suffix' => ' грн',
            ],
            [
                'name' => 'amount',
                'label' => 'Количество',
                'type' => 'closure',
                'function' => fn($entry) => sprintf('%d %s', $entry->amount, $entry->unit->name)
            ],
            [
                'name' => 'active',
                'label' => 'Наличие',
                'type' => 'check'
            ]
        ]);
    }

    /**
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->crud->setValidation(CreateCatalogRequest::class);

        $this->crud->addFields([
            [
                'name' => 'name',
                'label' => 'Название',
                'type' => 'text',
                'tab' => trans('backpack::crud.form_tab_product')
            ],
            [
                'name' => 'description',
                'label' => 'Описание',
                'type' => 'summernote',
                'options' => config('backpack.fields.summernote.options'),
                'tab' => trans('backpack::crud.form_tab_product')
            ],
            [
                'name' => 'categories',
                'label' => 'Категории',
                'type' => 'select2_multiple',
                'attribute' => 'full_path',
                'tab' => trans('backpack::crud.form_tab_product'),
                'options' => fn($query) => $query->whereNotRoot()->defaultOrder()->get(),
            ],
            [
                'name' => 'price',
                'label' => 'Цена',
                'type' => 'number',
                'suffix' => 'грн',
                'attributes' => ['step' => 'any'],
                'tab' => trans('backpack::crud.form_tab_product')
            ],
            [
                'type' => 'number',
                'label' => 'Количество',
                'name' => 'amount',
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-8',
                ],
                'tab' => trans('backpack::crud.form_tab_product')
            ],
            [
                'type' => 'select2',
                'name' => 'unit',
                'label' => '&nbsp;',
                'allows_null' => false,
                'attributes' => [
                    'data-minimum-results-for-search' => -1
                ],
                'wrapperAttributes' => [
                    'class' => 'form-group col-md-4',
                ],
                'tab' => trans('backpack::crud.form_tab_product')
            ],
            [
                'name' => 'active',
                'label' => 'Наличие',
                'type' => 'select2_from_array',
                'options' => [
                    Catalog::STATUS_AVAILABLE => 'Есть в наличии',
                    Catalog::STATUS_UNAVAILABLE => 'Нет в наличии'
                ],
                'attributes' => [
                    'data-minimum-results-for-search' => -1
                ],
                'allows_null' => false,
                'tab' => trans('backpack::crud.form_tab_product')
            ],
            [
                'label' => 'Изображение',
                'name' => 'image',
                'type' => 'image',
                'crop' => true,
                'disk' => ImageUploader::STORAGE_DISK,
                'tab' => trans('backpack::crud.form_tab_images'),
            ],
            [
                'name' => 'extra_images',
                'label' => 'Дополнительные изображение',
                'type' => 'upload_multiple',
                'upload' => true,
                'disk' => ImageUploader::STORAGE_DISK,
                'tab' => trans('backpack::crud.form_tab_images'),
            ],
            [
                'name' => 'ingredients',
                'label' => 'Ингредиенты',
                'type' => 'summernote',
                'options' => config('backpack.fields.summernote.options'),
                'tab' => trans('backpack::crud.form_tab_ingredients')
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

    /**
     * @return void
     */
    protected function setupShowOperation(): void
    {
        $this->crud->set('show.setFromDb', false);

        $this->crud->setColumns([
            [
                'name' => 'name',
                'label' => 'Название',
                'type' => 'text',
            ],
            [
                'name' => 'created_at',
                'label' => 'Категории',
                'type' => 'closure',
                'function' => fn($entry) => $entry->categories->pluck('name')->join('; ')
            ],
            [
                'type' => 'closure',
                'label' => 'Описание',
                'function' => fn($entry) => $entry->clean_description ?: '-'
            ],
            [
                'name' => 'price',
                'label' => 'Цена',
                'type' => 'number',

                'decimals' => 2,
                'dec_point' => '.',
                'thousands_sep' => ' ',
                'suffix' => ' грн',
            ],
            [
                'name' => 'amount',
                'label' => 'Количество',
                'type' => 'closure',
                'function' => fn($entry) => sprintf('%d %s', $entry->amount, $entry->unit->name)
            ],
            [
                'name' => 'active',
                'label' => 'Наличие',
                'type' => 'radio',
                'options' => ['Нет', 'Да']
            ],
            [
                'name' => 'all_images',
                'label' => 'Изображения',
                'type' => 'catalog_images',
                'disk' => ImageUploader::STORAGE_DISK,
            ],
        ]);
    }

    /**
     * @return void
     */
    protected function setupListFilters(): void
    {
        $this->crud->addFilter([
            'label' => 'Название',
            'type' => 'text',
            'name' => 'name',
        ], false, fn($value) => $this->crud->addClause('where', 'name', 'LIKE', "%$value%"));

        $this->crud->addFilter([
            'type' => 'select2',
            'name' => 'categories',
            'label' => 'Категории',
        ], $this->categoryRepository->getFullPathArray(), function ($value) {
            return $this->crud->query->whereHas('categories', fn($query) => $query->whereIn('category_id', $this->categoryRepository->getDescendantsAndSelf($value)));
        });

        $this->crud->addFilter([
            'type' => 'range',
            'name' => 'updated_at',
            'label' => 'Цена',
            'label_from' => 'от',
            'label_to' => 'до'
        ], false, function ($value) {
            $range = json_decode($value);

            if ($range->from) {
                $this->crud->addClause('where', 'price', '>=', (float)$range->from);
            }
            if ($range->to) {
                $this->crud->addClause('where', 'price', '<=', (float)$range->to);
            }
        });

        $this->crud->addFilter([
            'type' => 'simple',
            'name' => 'active',
            'label' => 'Активен'
        ], false, fn() => $this->crud->addClause('where', 'active', '=', 1));
    }
}
