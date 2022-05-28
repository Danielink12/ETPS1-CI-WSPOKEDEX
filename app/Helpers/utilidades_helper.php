<?php

use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use CodeIgniter\I18n\Time;

function __construct(){
    $key = \Config\Services::getSecretKey();
}

function Encrypt($cadena){

    for ($x=0; $x < strlen($cadena) ; $x++) { 
        # code...
        $Letr=substr($cadena,$x,1);
        $des=$des . chr((ord($Letr)-1)*2);
    }
    return utf8_encode ($des);
}

function Decrypt($cadena){

    for ($x=0; $x < strlen($cadena) ; $x++) { 
        # code...
        $Letr=substr($cadena,$x,1);
        $des=$des . chr((ord($Letr)/2)+1);
    }
    return $des;
}

function otorgarToken(){
    $now = Time::now();
    $exp = new Time('+60 minutes');

	$payload = [
		'aud' => 'http://POKEDEX.com',
        'iss' => 'Daniel Alas',
		'iat' => $now->getTimestamp(),
		'nbf' => $now->getTimestamp(),
        'exp' => $exp->getTimestamp()
	];
            
    $jwt = JWT::encode($payload, $key.$now->getDay(),'HS256');

    //$token = JWT::decode($jwt, new Key($key,'HS256'));

    return $jwt;
}

function validarToken($req){
    $now = Time::now();
    $header = $req;
    $token = preg_split('/[\s]+/',$header);
    $datajwt = JWT::decode($token[1], new Key($key.$now->getDay(),'HS256'));
    return json_encode($datajwt);
}

function decodeToken($token){
    $now = Time::now();
    $decode = JWT::decode($token, new Key($key.$now->getDay(),'HS256'));
    return $decode;
}

?>