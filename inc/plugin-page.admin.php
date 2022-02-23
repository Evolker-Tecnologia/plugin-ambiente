<?php

add_action('after_setup_theme', 'pluginAmbienteMenuDeAlteracoes_anexarAoPainel');

function pluginAmbienteMenuDeAlteracoes_anexarAoPainel() {
    add_menu_page('Plugin Ambiente', 'Plugin Ambiente', 'manage_options', 'plugin_ambiente', 'pluginAmbienteMenuDeAlteracoes_gerarVisao', null, 100);
}

function pluginAmbienteMenuDeAlteracoes_gerarVisao() {
    ?>
        <h1>Alterações</h1>
        <table class="tabela-alteracoes">
            <tr>
                <th>ID</th>
                <th>Autor</th>
                <th>Alteração</th>
            </tr>
        </table>
        <button onclick="confirmarAlteracoes()">Confirmar alterações</button>
        <script>

            function confirmarAlteracoes() {
                fetch(caminhoApi + "?acao=alteracoes", { method: "PUT" })
                    .then(() => location.reload())
                    .catch(e => console.error(e))
            }

            fetch(caminhoApi + "?alteracoes")
                .then(response => response.text())
                .then(response => {
                    response = JSON.parse(response)
                    response.forEach(alteracao => {
                        let tr = document.createElement("tr")
                        let tds = {
                            tdID: document.createElement("td"),
                            tdAutor: document.createElement("td"),
                            tdAlteracao: document.createElement("td")
                        }

                        tds.tdID.innerText = alteracao.id
                        tds.tdAutor.innerText = alteracao.autor
                        tds.tdAlteracao.innerText = alteracao.alteracoes
                        
                        let tdsKeysArray = Object.keys(tds)
                        
                        tdsKeysArray.forEach(td => {
                            tr.appendChild(tds[td])
                        })

                        tabelaAlteracoes.appendChild(tr)
                    })
                })
            
            let tabelaAlteracoes = document.querySelector(".tabela-alteracoes")
        </script>
    <?php
}