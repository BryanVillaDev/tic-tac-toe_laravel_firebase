<?php
/*
|--------------------------------------------------------------------------
|Controlador
|--------------------------------------------------------------------------
|
cree un controlador basico para administrar todas las peticiones desde la interfaz hacia la base de datos
|
 */

//creamos un nombre para exportar hacia las rutas
namespace App\Http\Controllers\TicTactToe;

//traemos el controlador http
use App\Http\Controllers\Controller;
//traemos la clase request para detectar los elementos post
use Illuminate\Http\Request;
//traemos el sdk de firebase recomendado por google para laravel https://firebase-php.readthedocs.io/en/stable/realtime-database.html
use Kreait\Firebase\Database;

// creamos nustra clase TicTacToeController  la cual tendrá todos los metodos necesarios
class TicTacToeController extends Controller
{
    //creamos un constructor para nuestra base de datos realtime
    public function __construct(Database $database)
    {
        //le damos un valor estandar nuestra variable $database para ccederlo en cualquier metodo
        $this->database = $database;
    }
    //metodo index o principal para mostrar la interfaz y formularios
    public function index()
    {
        $salaID = (session()->exists('salaID')) ? session('salaID') : '---';
        $nickName = (session()->exists('nickName')) ? session('nickName') : 'Jugador 1';
        $nickName2 = (session()->exists('nickName2')) ? session('nickName2') : 'Jugador 2';
        $jugador = (session()->exists('jugador')) ? session('jugador') : 'X';
        return view('tictactoe.play.index', ['salaID' => $salaID, 'nickName' => $nickName, 'nickName2' => $nickName2, 'jugador' => $jugador]);
    }
    //metodo crear sala para crear la sala del juego
    public function crearSala(Request $request)
    {
        //validamos la sesión que no este creada para proceder a crear una nueva
        if (!$this->ValidateSession()) {
            $arrayJuego = [" ", " ", " ", " ", " ", " ", " ", " ", " "];
            $nickName = (empty($request['nickName'])) ? 'Jugador 1' : $request['nickName'];
            //creamos el arreglo que enviaremos a firebase
            $postData = [
                'jugadores' => 1,
                'nickName1' => $nickName,
                'jugadorActual' => 'X',
                'juegoIniciado' => false,
                'arrayJuego' => $arrayJuego,
            ];
            //ejecutamos el arreglo
            $salaID = $this->database->getReference('salas')->push($postData)->getKey();
            //Validamos que se haya creado el id correctamente en firebase
            if ($salaID) {
                //creamos la sesion en firebase
                session(['salaID' => $salaID, 'nickName' => $postData['nickName1'], 'jugador' => 'X']);
                $data['salaID'] = $salaID;
                $data['nickName'] = $postData['nickName1'];
                $data['jugador'] = 'X';
                $data['message'] = "Sala creada correctamente comparta este código para unir a un jugador";
            } else {
                $data['message'] = "Error creando sala";
            }
        } else {
            $data['salaID'] = session('salaID');
            $data['nickName'] = session('nickName');
            $data['message'] = "Ya tiene una sala creada";
        }
        //retornamos algunos datos a atravez de json que usaremos en la interface
        return json_encode($data);
    }
    // creamos metodo unirseSala para unir a los usuarios que cuenten con un codigo de sala
    public function unirseSala(Request $request)
    {
        //buscamos la sala con el codigo
        $dataRemote = $this->database->getReference('salas')

            ->getChild($request['salaID'])
            ->getValue();
        //validamos que exista la sala
        if ($dataRemote) {
            //si la sala existe validamos la cantidad de jugadores en la sala
            if ($dataRemote['jugadores'] == 1) {
                $data['nickName2'] = (($dataRemote['nickName1'] == $request['nickName'])) ? 'Jugador 2' : 'Jugador 2';
                $postData = [
                    'jugadores' => 2,
                    'nickName2' => $data['nickName2'],
                ];
                //si la sala cuenta con un solo jugador actualizamos la sala con el otro jugador
                $UpdateDB = $this->database->getReference('salas/' . $request['salaID'])
                    ->update($postData);
                if ($UpdateDB) {
                    $data['message'] = "Se unio correctamente a la sala";
                    $data['jugador'] = 'O';
                    //creamnos la session con los dos jugadores
                    session(['salaID' => $request['salaID'], 'nickName2' => $postData['nickName2'], 'jugador' => 'O']);

                }
            } else {
                $data['message'] = "Sala con el maximo de jugadores";
            }
        } else {
            $data['message'] = "No existe sala";
        }
        //retornamos a travez de json algunos datos que necesitamos en la interfaz
        return json_encode($data);
    }
    //metodo para borrar sala
    public function borrarSala(Request $request)
    {
        //borramos la sesion de la sala
        $request->session()->flush();

    }
    // creamos alamcenar sala con el cual guardaremos las celdas clikadas por cada usuario
    public function AlmacenarJuego(Request $request)
    {
        // verificamos el juego actual
        $VerificacionJuego = $this->VerificarJuego();

        $arrayJuego = $VerificacionJuego['arrayJuego'];
        // almacenamos  el juego actual en el juego guardado posteriormente
        $arrayJuego[$request['indiceCelda']] = $request['jugadorActual'];
        $postData = [
            'juegoIniciado' => true,
            'arrayJuego' => $arrayJuego,
            'jugadorActual' => $request['jugadorActual'],
        ];
        // actualizamos en firebase el juego actual
        $UpdateDB = $this->database->getReference('salas/' . session('salaID'))->update($postData);
        $data['arrayJuego'] = $arrayJuego;
        //retornamos a travez de json algunos datos que necesitamos en la interfaz
        return json_encode($arrayJuego);
    }

