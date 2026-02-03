<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrudTableController;

Route::get('/', function () {
    $crudTables = \App\Models\CrudTable::withCount('columns')->get();

    return view('welcome', compact('crudTables'));
});

Route::get('/crud-tables', [CrudTableController::class, 'index'])
    ->name('crud_tables.index');

Route::get('/crud-tables/create', [CrudTableController::class, 'create'])
    ->name('crud_tables.create');

Route::post('/crud-tables', [CrudTableController::class, 'store'])
    ->name('crud_tables.store');

Route::get('/crud-tables/{crudTable}/edit', [CrudTableController::class, 'edit'])
    ->name('crud_tables.edit');

Route::put('/crud-tables/{crudTable}', [CrudTableController::class, 'update'])
    ->name('crud_tables.update');

// Rotas para criar e listar registros em uma tabela dinâmica
Route::get('/crud/{slug}', [CrudTableController::class, 'listRecords'])
    ->name('crud_records.index');

Route::get('/crud/{slug}/create', [CrudTableController::class, 'createRecord'])
    ->name('crud_records.create');

Route::post('/crud/{slug}', [CrudTableController::class, 'storeRecord'])
    ->name('crud_records.store');
