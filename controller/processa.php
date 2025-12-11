<?php 
    session_start();
    require_once "../model/Casa.php";
    require_once "../model/Porta.php";
    require_once "../model/Janela.php";

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
                echo "<input type='hidden' name='qtde_portas' value='{$qtdePortas}'>";
                echo "<input type='hidden' name='qtde_janelas' value='{$qtdeJanelas}'>";

                if($qtdePortas > 0){
                    echo "<h3>🚪Portas</h3>";
                    for($i = 1; $i <= $qtdePortas; $i++){
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

                if($qtdeJanelas > 0){
                    echo "<h3>🪟Janelas</h3>";
                    for($i = 1; $i <= $qtdeJanelas; $i++){
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
                for($i = 1; $i <= $qtdePortas; $i++){
                    $porta = new Porta();
                    $porta->setDescricao($_POST["descricao_porta_{$i}"]);
                    $porta->setEstado($_POST["estado_porta_{$i}"]);
                    $listaPortas[] = $porta;
                }
                $casa->setListaDePortas($listaPortas);

                $listaJanelas = [];
                for($i = 1; $i <= $qtdeJanelas; $i++){
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

                echo "<br><a href='../view/index.html'>⬅️Voltar ao menu</a>";
                break;

            case "movimentar":
                if(!isset($_SESSION['casa'])){
                    echo "<h2>⚠️ Nenhuma casa foi construída ainda!</h2>";
                    echo "<br><a href='../view/index.html'>⬅️Voltar ao menu</a>";
                    exit;
                }

                echo "<h2>🚪Movimentar Aberturas</h2>";
                echo "<p>Informe qual tipo de abertura deseja mover:</p>";

                echo '
                    <form action="processa.php" method="post">
                        <input type="hidden" name="acao" value="selecionar_tipo_abertura">

                        <button type="submit" name="tipo_abertura" value="porta">Mover Porta</button>
                        <button type="submit" name="tipo_abertura" value="janela">Mover Janela</button>
                    </form>
                    ';
                echo "<br><a href='../view/index.html'>⬅️Voltar ao menu</a>";
                break;
            case 'selecionar_abertura':
                $casa = unserialize($_SESSION['casa']);
                $tipo = $_POST['tipo'] ?? '';

                $lista = ($tipo === 'porta') ? $casa->getListaDePortas() : $casa->getListaDeJanelas();

                if(empty($lista)){
                    echo "<h2>⚠️ Nenhuma " . ($tipo === 'porta' ? "porta" : "janela") . " cadastrada!</h2>";
                    echo "<br><a href='../view/index.html'>⬅️Voltar ao menu</a>";
                    exit;
                }

                echo "<h2>Selecione qual " . ($tipo === 'porta' ? "porta" : "janela") . " deseja movimentar:</h2>";
                echo "<form action='processa.php' method='POST'>";
                echo "<input type='hidden' name='acao' value='mover_abertura'>";
                echo "<input type='hidden' name='tipo' value='{$tipo}'>";
                echo "<select name='posicao'>";
                foreach ($lista as $i => $abertura){
                    $estado = $abertura->getEstadoTexto();
                    echo "<option value='{$i}'>{$abertura->getDescricao()} - {$estado}</option>";
                }
                echo "<select><br><br>";
                echo "<button type='submit'>Avançar</button>";
                echo "</form>";
                echo "<br><a href='../view/index.html'>⬅️Voltar ao menu</a>";
                break;
            case 'mover_abertura':
                $casa = unserialize($_SESSION['casa']);
                $tipo = $_POST['tipo'] ?? '';
                $posicao = (int)($_POST['posicao'] ?? -1);

                $abertura = $casa->retornaAbertura($tipo, $posicao);
                if(!$abertura){
                    echo "<h2>❌ Abertura Inválida.</h2>";
                    echo "<br><a href='../view/index.html'>⬅️Voltar ao menu</a>";
                    exit;
                }

                echo "<h2>Movendo " . ($tipo === 'porta' ? "porta" : "janela") . "selecionada:<h2>";
                echo "<p><strong>{$abertura->getDescricao()}</strong> (atual: {$abertura->getEstadoTexto()})</p>";

                echo "<form action='processa.php' method='POST'>";
                echo "<input type='hidden' name='acao' value='aplicar_movimento'>";
                echo "<input type='hidden' name='tipo' value='{$tipo}'>";
                echo "<input type='hidden' name='posicao' value='{$posicao}'>";
                echo "<label>Novo Estado:</label><br>";
                echo "<select name='novo_estado'>";
                echo "<option value='1'>Aberta</option>";
                echo "<option value='0'>Fechada</option>";
                echo "</select><br><br>";
                echo "<button type='submit'>Aplicar</button>";
                echo "</form>";
                echo "<br><a href='../view/index.html'>⬅️Voltar ao menu</a>";
                break;
            case 'aplicar_movimento':
                $casa = unserialize($_SESSION['casa']);
                $tipo = $_POST['tipo'] ?? '';
                $posicao = (int)($_POST['posicao'] ?? -1);
                $novoEstado = (int)($_POST['novo_estado'] ?? 0);

                $abertura = $casa->retornaAbertura($tipo, $posicao);

                if($abertura){
                    $casa->moverAbertura($abertura, $novoEstado);
                    $_SESSION['casa'] = serialize($casa);

                    echo "<h2>✅ " . ucfirst($tipo) . "movimentada com sucesso!</h2>";
                    echo "<p><strong>{$abertura->getDescricao()}</strong> agora está <strong>{$abertura->getEstadoTexto()}</strong>.</p>";
                }else{
                    echo "<h2>❌ Erro ao movimentar abertura.</h2>";
                }

                echo "<br><a href='../view/index.html'>⬅️Voltar ao menu</a>";
                break;
            case 'selecionar_tipo_abertura':
                $tipo = $_POST['tipo_abertura'] ?? '';
                echo "<form action='processa.php' method='POST'>";
                echo "<input type='hidden' name='acao' value='selecionar_abertura'>";
                echo "<input type='hidden' name='tipo' value='{$tipo}'>";
                echo "<button type='submit'>Continuar</button>";
                echo "</form>";
                break;
            case 'ver_info':
                if(!isset($_SESSION['casa'])){
                    echo "<h2>⚠️ Nenhuma casa foi construída ainda!</h2>";
                    echo "<br><a href='../view/index.html'>⬅️Voltar ao menu</a>";
                    break;
                }

                $casa = unserialize($_SESSION['casa']);
                echo $casa->geraInfoCasa();

                echo "
                        <br><form action='processa.php' method='POST'>
                        <button type='submit' name='acao' value='limpar_sessao'>🧹 Nova Construção</button>
                        </form>
                        <br><a href='../view/index.html'>⬅️Voltar ao menu</a>;
                     ";
                break;
            case 'limpar_sessao':
                session_unset();
                session_destroy();
                echo "<h2>🧹 Dados da casa apagados!</h2>";
                echo "<p>Você pode construir uma nova casa agora.</p>";
                echo "<a href='../view/index.html'>⬅️Voltar ao menu inicial.</a>";
                break;
            default:
                echo "<h2>❌ Ação inválida.</h2>";
                echo "<a href='../view/index.html'>⬅️Voltar ao menu.</a>";
                break;
        }
    }else{
        echo "<h2>⚠️ Nenhuma ação recebida.</h2>";
        echo "<a href='../view/index.html'>⬅️Voltar ao menu.</a>";
    }
?>