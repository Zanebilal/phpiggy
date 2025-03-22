<?php 
declare(strict_types=1);

namespace App\config;

use Framework\App;
use App\Controllers\{HomeController,AboutController, AuthController, TransactionController,ReceiptController,ErrorController};
use App\Middleware\{AuthRequireedMiddleware, GestOnlyMiddleware};

function registerRoutes(App $app){
    $app->get('/',[HomeController::class,'home'])->add(AuthRequireedMiddleware::class);
    $app->get('/about',[AboutController::class,'about']);
    $app->get('/register',[AuthController::class,'registerView'])->add(GestOnlyMiddleware::class);
    $app->post('/register',[AuthController::class,'register'])->add(GestOnlyMiddleware::class);
    $app->get('/login',[AuthController::class,'loginView'])->add(GestOnlyMiddleware::class);
    $app->post('/login',[AuthController::class,'login'])->add(GestOnlyMiddleware::class);
    $app->get('/logout',[AuthController::class,'logout'])->add(AuthRequireedMiddleware::class);
    $app->get('/transactions',[TransactionController::class,'createView'])->add(AuthRequireedMiddleware::class);
    $app->post('/transactions',[TransactionController::class,'create'])->add(AuthRequireedMiddleware::class);
    $app->get('/transaction/{transaction}',[TransactionController::class,'editView'])->add(AuthRequireedMiddleware::class);
    $app->post('/transaction/{transaction}',[TransactionController::class,'edit'])->add(AuthRequireedMiddleware::class);
    $app->delete('/transaction/{transaction}',[TransactionController::class,'delete'])->add(AuthRequireedMiddleware::class);
    $app->get('/transaction/{transaction}/receipt',[ReceiptController::class,'uploadView'])->add(AuthRequireedMiddleware::class);
    $app->post('/transaction/{transaction}/receipt',[ReceiptController::class,'upload'])->add(AuthRequireedMiddleware::class);
    $app->get('/transaction/{transaction}/receipt/{receipt}',[ReceiptController::class,'download'])->add(AuthRequireedMiddleware::class);
    $app->delete('/transaction/{transaction}/receipt/{receipt}',[ReceiptController::class,'delete'])->add(AuthRequireedMiddleware::class);
    
    $app->setErrorHander([ErrorController::class, 'notFound']);
}