    // metodo para retornar el juego almacenado
    public function VerificarJuego()
    {
        $dataRemote = $this->database->getReference('salas')

            ->getChild(session('salaID'))
            ->getValue();
        if ($dataRemote) {
            $data['juegoIniciado'] = $dataRemote['juegoIniciado'];
            if ($dataRemote['juegoIniciado'] == true) {
                $data['jugadorActual'] = $dataRemote['jugadorActual'];
                $data['arrayJuego'] = $dataRemote['arrayJuego'];

            } else {
                $data['jugadorActual'] = "X";
                $data['arrayJuego'] = $arrayJuego = [" ", " ", " ", " ", " ", " ", " ", " ", " "];
            }
            $data['Ganador'] = $this->VerificarGanador($data['arrayJuego'], $data['jugadorActual']);
            $data['jugador'] = session('jugador');
            $data['juegoActivo'] = (empty($data['Ganador'])) ? true : false;
            $data['jugadorActual'] = ($data['jugadorActual'] === "X") ? "O" : "X";
        }
        return $data;
    }
    // metodo para validar la session
    private function ValidateSession()
    {
        if (session()->exists('salaID')) {
            return true;
        }
        return false;
    }
    // metodo para verificar ganador
    private function VerificarGanador($arrayJuego, $JugadorActual)
    {
        //posibles jugadas ganadoras almcenadas en un array
        $CondicionesGanar = array(
            array(0, 1, 2),
            array(3, 4, 5),
            array(6, 7, 8),
            array(0, 3, 6),
            array(1, 4, 7),
            array(2, 5, 8),
            array(0, 4, 8),
            array(2, 4, 6),
        );
        //variables de control
        $RondaGanada = false;
        $continua = false;
        //ciclo de validaciond el arreglo almacenado en firebase
        for ($i = 0; $i <= 7; $i++) {
            //alamacenamos las condiciones
            $CondicionGanadora = $CondicionesGanar[$i];
            //creamos variables para comparar el posible ganador
            $a = $arrayJuego[$CondicionGanadora[0]];
            $b = $arrayJuego[$CondicionGanadora[1]];
            $c = $arrayJuego[$CondicionGanadora[2]];
            //verificamos que no exista juego disponible
            if ($a === ' ' || $b === ' ' || $c === ' ') {
                $continua = true;
            }
            //verificamos posibles ganadores
            if ($a === $b && $b === $c) {
                //retornamos ganador
                return $a;
                break;
            }
        }

        if ($continua == false) {
            //retornamos empate en caso de que no exista ganador
            return 'Empate';
        }

    }
    // metodo resetear sala
    public function resetSala(Request $request)
    {
        $data['jugadorActual'] = "X";
        $data['arrayJuego'] = $arrayJuego = [" ", " ", " ", " ", " ", " ", " ", " ", " "];
        $postData = [
            'juegoIniciado' => true,
            'arrayJuego' => $arrayJuego,
            'jugadorActual' => $data['jugadorActual'],
        ];
        //actualizamos la sala con las variables vacias en firebase
        $UpdateDB = $this->database->getReference('salas/' . session('salaID'))->update($postData);
    }

}
