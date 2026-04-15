<?php

namespace App\Http\Controllers\Admin;

use App\Models\Review;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

class ReviewCrudController extends CrudController
{

    use ListOperation, ShowOperation, DeleteOperation;

    /**
     * @return void
     * @throws \Exception
     */
    public function setup(): void
    {
        $this->crud->setModel(Review::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/review');
        $this->crud->setEntityNameStrings('review', 'reviews');
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
                'name'      => 'user',
                'type'      => 'relationship',
                'label'     => 'User',
                'attribute' => 'email',
            ],
            [
                'name'      => 'barbershop',
                'type'      => 'relationship',
                'label'     => 'Barbershop',
                'attribute' => 'name',
            ],
            [
                'name'  => 'rating',
                'type'  => 'number',
                'label' => 'Rating',
            ],
            [
                'name'  => 'comment',
                'type'  => 'text',
                'label' => 'Comment',
                'limit' => 80,
            ],
            [
                'name'  => 'created_at',
                'type'  => 'datetime',
                'label' => 'Created',
            ],
        ]);
    }
}
