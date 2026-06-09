 <?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ErrorLogController;

// Error logging endpoint (public, for client-side errors)
Route::post('/error-log', [ErrorLogController::class, 'logError']);

// Protected error statistics endpoint (for admin)
Route::middleware(['auth.api'])->group(function () {
    Route::get('/error-log/statistics', [ErrorLogController::class, 'getStatistics']);
});