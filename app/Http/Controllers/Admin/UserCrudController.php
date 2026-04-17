<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

class UserCrudController extends CrudController
{

    use ListOperation, ShowOperation, DeleteOperation;

    /**
     * @return void
     * @throws \Exception
     */
    public function setup(): void
    {
        $this->crud->setModel(User::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/user');
        $this->crud->setEntityNameStrings('user', 'users');
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
                'name'  => 'email',
                'type'  => 'email',
                'label' => 'Email',
            ],
            [
                'name'  => 'email_verified_at',
                'type'  => 'datetime',
                'label' => 'Verified At',
            ],
            [
                'name'  => 'created_at',
                'type'  => 'datetime',
                'label' => 'Registered',
            ],
        ]);
    }

    /**
     * @return void
     */
    protected function setupShowOperation(): void
    {
        $this->setupListOperation();
    }
}
