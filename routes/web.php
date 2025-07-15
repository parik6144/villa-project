<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\VerifyIsAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PropertyHtmlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;

Route::get('/test-verify', function () {
    return auth()->user()->hasVerifiedEmail()
        ? 'Verified'
        : 'Not verified';
})->middleware('auth');

Route::get('/email/verify', EmailVerificationPromptController::class)
    ->middleware(['auth'])
    ->name('verification.notice.email');

Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify.email');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('auth', 'verified')->prefix('backend')->group(function () {

    Route::get('/backend', [HomeController::class, 'backend'])->name('backend');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    Route::middleware(['web'])
        ->group(function () {
            Route::get('/backend/roles', function () {
                abort(404);
            });
        });

    Route::get('/generate-property-html/{id}', [PropertyHtmlController::class, 'generateHtml']);
    Route::get('/generate-property-html-real-estate/{id}', [PropertyHtmlController::class, 'generateHtmlRealEstate']);

    Route::post('/save-input', function (Request $request) {
        // Check is ID exists
        sleep(3);
        if (!$request->has('propertyID')) {
            return response()->json(['error' => 'Missing property ID'], 400);
        }
        $url = DB::table('properties')
            ->where('id', $request->propertyID)
            ->value('url_for_site_presentation');

        if ($url) {
            return response()->json(['url' => $url]);
        } else {
            return response()->json(['error' => 'Failed to get property'], 500);
        }
    });
});

require __DIR__ . '/auth.php';
 