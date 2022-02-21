<?php
    require_once "inc/conexao.pdo.php";

    if(defined('WP_UNINSTALL_PLUGIN')) {
        $sql = file_get_contents('sql/wp_plugin_ambiente_ao_remover.sql', true);
        $statement = $con->prepare($sql);
        $statement->execute();
    }
?>