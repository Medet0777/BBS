<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'BarberHub API',
    version: '1.0.0',
    description: 'API for BarberHub barbershop booking system',
    contact: new OA\Contact(email: 'support@barberhub.com')
)]
#[OA\Server(
    url: '/api/v1',
    description: 'API V1'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum'
)]
abstract class Controller
{
    //
}
