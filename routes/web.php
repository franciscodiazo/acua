<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\ReadingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\PriceSettingController;
use App\Http\Controllers\CompanyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/reportes/diario', [DashboardController::class, 'reporteDiario'])->name('reportes.diario');
Route::get('/reportes/fechas', [DashboardController::class, 'reporteFechas'])->name('reportes.fechas');
Route::get('/reportes/cierre-anual', [DashboardController::class, 'cierreAnual'])->name('reportes.cierre-anual');

// Suscriptores
Route::get('subscribers/{subscriber}/estado-cuenta', [SubscriberController::class, 'estadoCuenta'])->name('subscribers.estado-cuenta');
Route::resource('subscribers', SubscriberController::class);

// Lecturas
Route::get('readings/calcular', [ReadingController::class, 'calcular'])->name('readings.calcular');
Route::resource('readings', ReadingController::class);

// Facturas
Route::post('invoices/facturar', [InvoiceController::class, 'facturarLecturas'])->name('invoices.facturar');
Route::post('invoices/facturar-seleccionadas', [InvoiceController::class, 'facturarSeleccionadas'])->name('invoices.facturar.seleccionadas');
Route::post('invoices/facturar/{reading}', [InvoiceController::class, 'facturarIndividual'])->name('invoices.facturar.individual');
Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
Route::post('invoices/{invoice}/anular', [InvoiceController::class, 'anular'])->name('invoices.anular');
Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);

// Pagos
Route::get('payments/{payment}/print', [PaymentController::class, 'print'])->name('payments.print');
Route::post('payments/{payment}/anular', [PaymentController::class, 'anular'])->name('payments.anular');
Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'show']);

// Créditos
Route::post('credits/{credit}/anular', [CreditController::class, 'anular'])->name('credits.anular');
Route::resource('credits', CreditController::class)->only(['index', 'create', 'store', 'show']);

// Configuración de Precios
Route::prefix('settings')->name('settings.')->group(function () {
    Route::resource('prices', PriceSettingController::class)->except(['show']);
    Route::get('company', [CompanyController::class, 'edit'])->name('company.edit');
    Route::put('company', [CompanyController::class, 'update'])->name('company.update');
    Route::get('company/delete-logo', [CompanyController::class, 'deleteLogo'])->name('company.delete-logo');
});
