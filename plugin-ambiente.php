<?php
/*
 * Plugin Name: Plugin Ambiente
 * Version:     0.2
 * Description: Mostra uma barra fixa no topo mostrando o ambiente em que o projeto está rodando.
 * Author:      Evolker Tecnologia
 * Author URI:  https://evolker.com.br
*/

function adicionarVersaoBetaNoTopo()
{
    $localAtual = pegarLocalAtual();
    $corLocalAtual = pegarCorParaLocalAtual();
    $temaAtual = wp_get_theme()->get_stylesheet();
    $versaoTemaAtual = wp_get_theme()->get("Version");
    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.1/font/bootstrap-icons.min.css" integrity="sha512-WYaDo1TDjuW+MPatvDarHSfuhFAflHxD87U9RoB4/CSFh24/jzUHfirvuvwGmJq0U7S9ohBXy4Tfmk2UKkp2gA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@500&display=swap');
        .aviso-dev-admin {
            left: 1%;
        }

        .aviso-dev {
            position: fixed;
            display: flex;
            justify-content: space-between;
            max-width: 100%;
            width: 100%;
            background-color: <?= $corLocalAtual["corDeFundo"] ?>;
            z-index: 9999;
            left: 0;
            border-bottom: 2px solid <?= $corLocalAtual["corDoTexto"] ?>;
        }

        .aviso-dev p {
            display: flex;
            align-items: center;
        }

        .aviso-dev p a {
            font-family: 'Outfit', sans-serif;
            font-size: 17px;
            color: <?= $corLocalAtual["corDoTexto"] ?>;
            font-weight: 500 !important;
            text-decoration: underline;
        }

        .aviso-dev p a:hover {
            color: <?= $corLocalAtual["corDoTextoHover"] ?>;
        }

        .fecha-aviso {
            color: #dc3545;
            font-weight: 800;
            padding: 5px 10px;
            font-size: 14px;
            text-decoration: none;
            transition: color .2s;
            margin: 0; 
            cursor: pointer;
        }

        .fecha-aviso:hover {
            text-shadow: 0px 0px 10px #dc3545;
        }

        i.bi {
            font-size: 30px;
        }

        @keyframes someAviso {
            from {clip-path: polygon(0 0, 100% 0, 100% 100%, 0% 100%)}
            to {clip-path: polygon(0 0, 100% 0, 100% 0, 0 0)}
        }

    </style>

    <div class="aviso-dev <?= is_admin()? 'aviso-dev-admin': ''?>">
        <p class="fecha-aviso fecha-aviso-esquerda" onclick="fechaAviso()">
            <i class="bi bi-x"></i>
        </p>
        <p class="color:red">
            <a href="https://canaltech.com.br/produtos/O-que-significa-dizer-que-um-software-ou-produto-esta-em-versao-beta/" target="_blank"><?= $temaAtual ?> v<?= $versaoTemaAtual ?> | Ambiente: <?= $localAtual ?></a>
        </p>
        <p class="fecha-aviso" onclick="fechaAviso()">
            <i class="bi bi-x"></i>
        </p>
    </div>

    <script>
        function fechaAviso() {
            let aviso = document.querySelector(".aviso-dev")
            aviso.style.animation = "someAviso .3s"
            setTimeout(() => {
                aviso.style.display = "none"
            }, 300);
        }
    </script>

    <?php
}

function retornarUrl()
{
    global $wp;
    return home_url($wp->request);
}

function verificarUrl()
{
    $url = retornarUrl();
    $locais = ["localhost", "dev.evolker", "hom.evolker"];
    $localAtual;

    foreach($locais as $local) {
        if (strpos($url, $local)) {
            $localAtual = $local;
            break;
        }
    }

    return $localAtual;
}

function pegarCorParaLocalAtual()
{
    $localAtual = verificarUrl();

    if($localAtual === "localhost") {
        return ["corDeFundo"=>"rgba(241, 196, 15, .6)", "corDoTexto"=>"#494802", "corDoTextoHover"=>"#5d4b02"];
    } else if($localAtual === "dev.evolker") {
        return ["corDeFundo"=>"rgba(46, 204, 113, .6)", "corDoTexto"=>"rgb(0 42 10)", "corDoTextoHover"=>"rgb(0 42 10)"];
    } else if($localAtual === "hom.evolker") { 
        return ["corDeFundo"=>"rgba(52, 152, 219, .6)", "corDoTexto"=>"#020849", "corDoTextoHover"=>"#05025d"];
    }
}


function pegarLocalAtual()
{
    $urlAtual = verificarUrl();

    if($urlAtual === "dev.evolker") {
        return "Dev";
    } else if($urlAtual === "hom.evolker") {
        return "Homologação";
    } else if($urlAtual === "localhost") {
        return "Local";
    }
}

if(!is_admin()) {
    add_action('wp_head', 'adicionarVersaoBetaNoTopo');
} else {
    add_action( 'wp_before_admin_bar_render', 'adicionarVersaoBetaNoTopo');
}

?>