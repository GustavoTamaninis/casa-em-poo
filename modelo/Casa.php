<?php 
    require_once "Aberturas.php";
    require_once "Porta.php";
    require_once "Janela.php";

    class Casa{
        private string $descricao;
        private string $cor;
        private array $listaDePortas = [];
        private array $listaDeJanelas = [];

        public function getDescricao(): string{
            return $this->descricao;
        }

        public function setDescricao(string $descricao): void{
            $this->descricao = $descricao;
        }

        public function getCor(): string{
            return $this->cor;
        }

        public function setCor(string $cor): void{
            $this->cor = $cor;
        }

        public function getListaDePortas(): array{
            return $this->listaDePortas;
        }

        public function setListaDePortas(array $listaDePortas): void{
            $this->listaDePortas = $listaDePortas;
        }

        public function getListaDeJanelas(): array{
            return $this->listaDeJanelas;
        }

        public function setListaDeJanelas(array $listaDeJanelas): void{
            $this->listaDeJanelas = $listaDeJanelas;
        }


        public function getAberturasPorTipo(string $tipo): array{
            if($tipo == "porta"){
                return $this->listaDePortas;
            }else if($tipo == "janela"){
                return $this->listaDeJanelas;
            }else{
                return [];
            }
        }
    }
?>