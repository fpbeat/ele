<?php

namespace App\Http\Controllers\Admin;

use App\Backpack\ImageUploader;
use App\Http\Requests\CatalogRequest;
use App\Http\Requests\CreateCatalogRequest;
use App\Http\Requests\CreateCategoryRequest;
use App\Models\Catalog;
use App\Models\UnitTypes;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
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

//    use ShowOperation;

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
        $this->crud->setColumns([
            [
                'name' => 'image',
                'label' => 'Фото',
                'type' => 'image',
                'disk' => 'public',

                'height' => 'auto',
                'width' => '50px',
            ],
            [
                'name' => 'name',
                'label' => 'Название',
                'type' => 'text'
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
                'name' => 'active',
                'label' => 'Активен',
                'type' => 'check'
            ],
            [
                'name' => 'amount',
                'label' => 'Количество',
                'type' => 'closure',
                'function' => function ($entry) {
                    return $entry->amount . ' ' . $entry->unit->name;
                }
            ],
        ]);
    }

    /**
     * @return void
     */
    protected function setupCreateOperation()
    {

        $this->crud->setValidation(CreateCatalogRequest::class);

        CRUD::addColumn('name');

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
                'label' => 'Photos',
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
}
