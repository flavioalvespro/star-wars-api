<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\StatisticsController;

Route::prefix('v1')->group(function () {
    //people routes
    Route::get('/people/search', [PeopleController::class, 'search'])->name('people.search');
    Route::get('/people/{id}', [PeopleController::class, 'show'])->name('people.show');

    //film routes
    Route::get('/films/search', [FilmController::class, 'search'])->name('films.search');
    Route::get('/films/{id}', [FilmController::class, 'show'])->name('films.show');

    //statistic routes
    Route::get('/statistics', [StatisticsController::class, 'index']);
});
