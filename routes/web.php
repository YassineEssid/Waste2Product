<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;
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

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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
    // Admin Dashboard
    Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dashboard');
    
    // User Management - Full CRUD
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
    
    // User Actions
    Route::post('/users/{user}/toggle-role', [App\Http\Controllers\Admin\UserController::class, 'toggleRole'])->name('users.toggle-role');
    Route::post('/users/bulk-delete', [App\Http\Controllers\Admin\UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::get('/users/export/csv', [App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export');
    
    // Statistics
    Route::get('/statistics', [App\Http\Controllers\Admin\StatisticsController::class, 'index'])->name('statistics');
    
    // Admin Profile Management
    Route::get('/profile', [App\Http\Controllers\Admin\AdminProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\Admin\AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Admin\AdminProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\Admin\AdminProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile/avatar', [App\Http\Controllers\Admin\AdminProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
});


