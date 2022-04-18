// ejecutamos la funcion de verificar
VerificarJuego();



// creamos la funcion de verificación
function VerificarJuego() {

    // creamos evento ajax para ir al controlador
    $.ajax({
        url: "play/verificar-juego",
        method: "POST",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        cache: false,
        dataType: 'json',
        success: function(data) {
            // tomamos valores retornados por el controlador y hacemos uso de ellos
            // verificamos estado del juego
            if (data.juegoIniciado) {
                //creamos la constante del arreglo alamcenado en la base de datos
                const juegoActual = data.arrayJuego;
                for (var i in juegoActual) {
                    //recorremos el arreglo y dibujamos las jugadas
                    $('#celda' + i).text(juegoActual[i])
                }
                //dibujamos el juagador actual
                $("#jugadorActual").val(data.jugadorActual);
            } else {
                $("#jugadorActual").val("X");

            }
            //seteamos unos valores en inputs que usaremos como variables
            $("#juegoActivo").val(data.juegoActivo);
            $("#juegoActual").val(data.arrayJuego.toString());
            $(".text-jugador").removeClass('hidden')
            $("#jugador").html(data.jugador);
            //Validamos el ganador
            if (data.juegoActivo === false) {
                $('.estado-juego').html("El ganador de la ronda es " + data.Ganador);
                // alert("El ganador de la ronda es " + data.Ganador);
            } else {
                $('.estado-juego').html('Es el turno de: ' + data.jugadorActual);
            }


        }
    });
}
// variables que usaremos más adelante
let juegoActivo = $("#juegoActivo").val();;
let jugadorActual = $("#jugadorActual").val();
// creamos un arreglo para almacenar el juego
let jugador = $("#jugador").text();;


// detenemos el cron de verificar
DetenerCron();
if (jugador !== jugadorActual) {
    // si el jugador es diferente a mi ejecutamos el cron
    IniciarCron();
}

// creamos funciones tipo variable  para utilizar más adelante
const MensajeGanador = () => `Jugador ${jugadorActual} ha ganado!`;
const empateMensaje = () => `El juego terminó en empate.!`;




// creamos el evento para validar y jugar
$(".celda-juego").on('click', function() {
    // leemos el indice de la celda que enviamos a tráves de un data
    const indiceCelda = parseInt($(this).data('celda-index'));
    let juegoActivo = $("#juegoActivo").val();
    let juegoActual = $("#juegoActual").val();
    let jugadorActual = $("#jugadorActual").val();
    let juegoActualArr = juegoActual.split(',');
    let jugador = $("#jugador").text();
    DetenerCron();

    if (jugador !== jugadorActual) {
        alert('Espere al otro jugador');
        IniciarCron();
        return
    }

    //Validamos que la celda este vacia
    if (juegoActualArr[indiceCelda] !== " " || juegoActivo == 'false') {
        // en caso de que la celda este jugada no hacemos nada
        return;
    }
    // asignamos la celda jugada en el arreglo
    // dibujamos la celda jugada

    // validamos si seguimos jugando o si tenemos empate o ganador
    AlmacenarJuego(jugadorActual, indiceCelda);
    $(this).html(jugadorActual);
});




$("#BtnCrearSala").on('click', function() {
    crearSala()
});


function crearSala() {
    var jugadorActual = "X";
    // creamos un arreglo para almacenar el juego
    var nickName = $('#nickName').val();
    var DatosPeticion = {
        'jugadorActual': jugadorActual,
        'nickName': nickName
    };
    $.ajax({
        url: "play/crear-sala",
        method: "POST",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: DatosPeticion,
        cache: false,
        dataType: 'json',
        success: function(data) {
            $("#CodigoJuego").html(data.salaID);
            $(".text-jugador").removeClass('hidden')
            $("#jugador").html(data.jugador);
            resetJuego()
            alert(data.message);
        }
    });
}

function resetJuego() {

    $('.celda-juego').html("");
    VerificarJuego();
}

$("#BtnResetSala").on('click', function() {
    if (confirm('Desea iniciar una nueva partida')) {
        $.ajax({
            url: "play/reset-sala",
            method: "POST",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            cache: false,
            success: function(data) {
                resetJuego()
                alert('Juego reiniciado');

            }
        });
    } else {
        alert('Seguimos jugando');
    }

});

$("#BtnBorrarSala").on('click', function() {

    if (confirm('Desea borrar la sala compartida')) {
        $.ajax({
            url: "play/borrar-sala",
            method: "POST",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            cache: false,
            success: function(data) {
                alert('Sala borrada');
                location.reload(true);
            }
        });
    } else {
        alert('Seguimos jugando');
    }
});


$("#BtnUnirseSala").on('click', function() {
    var salaID = $('#salaID').val();
    var nickName = $('#nickName').val();
    if (salaID == '') {
        alert('Para unirse debe ingresar un código ');
        return;
    } else {
        if (nickName == '') {
            alert('Para unirse ingrese su nick name ');
            return;
        }
    }
    var DatosPeticion = {
        'salaID': salaID
    };
    $.ajax({
        url: "play/unirse-sala",
        method: "POST",
        data: DatosPeticion,
        dataType: 'json',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        cache: false,
        success: function(data) {

            $('#nickName').val(data.nickName2);
            $("#CodigoJuego").html(data.salaID);
            $(".text-jugador").removeClass('hidden')
            $("#jugador").html(data.jugador);
        }
    });

});


function AlmacenarJuego(jugadorActual, indiceCelda) {


    var DatosPeticion = {
        'jugadorActual': jugadorActual,
        'indiceCelda': indiceCelda
    };
    $.ajax({
        url: "play/almacenar-juego",
        method: "POST",
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: DatosPeticion,
        cache: false,
        dataType: 'json',
        success: function(data) {

            IniciarCron()

        }
    });

}





var CronJob;

function DetenerCron() {
    clearInterval(CronJob);
}

function IniciarCron() {
    CronJob = setInterval(
        function() {
            VerificarJuego();
        }, 1000);

}