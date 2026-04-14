<?php

namespace App\Http\Controllers\Admin;

use App\Models\Barber;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

class BarberCrudController extends CrudController
{

    use ListOperation, CreateOperation, UpdateOperation, DeleteOperation;

    /**
     * @return void
     * @throws \Exception
     */
    public function setup(): void
    {
        $this->crud->setModel(Barber::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/barber');
        $this->crud->setEntityNameStrings('barber', 'barbers');
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
                'name'      => 'barbershop',
                'type'      => 'relationship',
                'label'     => 'Barbershop',
                'attribute' => 'name',
            ],
            [
                'name'  => 'name',
                'type'  => 'text',
                'label' => 'Name',
            ],
            [
                'name'  => 'specialization',
                'type'  => 'text',
                'label' => 'Specialization',
            ],
            [
                'name'  => 'rating',
                'type'  => 'number',
                'label' => 'Rating',
            ],
            [
                'name'   => 'experience_years',
                'type'   => 'number',
                'label'  => 'Experience',
                'suffix' => ' yrs',
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
            'barbershop_id' => 'required|exists:barbershops,id',
            'name'          => 'required|string|max:255',
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
                'name'      => 'user_id',
                'type'      => 'select',
                'label'     => 'User (optional)',
                'entity'    => 'user',
                'model'     => 'App\Models\User',
                'attribute' => 'email',
                'allows_null' => true,
            ],
            [
                'name'  => 'name',
                'type'  => 'text',
                'label' => 'Name',
            ],
            [
                'name'   => 'avatar',
                'type'   => 'upload',
                'label'  => 'Avatar',
                'upload' => true,
            ],
            [
                'name'  => 'specialization',
                'type'  => 'text',
                'label' => 'Specialization',
            ],
            [
                'name'       => 'rating',
                'type'       => 'number',
                'label'      => 'Rating',
                'attributes' => ['step' => '0.1', 'min' => '0', 'max' => '5'],
                'default'    => 0,
            ],
            [
                'name'    => 'experience_years',
                'type'    => 'number',
                'label'   => 'Experience (years)',
                'default' => 0,
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
    }
}
