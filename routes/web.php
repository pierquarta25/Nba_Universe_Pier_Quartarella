<?php


// FILE: routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NBAController;

Route::get('/',              [NBAController::class, 'home'])->name('home');
Route::get('/giocatori',     [NBAController::class, 'giocatori'])->name('giocatori');
Route::get('/giocatori/{id}',[NBAController::class, 'show'])->name('giocatori.show')->whereNumber('id');
Route::get('/partite',       [NBAController::class, 'partite'])->name('partite');
Route::get('/blog',          [NBAController::class, 'blog'])->name('blog');
Route::post('/contatti',     [NBAController::class, 'storeMail'])->name('contatti');