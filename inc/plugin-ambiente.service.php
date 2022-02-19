<?php

session_start();
require_once "conexao.pdo.php";
//require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

global $con;

function pegarEstadoDoAmbiente() {
    global $con;
    $resposta = $con->query("SELECT * FROM plugin_ambiente_situacao");
    echo $resposta->fetchAll(PDO::FETCH_ASSOC)[0]["alguem_esta_mexendo"];
}

function definirEstadoDoAmbiente($data) {
    $queryParams = $data->get_query_params()["definirMexendo"];
    global $con;

    if(isset($queryParams)) {
        if($queryParams === '1') {
            $con->query("UPDATE plugin_ambiente_situacao SET alguem_esta_mexendo = 1 WHERE id = 0");
        } else if($queryParams === '2') {
            $con->query("UPDATE plugin_ambiente_situacao SET alguem_esta_mexendo = 2 WHERE id = 0");
        }
    }
}

$metodo = $_SERVER['REQUEST_METHOD'];
$conteudo = file_get_contents('php://input');

switch ($metodo) {
    case 'DELETE':
        
        $_SESSION["barraFechada"] = 1;
            
        break;

    case 'PUT':
        $acao = $_GET['acao'];

        if ($acao === 'iniciar') {

            try {
                $con->query("UPDATE plugin_ambiente_situacao SET alguem_esta_mexendo = 2 WHERE id = 0");
            } catch (Exception $e) {
                http_response_code(500);
                echo $e;
            }

        } else if ($acao === 'finalizar') {
            try {
                $con->query("UPDATE plugin_ambiente_situacao SET alguem_esta_mexendo = 1 WHERE id = 0");
            } catch (Exception $e) {
                http_response_code(500);
                echo $e;
            }
        } else if ($acao === 'confirmar') {
            echo "nao implemetnado...";
        }

        break;

    case 'GET':
        pegarEstadoDoAmbiente();

        break;
        
    default:
//        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
//        die('{"msg": "Método não encontrado."}');
}