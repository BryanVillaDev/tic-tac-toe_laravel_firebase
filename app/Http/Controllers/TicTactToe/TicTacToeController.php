<?php

namespace App\Http\Controllers\TicTactToe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kreait\Firebase\Database;

class TicTacToeController extends Controller
{

    public function __construct(Database $database)
    {

        $this->database = $database;
    }

    public function index()
    {
        $salaID = (session()->exists('salaID')) ? session('salaID') : '---';
        $nickName = (session()->exists('nickName')) ? session('nickName') : 'Jugador 1';
        $nickName2 = (session()->exists('nickName2')) ? session('nickName2') : 'Jugador 2';
        $jugador = (session()->exists('jugador')) ? session('jugador') : 'X';
        return view('tictactoe.play.index', ['salaID' => $salaID, 'nickName' => $nickName, 'nickName2' => $nickName2, 'jugador' => $jugador]);
    }

    public function crearSala(Request $request)
    {
        if (!$this->ValidateSession()) {
            $arrayJuego = [" ", " ", " ", " ", " ", " ", " ", " ", " "];
            $nickName = (empty($request['nickName'])) ? 'Jugador 1' : $request['nickName'];
            $postData = [
                'jugadores' => 1,
                'nickName1' => $nickName,
                'jugadorActual' => 'X',
                'juegoIniciado' => false,
                'arrayJuego' => $arrayJuego,
            ];
            $salaID = $this->database->getReference('salas')->push($postData)->getKey();
            if ($salaID) {
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

        return json_encode($data);
    }

    public function unirseSala(Request $request)
    {

        $dataRemote = $this->database->getReference('salas')

            ->getChild($request['salaID'])
            ->getValue();
        if ($dataRemote) {
            if ($dataRemote['jugadores'] == 1) {
                $data['nickName2'] = (($dataRemote['nickName1'] == $request['nickName'])) ? 'Jugador 2' : $request['nickName'];
                $postData = [
                    'jugadores' => 2,
                    'nickName2' => $data['nickName2'],
                ];
                $UpdateDB = $this->database->getReference('salas/' . $request['salaID'])
                    ->update($postData);
                if ($UpdateDB) {
                    $data['message'] = "Se unio correctamente a la sala";
                    $data['jugador'] = 'O';

                    session(['salaID' => $request['salaID'], 'nickName2' => $postData['nickName2'], 'jugador' => 'O']);

                }
            } else {
                $data['message'] = "Sala con el maximo de jugadores";
            }
        } else {
            $data['message'] = "No existe sala";
        }
        return json_encode($data);
    }

    public function borrarSala(Request $request)
    {
        $request->session()->flush();

    }

    public function AlmacenarJuego(Request $request)
    {
        $VerificacionJuego = $this->VerificarJuego();
        $arrayJuego = $VerificacionJuego['arrayJuego'];
        $arrayJuego[$request['indiceCelda']] = $request['jugadorActual'];
        $postData = [
            'juegoIniciado' => true,
            'arrayJuego' => $arrayJuego,
            'jugadorActual' => $request['jugadorActual'],
        ];
        $UpdateDB = $this->database->getReference('salas/' . session('salaID'))->update($postData);
        $data['arrayJuego'] = $arrayJuego;
        return json_encode($arrayJuego);
    }

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

    private function ValidateSession()
    {
        if (session()->exists('salaID')) {
            return true;
        }
        return false;
    }

    private function VerificarGanador($arrayJuego, $JugadorActual)
    {
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
        $RondaGanada = false;
        $continua = false;
        for ($i = 0; $i <= 7; $i++) {

            $CondicionGanadora = $CondicionesGanar[$i];

            $a = $arrayJuego[$CondicionGanadora[0]];
            $b = $arrayJuego[$CondicionGanadora[1]];
            $c = $arrayJuego[$CondicionGanadora[2]];
            if ($a === ' ' || $b === ' ' || $c === ' ') {
                $continua = true;
                continue;

            }
            if ($a === $b && $b === $c) {
                return $a;
                break;
            }
        }

        if ($continua == false) {
            return 'Empate';
        }

    }

    public function resetSala(Request $request)
    {
        $data['jugadorActual'] = "X";
        $data['arrayJuego'] = $arrayJuego = [" ", " ", " ", " ", " ", " ", " ", " ", " "];
        $postData = [
            'juegoIniciado' => true,
            'arrayJuego' => $arrayJuego,
            'jugadorActual' => $data['jugadorActual'],
        ];
        $UpdateDB = $this->database->getReference('salas/' . session('salaID'))->update($postData);
    }

}
