<?php

use App\Http\Controllers\Api\AgentApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/v1/agents', [AgentApiController::class, 'index']);
