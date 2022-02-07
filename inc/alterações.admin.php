<?php 

function paginaAlterações() {
    add_menu_page(
        'Alterações',
        'Alterações',
        'manage_options',
        'plugin-ambiente',
        'alteraçõesHTML',
        'dashicons-admin-site',
        6
    );
}

add_action( 'admin_menu', 'paginaAlterações' );

function alteraçõesHTML() {
    ?>Olá Evolker<?php
}

?>