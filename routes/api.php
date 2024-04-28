<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;

Route::group( ['middleware' => ["auth:sanctum"]], function(){
    //rutas
    Route::get('user-profile', [UserController::class, 'userProfile']);
    Route::put('user-edit', [UserController::class, 'updateUsers']);
    Route::get('logout', [UserController::class, 'logout']);
    
    //rutas privadas para libros
    Route::get('/libros', 'App\Http\Controllers\LibroController@index');
    Route::post('/libro', 'App\Http\Controllers\LibroController@store');
    Route::put('/libro/{id}', 'App\Http\Controllers\LibroController@update');
    Route::delete('/libro/{id}', 'App\Http\Controllers\LibroController@destroy');

    // Rutas para imagenes
    // Route::get('/imagenes', 'App\Http\Controllers\ImagenFileController@index');
    //Route::get('/imagen/{id}', 'App\Http\Controllers\ImagenFileController@show');
    Route::post('/imagen', 'App\Http\Controllers\ImagenFileController@store');
    // Route::put('/imagen/{id}', 'App\Http\Controllers\ImagenFileController@update');
    Route::delete('/imagen/{id}', 'App\Http\Controllers\ImagenFileController@destroy');
    Route::get('/storage/img/{filename}', 'App\Http\Controllers\ImagenFileController@getImagen');

    //Rutas para documentos
    Route::post('/doc', 'App\Http\Controllers\DocFileController@store');
    Route::delete('/doc/{id}', 'App\Http\Controllers\DocFileController@destroy');
});

// Rutas publicas para auth
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::get('user-count', [UserController::class, 'countUsers']);
// Rutas publicas para libros 
Route::get('/libros-publicos', 'App\Http\Controllers\LibroController@index');
Route::get('/libro/{id}', 'App\Http\Controllers\LibroController@show');
Route::get('libro-principal', 'App\Http\Controllers\LibroController@showPrincipalBook');
Route::get('/download/{docFile}', 'App\Http\Controllers\DocFileController@downloadDoc');



