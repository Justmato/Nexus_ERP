<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Modern ERP API',
    description: 'API REST para ERP de inventario, ventas y administración empresarial'
)]
#[OA\Server(url: 'http://localhost:8000', description: 'Desarrollo local')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
#[OA\SecurityScheme(securityScheme: 'bearerAuth')]
class OpenApiSpec {}
