<?php

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