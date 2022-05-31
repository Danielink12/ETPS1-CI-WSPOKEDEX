<?php

namespace App\Controllers;

class Login extends BaseController
{

    public function __construct()
    {
        $this->$db = \Config\Database::connect();
        helper('utilidades');
    }

    public function index()
    {
        return view('welcome_message');
    }

    public function login(){
        $requestBody = json_decode($this->request->getBody());

        $usuario=$requestBody->Correo;
        $pass=$requestBody->Clave;

        $passencrypt = Encrypt($pass);

        //echo (Decrypt(utf8_decode( 'ÀÆØÐÚFb^`pÊèäÀÐ')));

        $query = $this->$db->query("SELECT * FROM usuario WHERE correo='".$usuario."' AND clave='".$passencrypt."'");

        $resultado = $query->getNumRows();

        $datos=$query->getResult();
    
        if($resultado>0){

            //$token = otorgarToken();

            $respuesta = array(
                "Ok"=>true,
                'usuario' => $datos,
                //"Token"=>$token,
                //"decode"=>decodeToken($token),
                //"fecha"=>$now
            );

            $this->response->setStatusCode(200,'Autorizado');
            return $this->response->setJSON($respuesta);
            //return view('dashboard');
        }else{
            $this->response->setStatusCode(401,'No autorizado');
        }
    }

    public function registro(){
        $requestBody = json_decode($this->request->getBody());

        $nombre=$requestBody->Nombre;
        $usuario=$requestBody->Correo;
        $pass=$requestBody->Clave;
        $pass=Encrypt($pass);

        $query = $this->$db->query("SELECT * FROM usuario WHERE correo='".$usuario."' ");

        $resultado = $query->getNumRows();

        $datos=$query->getResult();
    
        if($resultado>0){
            $this->response->setStatusCode(401,'El correo ya existe, proporcione uno diferente');
        }else{
            try {
                $query=$this->$db->query("INSERT INTO usuario(nombre,correo,clave) values('".$nombre."','".$usuario."','".$pass."')");
                $this->response->setStatusCode(200,'Felicidades, ya eres un maestro pokemon');
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
    }

    public function cambiarPW(){

        $requestBody = json_decode($this->request->getBody());

        $correo=$requestBody->Correo;
        $pass=$requestBody->Clave;
        $newpass=$requestBody->NClave;

        $pass = Encrypt($pass);
        $newpass = Encrypt($newpass);

        $query = $this->$db->query("SELECT * FROM usuario WHERE correo='".$correo."' AND clave='".$pass."'");

        $resultado = $query->getNumRows();
    
        if($resultado>0){

            $cambiopw = $this->$db->query("UPDATE usuario SET clave='".$newpass."'");
            $this->response->setStatusCode(200,'Cambio de contraseña exitoso');
            
        }else{
            $this->response->setStatusCode(401,'No fue posible hacer el cambio de contraseña');
        }

    }
}
