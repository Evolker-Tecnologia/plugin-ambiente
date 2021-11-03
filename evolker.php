<?php
/*
 * Plugin Name: Evolker
 * Version: 1.0beta
 */

function adicionarVersaoBetaNoTopo()
{
    ?>

    <style>
        .aviso-dev {
            display: flex;
            max-width: 100%;
            width: 100%;
            background-color: rgba(0, 128, 0, 0.28);
            justify-content: center;
        }

        .aviso-dev p a {
            color: #024902;
        }

        .aviso-dev p a:hover {
            color: #025d02;
        }

    </style>

    <div class="aviso-dev">
        <p class="color:red">
            <a href="https://canaltech.com.br/produtos/O-que-significa-dizer-que-um-software-ou-produto-esta-em-versao-beta/" target="_blank">Beta v0.6 (24/10/2021 - 15h20)</a>
        </p>
    </div>

    <?php
}

add_action('wp_head', 'adicionarVersaoBetaNoTopo');

?>