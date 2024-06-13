<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController {
    public static function index(){
       //echo "desde api/index";
       $servicios = Servicio::all();
        echo json_encode($servicios);
       //debuguear($servicios);
    }

    public static function guardar(){
       /*
        $respuesta =[
            'datos' => $_POST
        ];
        */
        // Almacena la cita y devuelve el ID
        $cita = new Cita($_POST);       
        $resultado = $cita->guardar(); 

        $id = $resultado['id'];

        // Almacena los servicios con el ID de la cita
        $idServicios = explode(",", $_POST['servicios']);

            foreach ($idServicios as $idServicios ) {
                $args = [
                    'citaId' => $id,
                    'servicioId' => $idServicios
                ];
                $citaServicio = new CitaServicio($args);
                $citaServicio->guardar();
            }
            // Retornamos una respuesta
            /*
        $respuesta = [
            'resultado' => $resultado
        ];
        */                
        echo json_encode(['resultado' => $resultado]);
    }

    public static function eliminar(){
       // echo "Eliminado Cita....";
       // debuguear($_POST);
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id'];
            //debuguear($id);
            $cita = Cita::find($id);
           // debuguear($cita);
           $cita->eliminar();
           header('Location:' . $_SERVER['HTTP_REFERER']);
        }
    }
}


?>