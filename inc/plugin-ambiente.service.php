<?php

session_start();
require_once "conexao.pdo.php";
//require_once(ABSPATH . "wp-admin/includes/upgrade.php");

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
        if($queryParams === "1") {
            $con->query("UPDATE plugin_ambiente_situacao SET alguem_esta_mexendo = 1 WHERE id = 0");
        } else if($queryParams === "2") {
            $con->query("UPDATE plugin_ambiente_situacao SET alguem_esta_mexendo = 2 WHERE id = 0");
        }
    }
}

$metodo = $_SERVER["REQUEST_METHOD"];
$conteudo = file_get_contents("php://input");

switch ($metodo) {
    case "DELETE":
        
        $_SESSION["barraFechada"] = 1;
            
        break;

    case "PUT":
        $acao = $_GET["acao"];

        if ($acao === "iniciar") {

            try {
                $con->query("UPDATE plugin_ambiente_situacao SET alguem_esta_mexendo = 2 WHERE id = 0");
            } catch (Exception $e) {
                http_response_code(500);
                echo $e;
            }

        } else if ($acao === "finalizar") {
            try {
                $con->query("UPDATE plugin_ambiente_situacao SET alguem_esta_mexendo = 1 WHERE id = 0");
            } catch (Exception $e) {
                http_response_code(500);
                echo $e;
            }
        } else if ($acao === "confirmar") {
            echo "nao implemetnado...";
        } else if($acao === "alteracoes") {
            try {
                $con->query("UPDATE plugin_ambiente_alteracoes SET foi_confirmada = 1 WHERE foi_confirmada = 0");
            } catch (Exception $e) {
                http_response_code(500);
                echo $e;
            }
        }

        break;

    case "GET":
        if(isset($_GET["alteracoes"])) {
            $resultado = $con->query("SELECT * FROM plugin_ambiente_alteracoes WHERE foi_confirmada = 0");
            $resultado = $resultado->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($resultado);
        } else {
            pegarEstadoDoAmbiente();
        }

        break;
    
    case "POST":
        $conteudo = json_decode($conteudo, true);
        $autor = $conteudo["autor"];
        $alteracao = $conteudo["alteracao"];

        try {
            $con->query("INSERT INTO plugin_ambiente_alteracoes (autor, alteracoes) VALUES ('$autor', '$alteracao')");
        } catch (Exception $e) {
            http_response_code(500);
            echo $e;
        }

        break;
        
    default:
//        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
//        die("{"msg": "Método não encontrado."}");
}