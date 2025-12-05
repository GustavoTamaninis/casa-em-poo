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

            case 'salvar_casa':
                $descricao = $_POST['descricao'] ?? '';
                $cor = $_POST['cor'] ?? '';
                $qtdePortas = (int)($_POST['qtde_portas'] ?? 0);
                $qtdeJanelas = (int)($_POST['qtde_janelas'] ?? 0);

                echo "<h2>Etapa 2: Definir portas e janelas</h2>";
                echo "<form action='processa.php' method='POST'>";
                echo "<input type='hidden' name='acao' value='finalizar_casa'>";
                echo "<input type='hidden' name='descricao' value='{$descricao}'>";
                echo "<input type='hidden' name='cor' value='{$cor}'>";
                echo "<input type='hidden' name='qtde_portas' value='{$qtde_portas}'>";
                echo "<input type='hidden' name='qtde_janelas' value='{$qtde_janelas}'>";

                if($qtde_portas > 0){
                    echo "<h3>🚪Portas</h3>";
                    for($i = 1; $i <= $qtde_portas; $i++){
                        echo "<label>Descrição da Porta {$i}:</label><br>";
                        echo "<input type='text' name='descricao_porta_{$i}' required'><br>";
                        echo "<label>Estado:</label>";
                        echo 
                            "<select name='estado_porta_{$i}'>
                                <option value='0'>Fechada</option>
                                <option value='1'>Aberta</option>
                            <select><br><br>";
                    }
                }

                if($qtde_janelas > 0){
                    echo "<h3>🪟Janelas</h3>";
                    for($i = 1; $i <= $qtde_janelas; $i++){
                        echo "<label>Descrição da Janela {$i}:</label><br>";
                        echo "<input type='text' name='descricao_janela_{$i}' required'><br>";
                        echo "<label>Estado:</label>";
                        echo 
                            "<select name='estado_janela_{$i}'>
                                <option value='0'>Fechada</option>
                                <option value='1'>Aberta</option>
                            <select><br><br>";
                    }
                }

                echo "<button type='submit'>Finalizar Construção</button>";
                echo "</form>";
                break;

            case 'finalizar_casa':
                $descricao = $_POST['descricao'] ?? '';
                $cor = $_POST['cor'] ?? '';
                $qtdePortas = (int)($_POST['qtde_portas'] ?? 0);
                $qtdeJanelas = (int)($_POST['qtde_janelas'] ?? 0);

                $casa = new Casa();
                $casa->setDescricao($descricao);
                $casa->setCor($cor);

                $listaPortas = [];
                for($i = 1; $i <= $qtde_portas; $i++){
                    $porta = new Porta();
                    $porta->setDescricao($_POST["descricao_porta_{$i}"]);
                    $porta->setEstado($_POST["estado_porta_{$i}"]);
                    $listaPortas[] = $porta;
                }
                $casa->setListaDePortas($listaPortas);

                $listaJanelas = [];
                for($i = 1; $i <= $qtde_janelas; $i++){
                    $janela = new Janela();
                    $janela->setDescricao($_POST["descricao_janela_{$i}"]);
                    $janela->setEstado($_POST["estado_janela_{$i}"]);
                    $listaJanelas[] = $janela;
                }
                $casa->setListaDeJanelas($listaJanelas);

                $_SESSION['casa'] = serialize($casa);

                //Exibe resumo:
                echo "<h2>🏗️Casa construída com sucesso!</h2>";
                echo "<p><strong>Descrição:</strong> {$casa->getDescricao()}</p>";
                echo "<p><strong>Cor:</strong> {$casa->getCor()}</p>";

                echo "<h3>🚪Portas:</h3>";
                foreach($casa->getListaDePortas() as $porta){
                    $estado = $porta->getEstado() == 1 ? "Aberta" : "fechada";
                    echo "<p>{$porta->getDescricao()} - {$estado}</p>";
                }

                echo "<h3>🪟Janelas:</h3>";
                foreach($casa->getListaDeJanelas() as $janela){
                    $estado = $janela->getEstado() == 1 ? "Aberta" : "fechada";
                    echo "<p>{$janela->getDescricao()} - {$estado}</p>";
                }

                echo "<br><a href='index.html'>⬅️Voltar ao menu</a>";
                break;
        }
    }
?>