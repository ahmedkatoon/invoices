<?php

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InvoiceAttachmentsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SectionController;
use Illuminate\Support\Facades\Route;

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

// Route::get('index', function () {
//     return view('home');
// });

Route::get("/", [LoginController::class, "login"]);
Route::resource('/invoices', InvoiceController::class);
Route::resource("/sections", SectionController::class);
Route::resource("/products", ProductController::class);
Route::resource("InvoiceAttachments", InvoiceAttachmentsController::class);

Route::get('/section/{id}', [InvoiceController::class, 'getProducts']);
Route::get("InvoicesDetails/{id}", [InvoicesDetailsController::class, "edit"]);
Route::get("View_file/{invoice_number}/{file_name}", [InvoicesDetailsController::class, "open_file"]);
Route::get("download/{invoice_number}/{file_name}", [InvoicesDetailsController::class, "get_file"]);
Route::post("delete_file", [InvoicesDetailsController::class, "destroy"])->name("delete_file");
Route::get("edit_invoice/{id}", [InvoiceController::class, "edit"]);
Route::post('/invoices/update', [InvoiceController::class, "update"]);
Route::get("Status_show/{id}", [InvoiceController::class, "show"])->name("Status_show");
Route::post("Status_Update/{id}", [InvoiceController::class, "Status_Update"])->name("Status_Update");

Route::get("Invoice_Paid",[InvoiceController::class,"Invoice_Paid"]);
Route::get("Invoice_UnPaid",[InvoiceController::class,"Invoice_UnPaid"]);
Route::get("Invoice_Partial",[InvoiceController::class,"Invoice_Partial"]);
Route::resource("Archive",ArchiveController::class);
Route::get("Print_invoice/{id}",[InvoiceController::class,"Print_invoice"]);





Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
