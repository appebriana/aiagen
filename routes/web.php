<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\ManageUserController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\UnansweredQuestionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/design-system', function () {
    return view('design-system');
});

// Live validation API routes
Route::post('/api/check-username', [ValidationController::class, 'checkUsername'])->name('api.check-username');
Route::post('/api/check-email', [ValidationController::class, 'checkEmail'])->name('api.check-email');

/*
|--------------------------------------------------------------------------
| Admin Routes (prefix: /admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

    // Pengaturan Section
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('/', function () {
            return view('admin.pengaturan.index');
        })->name('index');
    });

    // AI Agen Section
    Route::prefix('ai-agen')->name('ai-agen.')->group(function () {
        Route::get('/', function () {
            return view('admin.ai-agen.index');
        })->name('index');
        
        Route::get('/knowledge', [KnowledgeController::class, 'index'])->name('knowledge.index');
        Route::post('/knowledge', [KnowledgeController::class, 'store'])->name('knowledge.store');
        Route::delete('/knowledge/{knowledgeFile}', [KnowledgeController::class, 'destroy'])->name('knowledge.destroy');

        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

        Route::get('/connections', [ConnectionController::class, 'index'])->name('connections.index');
        Route::post('/connections/whatsapp', [ConnectionController::class, 'storeWhatsapp'])->name('connections.whatsapp.store');
        Route::get('/connections/whatsapp/{device}/status', [ConnectionController::class, 'getWhatsappStatus'])->name('connections.whatsapp.status');
        Route::delete('/connections/whatsapp/{whatsappDevice}', [ConnectionController::class, 'destroyWhatsapp'])->name('connections.whatsapp.destroy');
        Route::post('/connections/whatsapp/{device}/disconnect', [ConnectionController::class, 'disconnectWhatsapp'])->name('connections.whatsapp.disconnect');
        Route::post('/connections/whatsapp/{device}/init', [ConnectionController::class, 'initWhatsapp'])->name('connections.whatsapp.init');

        Route::get('/unanswered', [UnansweredQuestionController::class, 'index'])->name('unanswered.index');
        Route::put('/unanswered/{unansweredQuestion}', [UnansweredQuestionController::class, 'update'])->name('unanswered.update');
        Route::delete('/unanswered/{unansweredQuestion}', [UnansweredQuestionController::class, 'destroy'])->name('unanswered.destroy');
        Route::post('/unanswered/bulk-delete', [UnansweredQuestionController::class, 'bulkDelete'])->name('unanswered.bulk-delete');
        Route::get('/unanswered/export-pdf', [UnansweredQuestionController::class, 'exportPdf'])->name('unanswered.export-pdf');
        Route::post('/customers/toggle-mute', [CustomerController::class, 'toggleMute'])->name('customers.toggle-mute');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('users', ManageUserController::class)->names([
        'index' => 'users.index',
        'create' => 'users.create',
        'store' => 'users.store',
        'edit' => 'users.edit',
        'update' => 'users.update',
        'destroy' => 'users.destroy',
    ]);
});

/*
|--------------------------------------------------------------------------
| Pengguna Routes (prefix: /pengguna)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:pengguna'])->prefix('pengguna')->name('pengguna.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'penggunaDashboard'])->name('dashboard');

    // Pengaturan Section
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('/', function () {
            return view('pengguna.pengaturan.index');
        })->name('index');
    });

    // AI Agen Section
    Route::prefix('ai-agen')->name('ai-agen.')->group(function () {
        Route::get('/', function () {
            return view('pengguna.ai-agen.index');
        })->name('index');

        Route::get('/knowledge', [KnowledgeController::class, 'index'])->name('knowledge.index');
        Route::post('/knowledge', [KnowledgeController::class, 'store'])->name('knowledge.store');
        Route::delete('/knowledge/{knowledgeFile}', [KnowledgeController::class, 'destroy'])->name('knowledge.destroy');

        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

        Route::get('/connections', [ConnectionController::class, 'index'])->name('connections.index');
        Route::post('/connections/whatsapp', [ConnectionController::class, 'storeWhatsapp'])->name('connections.whatsapp.store');
        Route::get('/connections/whatsapp/{device}/status', [ConnectionController::class, 'getWhatsappStatus'])->name('connections.whatsapp.status');
        Route::delete('/connections/whatsapp/{whatsappDevice}', [ConnectionController::class, 'destroyWhatsapp'])->name('connections.whatsapp.destroy');
        Route::post('/connections/whatsapp/{device}/disconnect', [ConnectionController::class, 'disconnectWhatsapp'])->name('connections.whatsapp.disconnect');
        Route::post('/connections/whatsapp/{device}/init', [ConnectionController::class, 'initWhatsapp'])->name('connections.whatsapp.init');

        Route::get('/unanswered', [UnansweredQuestionController::class, 'index'])->name('unanswered.index');
        Route::put('/unanswered/{unansweredQuestion}', [UnansweredQuestionController::class, 'update'])->name('unanswered.update');
        Route::delete('/unanswered/{unansweredQuestion}', [UnansweredQuestionController::class, 'destroy'])->name('unanswered.destroy');
        Route::post('/unanswered/bulk-delete', [UnansweredQuestionController::class, 'bulkDelete'])->name('unanswered.bulk-delete');
        Route::get('/unanswered/export-pdf', [UnansweredQuestionController::class, 'exportPdf'])->name('unanswered.export-pdf');
        Route::post('/customers/toggle-mute', [CustomerController::class, 'toggleMute'])->name('customers.toggle-mute');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Legacy /dashboard redirect (for backward compatibility)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('pengguna.dashboard');
    }
    return redirect()->route('login');
})->middleware('auth')->name('dashboard');

require __DIR__.'/auth.php';
