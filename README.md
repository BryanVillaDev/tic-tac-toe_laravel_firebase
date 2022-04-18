

## INSTRUCCIONES PARA QUE FUNCIONES TIC TAC TOE BRYAN VILLALOBOS

El juego esta desarrollado en Laravel con jquery, donde laravel se esta usando como servidor backend:
## REQUERIMIENTOS:
- PHP ^7.4
- LARAVEL 6
- **[Firebase](https://firebase.google.com/?hl=es-419&gclid=Cj0KCQjwmPSSBhCNARIsAH3cYgbKVgrktGcaD6fjyOLBYK_57i61q077wf0uAZ7UyoBIXWSInwwDHE0aAuhHEALw_wcB&gclsrc=aw.ds)**
- **[SDK Firebase Admin SDK for PHP](https://firebase-php.readthedocs.io/en/stable/realtime-database.html#queries)**
- **[Bootstrap](https://getbootstrap.com/docs/5.1/components/navbar/)**
- **[Jquery](https://jquery.com/)**


## Paso a paso

1  clonar el repositorio 
- git clone https://github.com/BryanVillaDev/tictactoe.git
2 BASE DE DATOS
- crear base de datos en firebase
- despues de creada la base hacer lo siguiente
- 1 ![image](https://user-images.githubusercontent.com/80850130/163855754-7b9aac7a-0b6a-4e5f-b4ed-340ffb57e7fc.png)
- 2 ![image](https://user-images.githubusercontent.com/80850130/163855857-20e47b1f-c31c-4881-a0f7-3677d0a7ffc1.png)
- descargar el archivo json de credenciales y guardarlo en la carpeta raiz
3- asosicar definiciones en .env 
- FIREBASE_CREDENTIALS="nombre del archivo descargado"
- FIREBASE_DATABASE_URL="url de la base creada"
 
 -por ultimo ejecutar php artisan serve en la ubicacion del directorio clonado de github
 
 
 -la construcción del juego quedó en un 90% dado que solo tuve el día de hoy para diseñarlo.
 
## para jugar 

- en una ventana dirigirse a localhost:8000
- dar clik en crear sala
- compartir el codigo de la sala con el otro jugador
- en una ventana de incognito dirigirse a localhost:8000
- dar clik en unirse a sala y pegar el código de la sala
y ya se puede jugar
