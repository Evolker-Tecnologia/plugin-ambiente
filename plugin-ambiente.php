<?php
/*
 * Plugin Name: Plugin Ambiente
 * Version:     0.2
 * Description: Mostra uma barra fixa no topo mostrando o ambiente em que o projeto está rodando.
 * Author:      Evolker Tecnologia
 * Author URI:  https://evolker.com.br
*/
session_start();

function pegarCorParaLocalAtual() {
    $localAtual = verificarUrl();

    if ($localAtual === "localhost") {
        return ["corDeFundo" => "rgba(241, 196, 15, .6)", "corDoTexto" => "#494802", "corDoTextoHover" => "#5d4b02"];
    } else if ($localAtual === "dev.evolker") {
        return ["corDeFundo" => "rgba(46, 204, 113, .6)", "corDoTexto" => "rgb(0 42 10)", "corDoTextoHover" => "rgb(0 42 10)"];
    } else if ($localAtual === "hom.evolker") {
        return ["corDeFundo" => "rgba(52, 152, 219, .6)", "corDoTexto" => "#020849", "corDoTextoHover" => "#05025d"];
    }
}

function pegarLocalAtual() {
    $urlAtual = verificarUrl();

    if ($urlAtual === "dev.evolker") {
        return "Dev";
    } else if ($urlAtual === "hom.evolker") {
        return "Homologação";
    } else if ($urlAtual === "localhost") {
        return "Local";
    }
}

function fecharBarra($data) {
    $queryParam = $data->get_query_params()["fechar"];
    global $con;

    if(isset($queryParam)) {
        if($queryParam === '1') {
            $_SESSION["barraFechada"] = 1;
        }
    }
}

function retornarUrl() {
    global $wp;
    return home_url($wp->request);
}

function verificarUrl() {
    $url = retornarUrl();
    $locais = ["localhost", "dev.evolker", "hom.evolker"];
    $localAtual;

    foreach ($locais as $local) {
        if (strpos($url, $local)) {
            $localAtual = $local;
            break;
        }
    }

    return $localAtual;
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
    <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__) ?>style.css">

    <style>
        .aviso-dev {
            background-color: <?= $corLocalAtual["corDeFundo"] ?>;
            border-bottom: 2px solid<?= $corLocalAtual["corDoTexto"] ?>;
            display: none;
        }

        .aviso-dev p a {
            color: <?= $corLocalAtual["corDoTexto"] ?>;
        }

        .aviso-dev p a:hover {
            color: <?= $corLocalAtual["corDoTextoHover"] ?>;
        }

    </style>

    <div class="aviso-dev <?= is_admin() ? 'aviso-dev-admin' : '' ?>">
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

    <script>
        let aviso = document.querySelector(".aviso-dev")

        function fecharAviso() {
            aviso.style.animation = "someAviso .3s"
            setTimeout(() => {
                aviso.style.display = "none"
            }, 300)

            if("<?= verificarUrl() ?>" === "localhost") {
                let caminhoApi = '<?= get_site_url()."/wp-json/plug-ambiente/fecharBarra?fechar=1"; ?>'
                fetch(caminhoApi)
            }
        }

        window.onload = () => {
            if(<?= $_SESSION["barraFechada"]? $_SESSION["barraFechada"]: "false" ?>) {
                aviso.style.display = "none"
            } else {
                aviso.style.display = "flex"
            }
        }
    </script>

    <?php
}

add_action('rest_api_init', function () {
    register_rest_route( 'plug-ambiente/', 'fecharBarra/', array(
        'methods' => 'GET',
        'callback' => 'fecharBarra',
    ));
});

if (!is_admin()) {
    add_action('wp_head', 'adicionarBarraSuperior');
} else {
    add_action('wp_before_admin_bar_render', 'adicionarBarraSuperior');
}

?>