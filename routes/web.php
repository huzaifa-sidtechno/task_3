<?php

use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('items.index');
});

Route::controller(ItemController::class)->group(function () {
    Route::get('items', 'index')->name('items.index');
    Route::get('items/getData', 'getData')->name('items.getData');
    Route::post('items', 'store')->name('items.store');
    Route::get('items/{item}', 'show')->name('items.show');
    Route::put('items/{item}', 'update')->name('items.update');
    Route::delete('items/{item}', 'destroy')->name('items.destroy');
});

