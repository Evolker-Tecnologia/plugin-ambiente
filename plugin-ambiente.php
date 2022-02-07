<?php
/*
 * Plugin Name: Plugin Ambiente
 * Version:     0.2
 * Description: Mostra uma barra fixa no topo mostrando o ambiente em que o projeto está rodando.
 * Author:      Evolker Tecnologia
 * Author URI:  https://evolker.com.br
 */
session_start();
require_once "inc/conexao.pdo.php";
require_once "inc/utils.rest.php";
require_once "inc/utils.php";

function aoAtivarPlugin() { // ANTONY: por algum motivo a variavel $con aparece como NULL e por isso dá erro na hora de ativar o plugin
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $sql = file_get_contents('sql/wp_plugin_ambiente.sql', true);
    dbDelta($sql);
}

function adicionarBarraSuperior() {
    $localAtual = pegarLocalAtual();
    $corLocalAtual = pegarCorParaLocalAtual();
    $temaAtualPasta = wp_get_theme()->get_stylesheet();
    $temaAtual = wp_get_theme()->name;
    $versaoTemaAtual = wp_get_theme()->get("Version");
?>
    <!-- HTML DA BARRA ================================================================= -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.1/font/bootstrap-icons.min.css" integrity="sha512-WYaDo1TDjuW+MPatvDarHSfuhFAflHxD87U9RoB4/CSFh24/jzUHfirvuvwGmJq0U7S9ohBXy4Tfmk2UKkp2gA==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__) ?>assets/css/style.css">

    <style>
        .aviso-dev {
            background-color: <?= $corLocalAtual["corDeFundo"] ?>;
        }

        .container-botoes {
            border-bottom: 2px solid<?= $corLocalAtual["corDoTexto"] ?>;
        }

        .aviso-dev p a {
            color: <?= $corLocalAtual["corDoTexto"] ?>;
        }

        .aviso-dev p a:hover {
            color: <?= $corLocalAtual["corDoTextoHover"] ?>;
        }

        .container-aviso-dev {
            display: none;
        }
    </style>

    <div class="container-aviso-dev <?= is_admin()? 'container-aviso-dev-admin': ''?>">
        <div>
            <div class="aviso-dev">
                <p class="fecha-aviso fecha-aviso-esquerda" onclick="fecharAviso()">
                    <i class="bi bi-x"></i>
                </p>
                <p>
                    <a href="https://canaltech.com.br/produtos/O-que-significa-dizer-que-um-software-ou-produto-esta-em-versao-beta/" target="_blank"><?= $temaAtual ?> | <?= $temaAtualPasta ?> <?= $versaoTemaAtual ?> | Ambiente: <?= $localAtual ?></a>
                </p>
                <p class="fecha-aviso" onclick="fecharAviso()">
                    <i class="bi bi-x"></i>
                </p>
            </div>
            <div class="container-botoes">
                <button class="btnEstouMexendo" onclick="definirEstouMexendo()" disabled>Estou mexendo!</button>
                <button class="btnNaoEstouMexendo" onclick="definirNaoEstouMexendo()" disabled>Não estou mexendo!</button>
            </div>
        </div>
        <div class="aviso-dev mexendo">
            <span class="estado"></span>
            <button class="btnNaoEstouMexendo" onclick="definirNaoEstouMexendo()" disabled>Não estou mexendo!</button>
        </div>
    </div>

    <script>
        let caminhoApi = "<?= get_site_url()."/wp-json/plug-ambiente/" ?>"
        let btnEstouMexendo = document.querySelector(".btnEstouMexendo")
        let btnNaoEstouMexendo = document.querySelector(".btnNaoEstouMexendo")
        let estadoDaAplicacao = document.querySelector(".estado")
        let containerMexendo = document.querySelector(".mexendo")
        let containerAviso = document.querySelector(".container-aviso-dev")
        let aviso = document.querySelector(".aviso-dev")

        function pegarEstadoAtual() {
            fetch(caminhoApi+"estado?estadoAtual")
            .then(function(resposta) {
                return resposta.text()
            })
            .then(function(resposta) {
                if(resposta == 1) {
                    btnNaoEstouMexendo.disabled = "true"
                    btnEstouMexendo.removeAttribute("disabled")
                    estadoDaAplicacao.innerText = ""
                    containerMexendo.style.display = "none"
                } else if(resposta == 2) {
                    btnNaoEstouMexendo.removeAttribute("disabled")
                    btnEstouMexendo.disabled = "true"
                    estadoDaAplicacao.innerText = "Alguém está editando..."
                    containerMexendo.style.display = "flex"
                }
            })
        }

        function fecharAviso() {
            containerAviso.style.animation = "someAviso .3s"
            setTimeout(() => {
                containerAviso.style.display = "none"
            }, 300)

            if("<?= verificarUrl() ?>" === "localhost") {
                fetch(caminhoApi+"fecharBarra?fechar=1")
            }
        }

        function definirEstouMexendo() {
            fetch(caminhoApi + "mexendo?definirMexendo=2")
            .then(function() {
                pegarEstadoAtual()
            })
        }

        function definirNaoEstouMexendo() {
            fetch(caminhoApi + "mexendo?definirMexendo=1")
            .then(function() {
                pegarEstadoAtual()
            })
        }

        window.onload = () => {
            if(<?= $_SESSION["barraFechada"]? $_SESSION["barraFechada"]: "false" ?>) {
                containerAviso.style.display = "none"
            } else {
                containerAviso.style.display = "flex"
                pegarEstadoAtual()
            }
        }
    </script>

    <?php
}

if (!is_admin()) {
    add_action('wp_head', 'adicionarBarraSuperior');
} else {
    add_action('wp_before_admin_bar_render', 'adicionarBarraSuperior');
}

register_activation_hook(__FILE__, "aoAtivarPlugin");

?>
