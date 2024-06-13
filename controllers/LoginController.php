<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router){
       // echo "Desde Login";
       $alertas = [];

       if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);

          $alertas = $auth->validarLogin();

            //debuguear($auth);
          
          if(empty($alertas)){
            //echo "Usuario agrego tanto emial como password";
            // Comprobar que existe el usuario
            $usuario = Usuario::where('email', $auth->email);
            //debuguear($usuario);

            if($usuario){
                // verificar el password y que este confirmado
                $resultado = $usuario->comprobarPasswordAndVerificado($auth->password);
               if ($resultado){
                    // Autenticar el usuario
                   // session_start();
                   if(!isset($_SESSION)) {
                    session_start();
                     }

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionamiento
                        if($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }
               }

            }else{
                Usuario::setAlerta('error', 'Usuario no encontrado');
            }


          }

       }

       $alertas =  Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout(){
        //echo "Desde Logout";
        if(!isset($_SESSION)) {
            session_start();
             }

        $_SESSION = [];

        header('Location: /');

    }

    public static function olvide(Router $router){
        //echo "Desde Olvide";
       
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
          
            $auth = new Usuario($_POST);
           $alertas = $auth->validarEmail();
           // debuguear($auth);

           if(empty($alertas)){
                $usuario = Usuario::where('email', $auth->email);
               // debuguear($usuario);
               if($usuario && $usuario->confirmado === "1"){
                   // debuguear('Si existe y esta confirmado');

                   //Generar un token
                   $usuario->crearToken();
                   $usuario->guardar();
                   
                   // TODO: Enviar el Email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                   // Alerta de exito
                   Usuario::setAlerta('exito', 'Revisa tu email');

               }else{
                Usuario::setAlerta('error', 'El Usuario no existe o no esta confirmado');
               
               }
           }

        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router){
       // echo "Desde Recuperar";
       $alertas = [];
       $error = false;

       $token = s($_GET['token']);

       // Buscar usuario por su token
       $usuario = Usuario::where('token', $token);

       if(empty($usuario)){
            Usuario::setAlerta('error', 'Token no Valido');
            $error = true;
       }

      // debuguear($usuario);

      if($_SERVER['REQUEST_METHOD'] === 'POST'){
        // Leer el nuevo password y guardarlo
        $password = new Usuario($_POST);

        $alertas = $password->validarPassword();
        //debuguear($password);

        if(empty($alertas)){
           // debuguear($usuario);
           $usuario->password = null;
           //debuguear($password);
           $usuario->password = $password->password;
           $usuario->hashPassword();
           $usuario->token = null;

           $resultado = $usuario->guardar();
            if($resultado){
                header('Location: /');
            }   

           debuguear($usuario);
        }
      }

       $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crear(Router $router){
       // echo "Desde Crear";
        $usuario = new Usuario;
        //debuguear($usuario);

        // Alertas Vacias
        $alertas = [];

       if($_SERVER['REQUEST_METHOD'] === 'POST'){
           // echo "Enviaste el formulario";
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();
            
            //debuguear($alertas);

            //Revisae que alertas este vacio
            if(empty($alertas))
            {
                //echo "Pasastes la validacion";
                // Verificar que el usuario no este registrado
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows){
                    $alertas = Usuario::getAlertas();
                }else {
                    // No esta registrado
                    // debuguear('No esta registrado');

                    // Vamos a Hashear el password
                    $usuario->hashPassword();

                    // Generar un Token unico
                    $usuario->crearToken();

                    // Enviar Email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    // debuguear($email);

                    $email->enviarConfirmacion();

                    // Crear el usuario
                    $resultado = $usuario->guardar();

                  // debuguear($usuario);
                    if($resultado){
                        //echo "Guardado Correctamente";
                        header('Location: /mensaje');
                    }
                }
            }            
       }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router){

        $router->render('auth/mensaje');
    }


    public static function confirmar(Router $router){
        $alertas = [];

        $token = s($_GET['token']);

      // debuguear($token);

       $usuario = Usuario::where('token', $token);

      // debuguear($usuario);

      if(empty($usuario)){
        //Mostrar Mensaje de error
        //echo "Token no valido";
        Usuario::setAlerta('error', 'Token no Valido');


      }else {
        //Modificar a usuario confirmado
        //echo "Token valido, confirmando usuario"; 
       // debuguear($usuario);

        $usuario->confirmado = "1";
        $usuario->token = '';
        //debuguear($usuario);
        $usuario->guardar();
        Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
      }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }




}


?>