<?php
namespace Model;

class Usuario extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id','nombre','apellido','email','password','telefono','confirmado','admin','token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $confirmado;
    public $admin;
    public $token;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->confirmado = $args['confirmado'] ?? '0';
        $this->admin = $args['admin'] ?? '0';
        $this->token = $args['token'] ?? '';
        
    }

    // Mensajes de validacion para la creacion de una cuenta
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'El Nombre del Cliente es Obligatorio';
        }else 

        if(!$this->apellido){
            self::$alertas['error'][] = 'El Apellido del Cliente es Obligatorio';
        }

        if(!$this->telefono){
            self::$alertas['error'][] = 'El Telefono del Cliente es Obligatorio';
        }

        if(!$this->email){
            self::$alertas['error'][] = 'El Email del Cliente es Obligatorio';
        }

        if(!$this->password){
            self::$alertas['error'][] = 'El Password del Cliente es Obligatorio';
        }

        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }

        return self::$alertas;
    }

    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }

        if(!$this->password){
            self::$alertas['error'][] = 'El Password es Obligatorio';
        }

        return self::$alertas;
    }

    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }

        return self::$alertas;
    }

    public function validarPassword(){
        if(!$this->password){
            self::$alertas['error'][] = 'El Password es Obligatorio';
        }

        if(strlen($this->password < 6)){
            self::$alertas['error'][] = 'El Password debe tener al menos 6 caracteres';
        }

        return self::$alertas;
    }

    // Revisar si el usuario ya existe
    public function existeUsuario() {
        $query = " SELECT * FROM " . self::$tabla  . " WHERE email = '" . $this->email . "' LIMIT 1 ";

        $resultado = self::$db->query($query);

        if($resultado->num_rows){ // Si es que hay algun resultado
            self::$alertas['error'][] = 'El usuario ya esta registrado';

        }
       // debuguear($resultado);
       return $resultado;
    }

    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken(){
        $this->token = uniqid();
    }


    public function comprobarPasswordAndVerificado($password) {
        $resultado = password_verify($password, $this->password);

        //debuguear($resultado);
        
        if(!$resultado || !$this->confirmado) {
            self::$alertas['error'][] = 'Password Incorrecto o tu cuenta no ha sido confirmada';
        } else {
            return true;
        }
    }
    

    }


?>