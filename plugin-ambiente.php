<?php
/*
 * Plugin Name: Plugin Ambiente
 * Version:     0.2
 * Description: Mostra uma barra fixa no topo mostrando o ambiente em que o projeto está rodando.
 * Author:      Evolker Tecnologia
 * Author URI:  https://evolker.com.br
 */

require_once "inc/utils.php";
require_once "inc/conexao.pdo.php";
require_once "hooks/plugin-page.admin.php";

function aoAtivarPlugin() { // ANTONY: por algum motivo a variavel $con aparece como NULL e por isso dá erro na hora de ativar o plugin
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $sql = file_get_contents('sql/wp_plugin_ambiente_ao_instalar.sql', true);
    dbDelta($sql);
}

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

function verificarUrl() {
    $url = retornarUrl();
    $locais = ["localhost", "dev.evolker", "hom.evolker"];
    global $localAtual;

    foreach ($locais as $local) {
        if (strpos($url, $local)) {
            $localAtual = $local;
            break;
        }
    }

    return $localAtual;
}

function rendezarVisualizacao() {
    $localAtual = pegarLocalAtual();
    $corLocalAtual = pegarCorParaLocalAtual();
    $temaAtualPasta = wp_get_theme()->get_stylesheet();
    $temaAtual = wp_get_theme()->name;
    $versaoTemaAtual = wp_get_theme()->get("Version");
?>
    <!-- HTML begin =================================================================================================== -->
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

    <div class="container-aviso-dev <?= is_admin() ? 'container-aviso-dev-admin' : '' ?>">
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
                <button class="btnEstouMexendo" onclick="iniciarAlteracoes()">Iniciar alterações</button>
                <button class="btnFinalizarAlteracoes" onclick="finalizarAlteracoes()">Finalizar alterações</button>
                <button class="btnCancelar" onclick="definirNaoEstouMexendo()">Cancelar</button>
                <button class="btnAlteracoes" onclick="verAlteracoes()">Ver alterações pendentes</button>
            </div>
        </div>
        <div class="aviso-dev mexendo">
            <span class="estado"></span>
        </div>
    </div>

    <div class="container-alteracao">
        <div>
            <label for="iptAutor">Autor</label>
            <input type="text" class="iptAutor" id="iptAutor">
        </div>
        <div>
            <label for="iptAlteracao">Descricao</label>
            <textarea id="iptAlteracao" class="iptAlteracao" cols="30" rows="10"></textarea>
        </div>
        <button class="btnRegistrarAlteracao" onclick="confirmarAlteracao()">Começar alteração</button>
        <button class="btnCancelarAlteracao" onclick="cancelarConfirmacaoDeAlteracao()">Cancelar alteração</button>
    </div>

    <!-- [Pedro Henrique] Não podemos isolar esse código porque o PHP não pode ser incorporado em um arquivo Javascript  -->
    <script>
        function aoCarregarPagina() {
            if (<?= isset($_SESSION["barraFechada"]) ? "true" : "false" ?>) {
                containerAviso.style.display = "none"
            } else {
                containerAviso.style.display = "flex"
                pegarEstadoAtual()
            }
        }

        function pegarEstadoAtual() {
            fetch(caminhoApi)
                .then(function (resposta) {
                    return resposta.text()
                })
                .then(function (resposta) {
                    if (resposta == 1) { // Antony: não está mexendo
                        btnCancelar.style.display = "none"
                        btnEstouMexendo.style.display = "block"
                        btnFinalizarAlteracoes.style.display = "none"
                        btnAlteracoes.style.display = "none"
                        btnCancelar.style.display = "none"
                        containerMexendo.style.display = "none"
                        estadoDaAplicacao.innerText = ""
                    } else if (resposta == 2) { // Antony: está mexendo
                        btnCancelar.style.display = "block"
                        btnEstouMexendo.style.display = "none"
                        btnAlteracoes.style.display = "none"
                        btnCancelar.style.display = "block"
                        btnFinalizarAlteracoes.style.display = "block"
                        estadoDaAplicacao.innerText = "Alguém está editando..."
                        containerMexendo.style.display = "flex"
                    }
                })
            fetch(caminhoApi + "?alteracoes")
                .then(response => response.text())
                .then(response => JSON.parse(response).length >= 1 ? btnAlteracoes.style.display = "block" : btnAlteracoes.style.display = "none")
        }

        function fecharAviso() {
            containerAviso.style.animation = "someAviso .3s"
            setTimeout(() => {
                containerAviso.style.display = "none"
            }, 300)

            if ("<?= verificarUrl() ?>" === "localhost") {
                fetch(caminhoApi, {method: 'DELETE'})
            }
        }

        function iniciarAlteracoes() {
            fetch(caminhoApi + "?acao=iniciar", {method: 'PUT'})
                .then(response => {
                    cancelarConfirmacaoDeAlteracao()
                    pegarEstadoAtual()
                })
        }

        function finalizarAlteracoes() {
            containerAlteracao.style.display = "block"
        }
        
        function confirmarAlteracao() {
            fetch(caminhoApi + "?acao=finalizar", {method: 'PUT'})
                .then(response => {
                    cancelarConfirmacaoDeAlteracao()
                    pegarEstadoAtual()
                })
            fetch(caminhoApi, {method: 'POST', body: JSON.stringify({
                autor: iptAutor.value,
                alteracao: iptAlteracao.value
            })})
        }

        function cancelarConfirmacaoDeAlteracao() {
            containerAlteracao.style.display = "none"
        }

        function definirNaoEstouMexendo() {
            fetch(caminhoApi + "?acao=finalizar", {method: 'PUT'})
                .then(function () {
                    pegarEstadoAtual()
                })
        }

        function verAlteracoes() {
            location.href = "<?= admin_url() . '?page=plugin_ambiente'; ?>" // [Pedro Henrique] Por algum motivo a função menu_page_url('plugin_ambiente') não funciona aqui.
        }

        let caminhoApi = "<?= get_site_url() . "/wp-content/plugins/plugin-ambiente/inc/plugin-ambiente.service.php" ?>"
        let btnEstouMexendo = document.querySelector(".btnEstouMexendo")
        let btnCancelar = document.querySelector(".btnCancelar")
        let btnAlteracoes = document.querySelector(".btnAlteracoes")
        let btnFinalizarAlteracoes = document.querySelector(".btnFinalizarAlteracoes")
        let iptAutor = document.querySelector(".iptAutor")
        let iptAlteracao = document.querySelector(".iptAlteracao")
        let estadoDaAplicacao = document.querySelector(".estado")
        let containerMexendo = document.querySelector(".mexendo")
        let containerAviso = document.querySelector(".container-aviso-dev")
        let aviso = document.querySelector(".aviso-dev")
        let containerAlteracao = document.querySelector(".container-alteracao")

        window.onload = aoCarregarPagina;
    </script>
    <!-- HTML end =================================================================================================== -->
<?php
}

register_activation_hook(__FILE__, "aoAtivarPlugin");
session_start();
rendezarVisualizacao();

?>