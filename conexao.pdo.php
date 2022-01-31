<?php

// Pedro Henrique (13/16/2021): se o arquivo tiver sido incluído a partir do functions.php, usar o caminho absoluto, se não, o caminho relativo
if(defined('ABSPATH')) require ABSPATH . "/wp-config.php";
else{
    // Pedro Henrique (14/40/2021): gambiarra necessária porque incluindo diretamente o wp-config.php gera uma saída inesperada, causando um bug no front-end.
    $str = file_get_contents('../../../wp-config.php', true);
    $re = '/(define\( ?\'DB_.+ \')(.+)?(\'.*\);)/m';
    preg_match_all($re, $str, $matches);

    define('DB_NAME', $matches[2][0]);
    define('DB_USER', $matches[2][1]);
    define('DB_PASSWORD', $matches[2][2]);
    define('DB_HOST', $matches[2][3]);
}

$servername = DB_HOST;
$username = DB_USER;
$password = DB_PASSWORD;
$dbname = DB_NAME;
$con = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, array(
    PDO::MYSQL_ATTR_FOUND_ROWS => true // Pedro Henrique: por padrão, o MySQL considera 0 linhas afetadas quando ocorre um update na qual os dados de entrada são iguais os atuais. Adicionei essa propriedade para sempre retornar o número de linhas encontradas e seguir as diretrizes
));
// set the PDO error mode to exception
$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Pedro Henrique: em caso de erros no SQL, disparar a exceção

global $con;
$metodo = $_SERVER['REQUEST_METHOD'];
$conteudo = file_get_contents('php://input');

switch ($metodo) {
    case 'GET':
        if(isset($_GET["estadoAtual"])) {
            $resposta = $con->query("SELECT * FROM plug_ambiente");
            $resposta = $resposta->fetchAll(PDO::FETCH_ASSOC)[0]["alguem_esta_mexendo"];
            header('Content-Type: application/json; charset=UTF8');
            echo json_encode($resposta, JSON_NUMERIC_CHECK);
        }

        if(isset($_GET["definirMexendo"])) {
            if($_GET["definirMexendo"] === '1') {
                $con->query("UPDATE plug_ambiente SET alguem_esta_mexendo = 1 WHERE id = 0");
            } else if($_GET["definirMexendo"] === '2') {
                $con->query("UPDATE plug_ambiente SET alguem_esta_mexendo = 2 WHERE id = 0");
            }

            header('Content-Type: application/json; charset=UTF8');
            echo json_encode($_GET['definirMexendo'], JSON_NUMERIC_CHECK);
        }

        break;
    default:
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        die('{"msg": "Método não encontrado."}');
}