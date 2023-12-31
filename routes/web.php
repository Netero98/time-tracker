<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('/projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name(RouteServiceProvider::ROUTE_PROJECTS_INDEX);
        Route::post('/', [ProjectController::class, 'store'])->name(RouteServiceProvider::ROUTE_PROJECTS_STORE);
        Route::delete('/{id}', [ProjectController::class, 'destroy'])->name(RouteServiceProvider::ROUTE_PROJECTS_DESTROY);
        Route::patch('/{id}', [ProjectController::class, 'update'])->name(RouteServiceProvider::ROUTE_PROJECTS_UPDATE);
    });

    Route::prefix('/tasks')->group(function () {
        Route::patch('/{id}', [TaskController::class, 'update'])->name(RouteServiceProvider::ROUTE_TASKS_UPDATE);
        Route::post('/{project_id}', [TaskController::class, 'store'])->name(RouteServiceProvider::ROUTE_TASKS_STORE);
        Route::delete('/{id}', [TaskController::class, 'destroy'])->name(RouteServiceProvider::ROUTE_TASKS_DELETE);
    });
});

require __DIR__.'/auth.php';
