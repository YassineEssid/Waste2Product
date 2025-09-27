<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\WasteItemController;
use App\Http\Controllers\RepairRequestController;
use App\Http\Controllers\TransformationController;
use App\Http\Controllers\CommunityEventController;
use App\Http\Controllers\MarketplaceItemController;
use App\Http\Controllers\DashboardController;

// Home page - Smart redirection
Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public routes (no authentication required)
Route::get('/waste-items', [WasteItemController::class, 'index'])->name('waste-items.index');
Route::get('/waste-items/{wasteItem}', [WasteItemController::class, 'show'])->name('waste-items.show');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Waste Items (authenticated actions only)
    Route::get('/waste-items/create', [WasteItemController::class, 'create'])->name('waste-items.create');
    Route::post('/waste-items', [WasteItemController::class, 'store'])->name('waste-items.store');
    Route::get('/waste-items/{wasteItem}/edit', [WasteItemController::class, 'edit'])->name('waste-items.edit');
    Route::put('/waste-items/{wasteItem}', [WasteItemController::class, 'update'])->name('waste-items.update');
    Route::delete('/waste-items/{wasteItem}', [WasteItemController::class, 'destroy'])->name('waste-items.destroy');
    Route::get('/my-items', [WasteItemController::class, 'my'])->name('waste-items.my');
    Route::patch('/waste-items/{wasteItem}/claim', [WasteItemController::class, 'claim'])->name('waste-items.claim');
    Route::patch('/waste-items/{wasteItem}/toggle-availability', [WasteItemController::class, 'toggleAvailability'])->name('waste-items.toggle-availability');

    // Repair Requests
    Route::resource('repairs', RepairRequestController::class);
    Route::post('/repairs/{repair}/assign', [RepairRequestController::class, 'assign'])->name('repairs.assign');
    Route::post('/repairs/{repair}/start', [RepairRequestController::class, 'start'])->name('repairs.start');
    Route::post('/repairs/{repair}/complete', [RepairRequestController::class, 'complete'])->name('repairs.complete');

    // Transformations
    Route::resource('transformations', TransformationController::class);
    Route::post('/transformations/{transformation}/publish', [TransformationController::class, 'publish'])->name('transformations.publish');

    // Community Events
    Route::resource('events', CommunityEventController::class);
    Route::post('/events/{event}/register', [CommunityEventController::class, 'register'])->name('events.register');
    Route::delete('/events/{event}/unregister', [CommunityEventController::class, 'unregister'])->name('events.unregister');

    // Marketplace create (public, en dehors du groupe auth)
Route::get('/marketplace/create', [MarketplaceItemController::class, 'create'])->name('marketplace.create');

    // Marketplace (protected)
    Route::resource('marketplace', MarketplaceItemController::class)->except(['create']);
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/statistics', [App\Http\Controllers\Admin\StatisticsController::class, 'index'])->name('statistics');
});


