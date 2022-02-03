<?php

function pegarEstadoDoAmbiente($data) {
    global $con;
    $resposta = $con->query("SELECT * FROM plug_ambiente");
    echo $resposta->fetchAll(PDO::FETCH_ASSOC)[0]["alguem_esta_mexendo"];
}

function definirEstadoDoAmbiente($data) {
    $queryParams = $data->get_query_params()["definirMexendo"];
    global $con;

    if(isset($queryParams)) {
        if($queryParams === '1') {
            $con->query("UPDATE plug_ambiente SET alguem_esta_mexendo = 1 WHERE id = 0");
        } else if($queryParams === '2') {
            $con->query("UPDATE plug_ambiente SET alguem_esta_mexendo = 2 WHERE id = 0");
        }
    }
}

function fecharBarra($data) {
    $queryParam = $data->get_query_params()["fechar"];

    if(isset($queryParam)) {
        if($queryParam === '1') {
            $_SESSION["barraFechada"] = 1;
        }
    }
}

add_action('rest_api_init', function () {
    register_rest_route( 'plug-ambiente/', 'mexendo/', array(
        'methods' => 'GET',
        'callback' => 'definirEstadoDoAmbiente',
    ));

    register_rest_route( 'plug-ambiente/', 'estado/', array(
        'methods' => 'GET',
        'callback' => 'pegarEstadoDoAmbiente',
    ));

    register_rest_route( 'plug-ambiente/', 'fecharBarra/', array(
        'methods' => 'GET',
        'callback' => 'fecharBarra',
    ));
});