<?php

namespace Controllers;

use MVC\Router;

class citaController{
    public static function index(Router $router){

        if(!isset($_SESSION)) {
            session_start();
             }
        //debuguear($_SESSION);
        isAuth();

        $router->render('cita/index', [
            'nombre' => $_SESSION['nombre'],
            'id' => $_SESSION['id']

        ]);
    }
}


?>