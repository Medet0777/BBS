<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class ServiceCrudController extends CrudController
{

    use ListOperation, CreateOperation, UpdateOperation, DeleteOperation;

    /**
     * @return void
     * @throws \Exception
     */
    public function setup(): void
    {
        $this->crud->setModel(Service::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/service');
        $this->crud->setEntityNameStrings('service', 'services');
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
                'name'     => 'barbershop',
                'type'     => 'relationship',
                'label'    => 'Barbershop',
                'attribute' => 'name',
            ],
            [
                'name'     => 'serviceCategory',
                'type'     => 'relationship',
                'label'    => 'Category',
                'attribute' => 'name',
            ],
            [
                'name'   => 'price',
                'type'   => 'number',
                'label'  => 'Price',
                'suffix' => ' ₸',
            ],
            [
                'name'   => 'duration_minutes',
                'type'   => 'number',
                'label'  => 'Duration',
                'suffix' => ' min',
            ],
        ]);
    }

    /**
     * @return void
     */
    protected function setupCreateOperation(): void
    {
        $this->crud->setValidation([
            'name'                => 'required|string|max:255',
            'barbershop_id'       => 'required|exists:barbershops,id',
            'service_category_id' => 'required|exists:service_categories,id',
            'price'               => 'required|numeric|min:0',
        ]);

        $this->crud->addFields([
            [
                'name'      => 'barbershop_id',
                'type'      => 'select',
                'label'     => 'Barbershop',
                'entity'    => 'barbershop',
                'model'     => 'App\Models\Barbershop',
                'attribute' => 'name',
            ],
            [
                'name'      => 'service_category_id',
                'type'      => 'select',
                'label'     => 'Category',
                'entity'    => 'serviceCategory',
                'model'     => 'App\Models\ServiceCategory',
                'attribute' => 'name',
            ],
            [
                'name'  => 'name',
                'type'  => 'text',
                'label' => 'Name',
            ],
            [
                'name'  => 'price',
                'type'  => 'number',
                'label' => 'Price (₸)',
                'attributes' => ['step' => '0.01'],
            ],
            [
                'name'  => 'duration_minutes',
                'type'  => 'number',
                'label' => 'Duration (minutes)',
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
