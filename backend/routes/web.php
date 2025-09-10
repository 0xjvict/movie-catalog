<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json('Bem-vindo à API de MovieCatalog!');
});
