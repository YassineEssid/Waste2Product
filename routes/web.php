<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WasteItemController;
use App\Http\Controllers\RepairRequestController;
use App\Http\Controllers\TransformationController;
use App\Http\Controllers\CommunityEventController;
use App\Http\Controllers\EventCommentController;
use App\Http\Controllers\MarketplaceItemController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\HomeController;

// Front Office Routes
Route::get('/', [HomeController::class, 'index'])->name('front.home');
Route::get('/a-propos', [HomeController::class, 'about'])->name('front.about');
Route::get('/comment-ca-marche', [HomeController::class, 'howItWorks'])->name('front.how-it-works');
Route::get('/contact', [HomeController::class, 'contact'])->name('front.contact');
Route::get('/evenements', [HomeController::class, 'events'])->name('front.events');
Route::get('/boutique', [HomeController::class, 'marketplace'])->name('front.marketplace');

// AJAX Search Routes for Front Office
Route::get('/api/search/events', [HomeController::class, 'searchEvents'])->name('api.search.events');
Route::get('/api/search/marketplace', [HomeController::class, 'searchMarketplace'])->name('api.search.marketplace');

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


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
    Route::get('/my-repairs', [RepairRequestController::class, 'my'])->name('repairs.my');
    Route::post('/repairs/{repair}/complete', [RepairRequestController::class, 'complete'])->name('repairs.complete');

    // Transformations
    Route::resource('transformations', TransformationController::class);
    Route::post('/transformations/{transformation}/publish', [TransformationController::class, 'publish'])->name('transformations.publish');

    // Community Events
    // AI Generation for Events (must be before resource routes to avoid conflicts)
    Route::post('/events/ai/generate-description', [CommunityEventController::class, 'generateDescription'])->name('events.ai.generate-description');
    Route::post('/events/ai/generate-faq', [CommunityEventController::class, 'generateFAQ'])->name('events.ai.generate-faq');

    Route::resource('events', CommunityEventController::class);
    Route::post('/events/{event}/register', [CommunityEventController::class, 'register'])->name('events.register');
    Route::delete('/events/{event}/unregister', [CommunityEventController::class, 'unregister'])->name('events.unregister');

    // Event Comments
    Route::resource('event-comments', EventCommentController::class);
    Route::post('/event-comments/{eventComment}/toggle-approval', [EventCommentController::class, 'toggleApproval'])->name('event-comments.toggle-approval');

    // Gamification Routes
    Route::prefix('gamification')->name('gamification.')->group(function () {
        Route::get('/profile', [App\Http\Controllers\GamificationController::class, 'profile'])->name('profile');
        Route::get('/activity', [App\Http\Controllers\GamificationController::class, 'activity'])->name('activity');
        Route::get('/achievements', [App\Http\Controllers\GamificationController::class, 'achievements'])->name('achievements');
        Route::get('/points-info', [App\Http\Controllers\GamificationController::class, 'pointsInfo'])->name('points-info');
    });

    // Badges Routes
    Route::prefix('badges')->name('badges.')->group(function () {
        Route::get('/', [App\Http\Controllers\BadgeController::class, 'index'])->name('index');
        Route::get('/collection', [App\Http\Controllers\BadgeController::class, 'collection'])->name('collection');
        Route::get('/{badge}', [App\Http\Controllers\BadgeController::class, 'show'])->name('show');
        Route::post('/{badge}/toggle-display', [App\Http\Controllers\BadgeController::class, 'toggleDisplay'])->name('toggle-display');
    });

    // Leaderboard Routes
    Route::prefix('leaderboard')->name('leaderboard.')->group(function () {
        Route::get('/', [App\Http\Controllers\LeaderboardController::class, 'index'])->name('index');
        Route::get('/user/{user}', [App\Http\Controllers\LeaderboardController::class, 'userProfile'])->name('user-profile');
    });

    // Marketplace create (public, en dehors du groupe auth)
Route::get('/marketplace/create', [MarketplaceItemController::class, 'create'])->name('marketplace.create');

    // Marketplace (protected)
    Route::resource('marketplace', MarketplaceItemController::class)->except(['create']);

    // Marketplace AI - Category Detection & Price Suggestion
    Route::post('/marketplace/ai/detect-category', [MarketplaceItemController::class, 'detectCategory'])
        ->name('marketplace.ai.detect-category');
    Route::post('/marketplace/ai/suggest-price', [MarketplaceItemController::class, 'suggestPrice'])
        ->name('marketplace.ai.suggest-price');

    // Marketplace Toggle Status (Mark as Sold)
    Route::post('/marketplace/{marketplace}/toggle-status', [MarketplaceItemController::class, 'toggleStatus'])
        ->name('marketplace.toggle-status');

    // Marketplace Search (AJAX)
    Route::get('/marketplace/search', [MarketplaceItemController::class, 'search'])
        ->name('marketplace.search');

    // Messaging
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/recent', [MessageController::class, 'recentConversations'])->name('messages.recent');
    Route::get('/messages/unread/count', [MessageController::class, 'unreadCount'])->name('messages.unread.count');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::get('/messages/{conversation}/poll', [MessageController::class, 'poll'])->name('messages.poll');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/marketplace/{item}/contact', [MarketplaceItemController::class, 'startConversation'])->name('marketplace.contact');
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

// Public routes (no authentication required)
Route::get('/waste-items', [WasteItemController::class, 'index'])->name('waste-items.index');
Route::get('/waste-items/{wasteItem}', [WasteItemController::class, 'show'])->name('waste-items.show');