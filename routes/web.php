<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes(['register' => false, 'reset' => false]);

Route::get('admin/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
Route::post('admin/alterar-senha', [App\Http\Controllers\SettingsController::class, 'edit'])->name('alterar-senha');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/clientes', [App\Http\Controllers\ClientesController::class, 'index'])->name('clientes');
Route::match(['get', 'post'],'/alterar-clientes', [App\Http\Controllers\ClientesController::class, 'alterar'])->name('alterar-clientes');
Route::match(['get', 'post'],'/incluir-clientes', [App\Http\Controllers\ClientesController::class, 'incluir'])->name('incluir-clientes');

Route::get('/pedidos', [App\Http\Controllers\PedidosController::class, 'index'])->name('pedidos');
Route::match(['get', 'post'],'/alterar-pedidos', [App\Http\Controllers\PedidosController::class, 'alterar'])->name('alterar-pedidos');
Route::match(['get', 'post'],'/incluir-pedidos', [App\Http\Controllers\PedidosController::class, 'incluir'])->name('incluir-pedidos');
Route::match(['post'],'/pedidos/observacoes', [App\Http\Controllers\PedidosController::class, 'observacoes'])->name('observacoes');

Route::match(['get', 'post'],'/acompanhamento_pedidos', [App\Http\Controllers\PedidosController::class, 'acompanhamentoPedidos'])->name('acompanhamento_pedidos');

Route::match(['post'],'/desvincular-funcionario', [App\Http\Controllers\PedidosController::class, 'desvincularFuncionario'])->name('desvincular-funcionario');
Route::match(['post'],'/incluir-funcionario', [App\Http\Controllers\PedidosController::class, 'incluirFuncionario'])->name('incluir-funcionario');
Route::match(['post'],'/finalizar_etapa', [App\Http\Controllers\PedidosController::class, 'FinalizaEtapa'])->name('finalizar_etapa');

Route::get('/financeiro', [App\Http\Controllers\FinanceiroController::class, 'index'])->name('financeiro');
Route::match(['get', 'post'],'/alterar-financeiro', [App\Http\Controllers\FinanceiroController::class, 'alterar'])->name('alterar-financeiro');
Route::match(['get', 'post'],'/incluir-financeiro', [App\Http\Controllers\FinanceiroController::class, 'incluir'])->name('incluir-financeiro');

Route::match(['get', 'post'],'/status', [App\Http\Controllers\StatusController::class, 'index'])->name('status');
Route::match(['get', 'post'],'/alterar-status', [App\Http\Controllers\StatusController::class, 'alterar'])->name('alterar-status');
Route::match(['get', 'post'],'/incluir-status', [App\Http\Controllers\StatusController::class, 'incluir'])->name('incluir-status');

Route::get('/users', [App\Http\Controllers\UsersController::class, 'index'])->name('users')->can('perfil_admin');
Route::match(['get', 'post'],'/alterar-users', [App\Http\Controllers\UsersController::class, 'alterar'])->name('alterar-users');
Route::match(['get', 'post'],'/incluir-users', [App\Http\Controllers\UsersController::class, 'incluir'])->name('incluir-users');
