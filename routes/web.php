<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\ClassSettingsController;
use App\Http\Controllers\Admin\FeeController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportCardController;
use App\Http\Controllers\Admin\SchoolClassController;
use App\Http\Controllers\Admin\SchoolPaymentController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentImportController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\AdminDashboard;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Proprietor\DashboardController;
use App\Http\Controllers\Proprietor\SessionTermController;
use App\Http\Controllers\Proprietor\UserManagementController;
use App\Http\Controllers\SchoolResultController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\ResultController;
use App\Http\Controllers\StaffDashboard;
use App\Http\Controllers\SuperAdmin\SchoolsController;
use App\Http\Controllers\SuperAdmin\SubscriptionController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('loginview');

Route::middleware('auth')->group(function () {
    Route::get('/school/pending', fn () => view('auth.pending'))->name('school.pending');
    Route::get('/school/banned', fn () => view('auth.banned'))->name('school.banned');
    // Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.password.update');
    Route::get('/change-password', [ProfileController::class, 'changePasswordView'])->name('change.password');
    Route::post('/change-password', [ProfileController::class, 'updatePassword'])->name('store.password');

});
Route::middleware(['auth', 'school.active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'role:super-admin'])->group(function () {
    Route::get('/super-admin/profile', [ProfileController::class, 'show'])->name('superadmin.profile.show');
    Route::put('/super-admin/profile', [ProfileController::class, 'update'])->name('superadmin.profile.update');
});



