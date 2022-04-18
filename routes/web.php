<?php

use App\Http\Controllers\TicTactToe\TicTacToeController;
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

Route::get('play', [TicTacToeController::class,'index']);

Route::post('play/crear-sala', [TicTacToeController::class,'crearSala']);

Route::post('play/borrar-sala', [TicTacToeController::class,'borrarSala']);

Route::post('play/unirse-sala', [TicTacToeController::class,'unirseSala']);

Route::post('play/almacenar-juego', [TicTacToeController::class,'AlmacenarJuego']);

Route::post('play/verificar-juego', [TicTacToeController::class,'VerificarJuego']);


Route::post('play/reset-sala', [TicTacToeController::class,'resetSala']);



Route::get('/', function () {
    return view('tictactoe.play.index');
});
