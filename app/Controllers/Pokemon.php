<?php

namespace App\Controllers;

class Pokemon extends BaseController
{
    public function __construct()
    {
        $this->$db = \Config\Database::connect();
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
            'mensaje' => 'pokemons mostrados correctamente',
            'total_registros' => count($resultado),
            'pokemons' => $resultado
        );
        return $this->response->setJSON($respuesta);
    }

    public function pokemon($id){

        //$pokemon = $this->$db->query("SELECT * FROM pokemon where numero_pokedex=".$id);
        $pokemon = $this->$db->query("SELECT p.*, CASE  
                WHEN (SELECT count(*) FROM pokemon_tipo pkt
                    WHERE pkt.numero_pokedex=p.numero_pokedex)<2 THEN tp.nombre
                WHEN (SELECT count(*) FROM pokemon_tipo pkt
                    WHERE pkt.numero_pokedex=p.numero_pokedex)>1 THEN CONCAT(min(tp.nombre),' - ',max(tp.nombre))
                END as tipo
            FROM pokemon p
            INNER JOIN pokemon_tipo pkt ON p.numero_pokedex=pkt.numero_pokedex
            INNER JOIN tipo tp ON pkt.id_tipo=tp.id_tipo
            WHERE p.numero_pokedex=".$id);
        /*$tipo = $this->$db->query("SELECT * FROM pokemon_tipo pkt
        INNER JOIN tipo tp ON pkt.id_tipo=tp.id_tipo
        WHERE pkt.numero_pokedex=". $id);*/
        $movimientos = $this->$db->query("SELECT mov.*,tfa.* FROM pokemon_movimiento_forma tmf
        INNER JOIN movimiento mov ON tmf.id_movimiento=mov.id_movimiento
        INNER JOIN forma_aprendizaje fa ON tmf.id_forma_aprendizaje=fa.id_forma_aprendizaje
        INNER JOIN tipo_forma_aprendizaje tfa ON fa.id_tipo_aprendizaje=tfa.id_tipo_aprendizaje
        WHERE tmf.numero_pokedex=".$id);
        
        $respuesta = array(
            'error' => FALSE,
            'mensaje' => 'pokemon mostrado correctamente',
            'total_registros' => count($pokemon->getResult()),
            'pokemons' => $pokemon->getResult(),
            //'tipo' => $tipo->getResult(),
            //'movimientos' => $movimientos->getResult()
        );
        return $this->response->setJSON($respuesta);
    }

    public function addfavorito($usuarioid,$pokemonid){

        try {
            $query = $this->$db->query("INSERT INTO favoritos(usuarioid,numero_pokedex) VALUES(".$usuarioid.",".$pokemonid.")");
            $this->response->setStatusCode(200,'El pokemon fue agregado como favorito');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function isfavorito($usuarioid,$pokemonid){
        $query = $this->$db->query("SELECT * FROM favoritos WHERE usuarioid=".$usuarioid." AND ".$pokemonid."");
        $resultado = $query->getNumRows();
        if($resultado>0){
            $this->response->setStatusCode(200);
        }else{
            $this->response->setStatusCode(401);
        }
    }

    public function deletefavorito($usuarioid,$pokemonid){

        try {
            //code...
            $query = $this->$db->query("DELETE FROM favoritos WHERE usuarioid=".$usuarioid." AND numero_pokedex=".$pokemonid."");
            $this->response->setStatusCode(200,'Eliminado correctamente');
        } catch (\Throwable $th) {
            //throw $th;
        }
        
    }

}