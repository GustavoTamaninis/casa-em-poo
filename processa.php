<?php 
    session_start();
    require_once "modelo/Casa.php";
    require_once "modelo/Porta.php";
    require_once "modelo/Janela.php";

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $acao = $_POST["acao"] ?? "";

        switch($acao){
            case 'construir':
                echo '<h2>🏠 Você escolheu construir a casa!</h2>';
                echo '<p>Preencha os dados abaixo para definir as características da sua casa:</p>';

                echo '
                        <form action="processa.php" method="post">
                        <input type="hidden" name="acao" value="salvar_casa">

                        <label><strong>Descrição da casa:</strong></label><br>
                        <input type="text" name="descricao" required><br><br>

                        <label><strong>Cor da casa:</strong></label><br>
                        <input type="text" name="cor" required><br><br>

                        <label><strong>Quantidade de portas:</strong></label><br>
                        <input type="number" name="qtde_portas" min="0" required><br><br>

                        <label><strong>Quantidade de janelas:</strong></label><br>
                        <input type="number" name="qtde_janelas" min="0" required><br><br>

                        <button type="submit">Avançar</button>
                    </form>
                ';
                break;
        }
    }
?>