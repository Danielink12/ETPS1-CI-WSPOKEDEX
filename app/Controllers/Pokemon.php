<?php

namespace App\Controllers;

class Pokemon extends BaseController
{
    public function __construct()
    {
        $this->$db = \Config\Database::connect();;
    }

    public function index()
    {
        return view('welcome_message');
    }

    public function pokemons(){
        $query = $this->$db->query("SELECT * FROM POKEMON");
        $resultado = $query->getResult();
        $respuesta = array(
            'error' => FALSE,
            'mensaje' => 'registros mostrados correctamente',
            'total_registros' => count($resultado),
            'pokemons' => $resultado
        );
        return $this->response->setJSON($respuesta);
    }

}