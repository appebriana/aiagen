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
use App\Http\Controllers\ReportController;
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
        Route::get('/knowledge/{knowledgeFile}/download', [KnowledgeController::class, 'download'])->name('knowledge.download');
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
    
    // Laporan Section for Admin
    Route::prefix('laporan')->name('laporan.')->group(function () {
        // Redirect /interaksi to /interaksi/wa for backward compatibility
        Route::get('/interaksi', function () {
            return redirect()->route('admin.laporan.interaksi.wa');
        })->name('interaksi');

        // WhatsApp Reports
        Route::get('/interaksi/wa', [ReportController::class, 'interaction'])->name('interaksi.wa');
        Route::get('/interaksi/wa/detail/{phone}', [ReportController::class, 'interactionDetail'])->name('interaksi.wa.detail');
        Route::get('/interaksi/wa/export/excel', [ReportController::class, 'exportExcel'])->name('interaksi.wa.export.excel');
        Route::get('/interaksi/wa/export/pdf', [ReportController::class, 'exportPdf'])->name('interaksi.wa.export.pdf');

        // Coming Soon Platforms
        Route::get('/interaksi/ig', [ReportController::class, 'comingSoon'])->name('interaksi.ig');
        Route::get('/interaksi/telegram', [ReportController::class, 'comingSoon'])->name('interaksi.telegram');
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
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

    // Pengaturan Section
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('/', function () {
            return view('pengguna.pengaturan.index');
        })->name('index');
    });

    // AI Agen Section
    Route::prefix('ai-agen')->name('ai-agen.')->group(function () {
        Route::get('/', [DashboardController::class, 'aiAgenIndex'])->name('index');

        Route::get('/knowledge', [KnowledgeController::class, 'index'])->name('knowledge.index');
        Route::post('/knowledge', [KnowledgeController::class, 'store'])->name('knowledge.store');
        Route::get('/knowledge/{knowledgeFile}/download', [KnowledgeController::class, 'download'])->name('knowledge.download');
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

    // Laporan Section
    Route::prefix('laporan')->name('laporan.')->group(function () {
        // Redirect /interaksi to /interaksi/wa for backward compatibility
        Route::get('/interaksi', function () {
            return redirect()->route('pengguna.laporan.interaksi.wa');
        })->name('interaksi');

        // WhatsApp Reports
        Route::get('/interaksi/wa', [ReportController::class, 'interaction'])->name('interaksi.wa');
        Route::get('/interaksi/wa/detail/{phone}', [ReportController::class, 'interactionDetail'])->name('interaksi.wa.detail');
        Route::get('/interaksi/wa/export/excel', [ReportController::class, 'exportExcel'])->name('interaksi.wa.export.excel');
        Route::get('/interaksi/wa/export/pdf', [ReportController::class, 'exportPdf'])->name('interaksi.wa.export.pdf');

        // Coming Soon Platforms
        Route::get('/interaksi/ig', [ReportController::class, 'comingSoon'])->name('interaksi.ig');
        Route::get('/interaksi/telegram', [ReportController::class, 'comingSoon'])->name('interaksi.telegram');
    });
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
