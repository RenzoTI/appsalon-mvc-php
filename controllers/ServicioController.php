<?php

namespace Controllers;

use MVC\Router;
use Model\Servicio;

class ServicioController{
    public static function index(Router $router ){
       // echo "Desde de Servicio";
       if(!isset($_SESSION)) {
        session_start();
         }
        isAdmin();
         $servicios = Servicio::all();



       $router->render('servicios/index', [
            'nombre' => $_SESSION['nombre'],
            'servicios' => $servicios
       ]);
    }

    public static function crear(Router $router ){
       // echo "Desde Crear";
        if(!isset($_SESSION)) {
            session_start();
             }
             isAdmin();
        $servicio = new Servicio;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $servicio->sincronizar($_POST);

            $alertas = $servicio->validar();

            if(empty($alertas)){
                $servicio->guardar();
                header('Location: /servicios');
            }
        }

        $router->render('servicios/crear', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas
       ]);
    }

    public static function actualizar(Router $router ){
       // echo "Desde Actualizar";
        if(!isset($_SESSION)) {
            session_start();
             }
             isAdmin();
        if(!is_numeric($_GET['id'])) return;
        $servicio = Servicio::find($_GET['id']);
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $servicio->sincronizar($_POST);

            $alertas = $servicio->validar();

            if(empty($alertas)){
                $servicio->guardar();
                header('Location: /servicios');
            }
        }

        $router->render('servicios/actualizar', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas
       ]);
    }

    public static function eliminar( ){
       // echo "Desde Eliminar";
       if(!isset($_SESSION)) {
        session_start();
         }
       isAdmin();
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id'];
            $servicio = Servicio::find($id);
            $servicio->eliminar();
            header('Location: /servicios');
        }
    }
}



?>