Route::middleware(['auth', 'verified','school.active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/classlist', [SchoolClassController::class, 'classlistView'])->name('class-list');
    Route::post('/classlist', [SchoolClassController::class, 'storeClass'])->name('class-store');
    Route::get('/class-fee', [SchoolClassController::class, 'classFees'])->name('class.fees');
    Route::post('/class-fee', [SchoolClassController::class, 'storeClassFee'])->name('class.fee.store');
    Route::get('assign-class', [SchoolClassController::class, 'assignClass'])->name('assign.class');
    Route::post('/school-list', [SchoolClassController::class, 'schoolStore'])->name('school-store');
    Route::get('/school-list', [SchoolClassController::class, 'schoolListView'])->name('school-list');
    Route::get('/students', [SchoolClassController::class, 'studentList'])->name('students');
    Route::post('/students', [SchoolClassController::class, 'storestudent'])->name('store-student');
    Route::get('/admin/classes-by-school/{school}', [SchoolClassController::class, 'classesBySchool'])->name('classes.by.school');
    Route::get('/makepayment', [SchoolPaymentController::class, 'create'])->name('makepayment');
    Route::post('/makepayment', [SchoolPaymentController::class, 'store'])->name('admin.payment.store');
    Route::get('/payments', [SchoolPaymentController::class, 'allPayments'])->name('admin.payment.list');
    Route::get('/complete-payments', [SchoolPaymentController::class, 'completePayments'])->name('admin.complete.payments');
    Route::get('/defaulters', [AdminDashboard::class, 'defaulters'])->name('defaulters');
    
    Route::get('/users', [AdminDashboardController::class, 'staffList'])->name('users.index');
    Route::get('/users/create', [AdminDashboardController::class, 'staffCreate'])->name('users.create');
    Route::post('/users', [AdminDashboardController::class, 'staffStore'])->name('users.store');
    Route::get('/users/{user}', [AdminDashboardController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminDashboardController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminDashboardController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/ban', [AdminDashboardController::class, 'ban'])->name('users.ban');
    Route::patch('/users/{user}/unban', [AdminDashboardController::class, 'unban'])->name('users.unban');
    Route::patch('/users/{user}/reset-password', [AdminDashboardController::class, 'resetPassword'])->name('users.reset-password');
    Route::delete('/users/{user}', [AdminDashboardController::class, 'destroy'])->name('users.destroy');

    Route::post('/test/excel', [StudentImportController::class, 'importExcel'])
        ->name('students.import.excel');

    Route::post('/students', [SchoolClassController::class, 'storestudent'])->name('store-student');
    Route::get('/classes/{school}/by-school', [StaffDashboard::class, 'classesBySchool'])->name('admin.classes.by.school');
    Route::get('/students/{class}/by-class', [StaffDashboard::class, 'studentsByClass'])->name('admin.students.by.class');
    Route::get('/subjects/create', [AdminDashboard::class, 'create'])->name('subjects.create');
    Route::post('/subjects/store', [AdminDashboard::class, 'store'])->name('subjects.store');
    Route::get('/get-classes/{schoolId}', function ($schoolId) {
        return SchoolClass::where('school_id', $schoolId)->get();
    });

});




Route::middleware(['auth','verified','school.active', 'role:admin,proprietor'])->prefix('school')->name('admin.')->group(function () {

    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::post('/classes', [ClassController::class, 'store'])->name('classes.store');
    Route::put('/classes/{class}', [ClassController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{class}', [ClassController::class, 'destroy'])->name('classes.destroy');
    Route::post('/classes/{class}/subjects', [ClassController::class, 'assignSubject'])->name('classes.subjects.assign');
    Route::delete('/classes/subjects/{assignment}', [ClassController::class, 'removeSubject'])->name('classes.subjects.remove');

    Route::get('/classes/settings', [ClassSettingsController::class, 'index'])->name('classes.settings');
    Route::post('/classes/settings/levels', [ClassSettingsController::class, 'storeLevel'])->name('classes.settings.levels.store');
    Route::put('/classes/settings/levels/{level}', [ClassSettingsController::class, 'updateLevel'])->name('classes.settings.levels.update');
    Route::delete('/classes/settings/levels/{level}', [ClassSettingsController::class, 'destroyLevel'])->name('classes.settings.levels.destroy');
    Route::post('/classes/settings/sections', [ClassSettingsController::class, 'storeSection'])->name('classes.settings.sections.store');
    Route::put('/classes/settings/sections/{section}', [ClassSettingsController::class, 'updateSection'])->name('classes.settings.sections.update');
    Route::delete('/classes/settings/sections/{section}', [ClassSettingsController::class, 'destroySection'])->name('classes.settings.sections.destroy');

    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    Route::put('/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/import', [StudentController::class, 'importForm'])->name('students.import.form');
    Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('/students/download-template', [StudentController::class, 'downloadTemplate'])->name('students.download-template');
    Route::post('/fees/types', [FeeController::class, 'storeFeeType'])->name('fees.types.store');
    Route::put('/fees/types/{feeType}', [FeeController::class, 'updateFeeType'])->name('fees.types.update');
    Route::delete('/fees/types/{feeType}', [FeeController::class, 'destroyFeeType'])->name('fees.types.destroy');

    Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
    Route::post('/fees', [FeeController::class, 'store'])->name('fees.store');
    Route::put('/fees/{fee}', [FeeController::class, 'update'])->name('fees.update');
    Route::delete('/fees/{fee}', [FeeController::class, 'destroy'])->name('fees.destroy');
    Route::get('/fees/terms-by-session/{session}', [FeeController::class, 'termsBySession'])->name('fees.terms-by-session');

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/paid-students', [PaymentController::class, 'indexPaid'])->name('paid-students.index');
    Route::get('/paid-students/terms', [PaymentController::class, 'termsBySession'])->name('paid-students.terms');
    Route::get('/paid-students/fees', [PaymentController::class, 'feesByTerm'])->name('paid-students.fees');
    Route::get('/payments/student-fees', [PaymentController::class, 'studentFees'])->name('payments.student-fees');
    Route::get('/payments/student/{student}', [PaymentController::class, 'show'])->name('payments.show');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    Route::get('/payments/defaulter', [PaymentController::class, 'indexDefaulter'])->name('payments.defaulter.index');
    Route::get('/payments/defaulter/create', [PaymentController::class, 'createDefaulter'])->name('payments.defaulter.create');
    Route::post('/payments/defaulter', [PaymentController::class, 'storeDefaulter'])->name('payments.defaulter.store');

    // AJAX
    Route::get('/payments/terms', [PaymentController::class, 'termsBySession'])->name('payments.terms');
    Route::get('/payments/defaulter/fees', [PaymentController::class, 'defaulterFeesByTerm'])->name('payments.defaulter.fees');

    // Student search (used by payment form)
    Route::get('/students/search', [StudentController::class, 'search'])->name('students.search');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::patch('/students/{student}/assign-class', [StudentController::class, 'assignClass'])->name('students.assign-class');
    Route::patch('/students/{student}/toggle-status', [StudentController::class, 'toggleStatus'])->name('students.toggle-status');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
});

Route::middleware(['auth', 'verified','school.active', 'role:staff,admin'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::get('/my-uploaded-results', [StaffDashboard::class, 'myUploadedResults'])->name('staff.my.uploaded.results');
    Route::get('/results', [SchoolResultController::class, 'index'])->name('results.index');
    Route::get('/results/upload', [SchoolResultController::class, 'uploadForm'])->name('results.upload.form');
    Route::post('/results/upload', [SchoolResultController::class, 'upload'])->name('results.upload');
    Route::get('/results/download-template', [SchoolResultController::class, 'downloadTemplate'])->name('results.download-template');
    Route::get('/results/view', [SchoolResultController::class, 'view'])->name('results.view');
    Route::get('/results/{result}/edit', [SchoolResultController::class, 'edit'])->name('results.edit');
    Route::put('/results/{result}', [SchoolResultController::class, 'update'])->name('results.update');
    Route::delete('/results/{result}', [SchoolResultController::class, 'destroy'])->name('results.destroy');
    Route::get('/export/results', [ReportCardController::class, 'index'])->name('report-cards.index');
    Route::get('/preview', [ReportCardController::class, 'preview'])->name('report-cards.preview');
    Route::get('/download', [ReportCardController::class, 'download'])->name('report-cards.download');
    Route::get('/terms', [ReportCardController::class, 'termsBySession'])->name('report-cards.terms');
});

Route::middleware(['auth', 'permission:manage schools'])->prefix('super-admin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('dashboard');
    Route::prefix('schools')->name('schools.')->group(function () {
        Route::get('/', [SchoolsController::class, 'index'])->name('index');
        Route::get('/{school}', [SchoolsController::class, 'show'])->name('show');
        Route::post('/{school}/activate', [SchoolsController::class, 'activate'])->name('activate');
        Route::post('/{school}/ban', [SchoolsController::class, 'ban'])->name('ban');
        Route::post('/{school}/reactivate', [SchoolsController::class, 'reactivate'])->name('reactivate');
    });
    Route::prefix('subscription')->name('subscriptions.')->group(function(){
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::get('/create', [SubscriptionController::class, 'create'])->name('create');
        Route::post('/', [SubscriptionController::class, 'store'])->name('store');
        Route::get('/school-info', [SubscriptionController::class, 'schoolInfo'])->name('school-info');
        Route::get('/{school}', [SubscriptionController::class, 'show'])->name('show');
    });

});

Route::middleware(['auth', 'verified', 'school.active', 'role:proprietor'])->prefix('proprietor')->name('proprietor.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/school-profile', [ProfileController::class, 'showSchool'])->name('school-profile.show');
    Route::put('/school-profile', [ProfileController::class, 'updateSchool'])->name('school-profile.update');
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/ban', [UserManagementController::class, 'ban'])->name('users.ban');
    Route::patch('/users/{user}/unban', [UserManagementController::class, 'unban'])->name('users.unban');
    Route::patch('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::get('/sessions', [SessionTermController::class, 'index'])->name('sessions.index');
    Route::post('/sessions', [SessionTermController::class, 'storeSession'])->name('sessions.store');
    Route::put('/sessions/{session}', [SessionTermController::class, 'updateSession'])->name('sessions.update');
    Route::patch('/sessions/{session}/set-current', [SessionTermController::class, 'setCurrentSession'])->name('sessions.set-current');
    Route::delete('/sessions/{session}', [SessionTermController::class, 'destroySession'])->name('sessions.destroy');

    Route::post('/sessions/{session}/terms', [SessionTermController::class, 'storeTerm'])->name('sessions.terms.store');
    Route::put('/terms/{term}', [SessionTermController::class, 'updateTerm'])->name('terms.update');
    Route::patch('/terms/{term}/set-current', [SessionTermController::class, 'setCurrentTerm'])->name('terms.set-current');
    Route::delete('/terms/{term}', [SessionTermController::class, 'destroyTerm'])->name('terms.destroy');

});

Route::fallback(function () {
    return view('page404');
})->name('404');

require __DIR__.'/auth.php';
