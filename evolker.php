<?php
/*
 * Plugin Name: Evolker
 * Version: 1.0beta
 */

function adicionarVersaoBetaNoTopo()
{
    $localAtual = pegarLocalAtual();
    $corLocalAtual = pegarCorParaLocalAtual();
    $temaAtual = wp_get_theme()->get_stylesheet();
    $versaoTemaAtual = wp_get_theme()->get("Version");
    ?>

    <style>
        .aviso-dev {
            display: flex;
            max-width: 100%;
            width: 100%;
            background-color: <?= $corLocalAtual["corDeFundo"] ?>;
            justify-content: center;
        }

        .aviso-dev p a {
            color: <?= $corLocalAtual["corDoTexto"] ?>;
        }

        .aviso-dev p a:hover {
            color: <?= $corLocalAtual["corDoTextoHover"] ?>;
        }

    </style>

    <div class="aviso-dev">
        <p class="color:red">
            <a href="https://canaltech.com.br/produtos/O-que-significa-dizer-que-um-software-ou-produto-esta-em-versao-beta/" target="_blank"><?= $temaAtual ?> v<?= $versaoTemaAtual ?> | Ambiente: <?= $localAtual ?></a>
        </p>
    </div>

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
        return ["corDeFundo"=>"rgba(124, 128, 0, 0.28)", "corDoTexto"=>"#494802", "corDoTextoHover"=>"#5d4b02"];
    } else if($localAtual === "dev.evolker") {
        return ["corDeFundo"=>"rgba(0, 128, 0, 0.28)", "corDoTexto"=>"#024902", "corDoTextoHover"=>"#025d02"];
    } else if($localAtual === "hom.evolker") {
        return ["corDeFundo"=>"rgba(0, 26, 128, 0.28)", "corDoTexto"=>"#020849", "corDoTextoHover"=>"#05025d"];;
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

add_action('wp_head', 'adicionarVersaoBetaNoTopo');

?>