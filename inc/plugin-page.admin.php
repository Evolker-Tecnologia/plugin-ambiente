<?php

add_action('admin_menu', 'pluginAmbienteMenuDeAlteracoes_anexarAoPainel');

function pluginAmbienteMenuDeAlteracoes_anexarAoPainel() {
    add_menu_page('Alterações', 'Alterações', 'manage_options', "alteracoes", 'pluginAmbienteMenuDeAlteracoes_gerarVisao', null, 5);
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
        <script>
            let tabelaAlteracoes = document.querySelector(".tabela-alteracoes")

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
        </script>
    <?php
}