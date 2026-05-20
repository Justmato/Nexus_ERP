<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => 'Modern ERP API',
        'version' => '1.0.0',
        'docs' => url('/api/documentation'),
    ]);
});
