<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AgentController;

Route::get('/agent/context', [AgentController::class, 'context']);
