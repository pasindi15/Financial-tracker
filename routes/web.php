<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', fn() => view('login'));
Route::get('/', fn() => redirect('/dashboard'));
Route::get('/dashboard', fn() => view('dashboard'));
Route::get('/transactions', fn() => view('transactions'));
Route::get('/categories', fn() => view('categories'));
Route::get('/budgets', fn() => view('budgets'));
Route::get('/reports', fn() => view('reports'));
