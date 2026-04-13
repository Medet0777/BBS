<?php

namespace App\Http\Controllers\Admin;

use App\Models\Barbershop;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class BarbershopCrudController extends CrudController
{

    use ListOperation, CreateOperation, UpdateOperation, DeleteOperation, ShowOperation;

    /**
     * @return void
     * @throws \Exception
     */
    public function setup(): void
    {
        $this->crud->setModel(Barbershop::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/barbershop');
        $this->crud->setEntityNameStrings('barbershop', 'barbershops');
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
                'name'  => 'phone',
                'type'  => 'text',
                'label' => 'Phone',
            ],
            [
                'name'  => 'address',
                'type'  => 'text',
                'label' => 'Address',
            ],
            [
                'name'  => 'rating',
                'type'  => 'number',
                'label' => 'Rating',
            ],
            [
                'name'  => 'is_active',
                'type'  => 'boolean',
                'label' => 'Active',
            ],
        ]);
    }

    /**
     * @return void
     */
    protected function setupCreateOperation(): void
    {
        $this->crud->setValidation([
            'name'      => 'required|string|max:255',
            'slug'      => 'required|string|max:255|unique:barbershops,slug',
            'phone'     => 'required|string|max:20',
            'address'   => 'required|string|max:255',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'opens_at'  => 'required|string',
            'closes_at' => 'required|string',
        ]);

        $this->crud->addFields([
            [
                'name'  => 'name',
                'type'  => 'text',
                'label' => 'Name',
            ],
            [
                'name'  => 'slug',
                'type'  => 'text',
                'label' => 'Slug',
                'hint'  => 'URL-friendly name (e.g. barbershop-kz)',
            ],
            [
                'name'  => 'description',
                'type'  => 'textarea',
                'label' => 'Description',
            ],
            [
                'name'   => 'logo',
                'type'   => 'upload',
                'label'  => 'Logo',
                'upload' => true,
            ],
            [
                'name'  => 'phone',
                'type'  => 'text',
                'label' => 'Phone',
            ],
            [
                'name'  => 'address',
                'type'  => 'text',
                'label' => 'Address',
            ],
            [
                'name'       => 'latitude',
                'type'       => 'number',
                'label'      => 'Latitude',
                'attributes' => ['step' => '0.0000001'],
            ],
            [
                'name'       => 'longitude',
                'type'       => 'number',
                'label'      => 'Longitude',
                'attributes' => ['step' => '0.0000001'],
            ],
            [
                'name'  => 'opens_at',
                'type'  => 'time',
                'label' => 'Opens At',
            ],
            [
                'name'  => 'closes_at',
                'type'  => 'time',
                'label' => 'Closes At',
            ],
            [
                'name'    => 'is_active',
                'type'    => 'checkbox',
                'label'   => 'Active',
                'default' => true,
            ],
        ]);
    }

    /**
     * @return void
     */
    protected function setupUpdateOperation(): void
    {
        $this->setupCreateOperation();

        $this->crud->setValidation([
            'slug' => 'required|string|max:255|unique:barbershops,slug,' . $this->crud->getCurrentEntryId(),
        ]);
    }
}
