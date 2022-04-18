@extends('tictactoe.app')

@section('content')
    <div class="container">
        <main class="mx-auto ">
            <div class="row">
                <div class="col-md-7">

                    <section id="TableroDIV" class="">

                        <div class="card-header bg-dark text-white">
                            <h4 class="text-center"><span class="estado-juego text-danger"></span></h4>
                        </div>
                        <div class="tablero-juego pb-4">
                            <div id="celda0" data-celda-index="0" class="celda-juego"></div>
                            <div id="celda1" data-celda-index="1" class="celda-juego"></div>
                            <div id="celda2" data-celda-index="2" class="celda-juego"></div>
                            <div id="celda3" data-celda-index="3" class="celda-juego"></div>
                            <div id="celda4" data-celda-index="4" class="celda-juego"></div>
                            <div id="celda5" data-celda-index="5" class="celda-juego"></div>
                            <div id="celda6" data-celda-index="6" class="celda-juego"></div>
                            <div id="celda7" data-celda-index="7" class="celda-juego"></div>
                            <div id="celda8" data-celda-index="8" class="celda-juego"></div>
                        </div>

                        <table class="text-center table mt-2">

                            <h3 class="text-center">Juegos ganados</h3>
                            <thead>
                                <tr>
                                    <th>{{ $nickName }}</th>
                                    <th>{{ $nickName2 }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td id="WinsJugador1"></td>
                                    <td id="WinsJugador2"></td>
                                </tr>
                            </tbody>
                        </table>
                    </section>



                </div>


                {{-- Student app form --}}
                <div class="col-md-5">
                    <div class="card">
                        <input text="text" id="jugadorActual" value="X">
                        <input text="text" id="juegoActual" value="NA">
                        <input text="text" id="juegoActivo" value="true">
                        <div class="card-header bg-dark text-white">
                            <h4>MULTIJUGADOR</h4>
                        </div>
                        <div class="card-body">
                            <label for="formGroupExampleInput" class="form-label">CÓDIGO PARA MULTIJUGADOR</label>
                            <h3 class="text-success" id="CodigoJuego">{{ $salaID }}</h3>
                            <form action="#" method="post" id="createStudentForm" autocomplete="off">
                                <div class="mb-3">
                                    <label for="formGroupExampleInput" class="form-label">Nick name</label>
                                    <input type="text" class="form-control" value="{{ $nickName }}" id="nickName"
                                        placeholder="Ingrese el nombre">
                                </div>
                                <div class="mb-3">
                                    <label for="formGroupExampleInput2" class="form-label"><small>Ingrese Código para
                                            conectarse a una partida</small></label>
                                    <input type="text" class="form-control" id="salaID" placeholder="Ingrese Código">
                                </div>
                            </form>
                        </div>
                        <div class="card-footer">
                            <button id="BtnResetSala" class="btn btn-warning">Nuevo juego</button>
                            <button id="BtnUnirseSala" class="btn btn-info">Unirse a sala</button>
                            <button id="BtnCrearSala" class="btn btn-success">Crear sala</button>
                            <button id="BtnBorrarSala" class="btn btn-danger">Borrar sala</button>
                        </div>

                        <h3 align="center" class="hidden text-success text-jugador">TÚ ICONO ES <b><span
                                    id="jugador">{{ $jugador }}</span></b></h3>
                    </div>
                </div>

            </div>
        </main>
    </div>
@endsection
@push('script')
    <script>
        setInterval(
            function() {
                $("meta[name='csrf-token']").attr("content", {{ csrf_token() }});
            }, 1000);
    </script>
@endpush
