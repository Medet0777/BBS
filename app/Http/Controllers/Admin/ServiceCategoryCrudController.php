<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServiceCategory;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class ServiceCategoryCrudController extends CrudController
{

    use ListOperation, CreateOperation, UpdateOperation, DeleteOperation;

    /**
     * @return void
     * @throws \Exception
     */
    public function setup(): void
    {
        $this->crud->setModel(ServiceCategory::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/service-category');
        $this->crud->setEntityNameStrings('service category', 'service categories');
    }

    /**
     * @return void
     */
    protected function setupListOperation(): void
    {
        $this->crud->addColumns([
            [
                'name'  => 'id',
                'type'  => 'number',
                'label' => 'ID',
            ],
            [
                'name'  => 'name',
                'type'  => 'text',
                'label' => 'Name',
            ],
            [
                'name'  => 'icon',
                'type'  => 'text',
                'label' => 'Icon',
            ],
            [
                'name'  => 'sort_order',
                'type'  => 'number',
                'label' => 'Sort Order',
            ],
        ]);
    }

    /**
     * @return void
     */
    protected function setupCreateOperation(): void
    {
        $this->crud->setValidation([
            'name' => 'required|string|max:255',
        ]);

        $this->crud->addFields([
            [
                'name'  => 'name',
                'type'  => 'text',
                'label' => 'Name',
            ],
            [
                'name'  => 'icon',
                'type'  => 'text',
                'label' => 'Icon',
                'hint'  => 'Icon name or emoji',
            ],
            [
                'name'    => 'sort_order',
                'type'    => 'number',
                'label'   => 'Sort Order',
                'default' => 0,
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
