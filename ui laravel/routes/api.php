 <?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppController;

Route::post('send-wa', [WhatsAppController::class, 'send']); 
