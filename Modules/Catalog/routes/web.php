<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalog\Http\Controllers\CatalogController;

Route::resource('catalogs', CatalogController::class)->names('catalog');
