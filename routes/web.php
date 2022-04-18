<?php

use App\Http\Controllers\TicTactToe\TicTacToeController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
|creamos las rutas que vamos a ejecutar atravez del jquery ajax
|
*/
// ruta para mostrar el formulario
Route::get('play', [TicTacToeController::class,'index']);
// ruta para crear la sala 
Route::post('play/crear-sala', [TicTacToeController::class,'crearSala']);
// ruta para borrar la sala 
Route::post('play/borrar-sala', [TicTacToeController::class,'borrarSala']);
// ruta para unirse a la sala 
Route::post('play/unirse-sala', [TicTacToeController::class,'unirseSala']);
// ruta para almacenar juego en firebase
Route::post('play/almacenar-juego', [TicTacToeController::class,'AlmacenarJuego']);
// ruta para  verificar juego
Route::post('play/verificar-juego', [TicTacToeController::class,'VerificarJuego']);
// ruta para  reset juego
Route::post('play/reset-sala', [TicTacToeController::class,'resetSala']);
// ruta defecto desde el index para redirigir a donde esta la aplicación
Route::get('/', function () {
    return redirect('/play');
});
