<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MessageRequest;
use Backpack\CRUD\app\{Http\Controllers\CrudController,
    Http\Controllers\Operations\ListOperation,
    Http\Controllers\Operations\UpdateOperation,
    Library\CrudPanel\CrudPanelFacade as CRUD
};
use Illuminate\Support\Str;

/**
 * Class MessageCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MessageCrudController extends CrudController
{
    use ListOperation;
    use UpdateOperation;

    /**
     * @var int
     */
    const MESSAGE_WRAP_LENGTH = 85;

    /**
     * @return void
     * @throws \Exception
     */
    public function setup(): void
    {
        CRUD::setModel(\App\Models\Message::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/message');
        CRUD::setEntityNameStrings('текст', 'тексты');
    }

    /**
     * @return void
     */
    protected function setupListOperation(): void
    {
        $this->crud->setDefaultPageLength(-1);

        $this->crud->setColumns([
            [
                'name' => 'key',
                'label' => 'Ключ',
                'type' => 'string',
                'priority' => 10,
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhere('key', 'like', '%' . $searchTerm . '%');
                },
            ],
            [
                'name' => 'message',
                'type' => 'closure',
                'label' => 'Сообщение',
                'priority' => 0,
                'function' => function ($entry) {
                    return nl2br(Str::wordwrap($entry->clean_message, self::MESSAGE_WRAP_LENGTH));
                },
                'searchLogic' => function ($query, $column, $searchTerm) {
                    $query->orWhere('message', 'like', '%' . $searchTerm . '%');
                },
            ]
        ]);
    }

    /**
     * @return void
     */
    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(MessageRequest::class);

        $this->crud->addFields([
            [
                'name' => 'key',
                'type' => 'text',
                'label' => 'Ключ',
                'attributes' => [
                    'disabled' => 'disabled'
                ]
            ],
            [
                'name' => 'message',
                'label' => 'Сообщение',
                'type' => 'summernote',
                'options' => config('backpack.fields.summernote.options'),
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
