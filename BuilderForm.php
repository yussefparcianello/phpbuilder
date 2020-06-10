<?php

class BuilderForm {
    
    protected $tabela;
    protected $listaColunas;

    public function __construct($tabela, $listaColunas) {

        $this->tabela = ucfirst($tabela);
        $this->listaColunas = $listaColunas;
    }

    public function build() {

        $buf  = $this->build_trecho_php();
        $buf .= $this->build_form();
        
        return $buf;
    }
    
    private function build_trecho_php(){
        $buf  = $this->tab(0) . "<?php" . $this->eol(1);
        $buf .= $this->tab(1) . 'require_once $_SERVER[\'DOCUMENT_ROOT\'] . \'/bal/' . $this->tabela . 'Bal.php\';' . $this->eol(1);
        $buf .= $this->tab(1) . 'require_once $_SERVER[\'DOCUMENT_ROOT\'] . \'/vo/' . $this->tabela . 'Vo.php\';' . $this->eol(2);
        $buf .= $this->tab(1) . '$' . lcfirst($this->tabela) . 'Vo = new ' . $this->tabela .'Vo();' . $this->eol(1);
        $buf .= $this->tab(1) . '$' . lcfirst($this->tabela) . 'Bal = new ' . $this->tabela .'Bal();' . $this->eol(1);
        $buf .= $this->tab(0) . '?>' . $this->eol(2);
        
        return $buf;
    }
    
    private function build_form(){
        $buf  = $this->tab(0) . '<h1>Cadastro de ' . $this->tabela .'</h1>' . $this->eol(1);
        $buf .= $this->tab(0) . '<form role="form" method="POST" action="">' . $this->eol(1);
                
        foreach ($this->listaColunas as $coluna) {
            $buf .= $this->tab(1) . '<div class="form-group">' . $this->eol(1);
            $buf .= $this->tab(2) . '<label for="' . $coluna->nome_coluna . '">' . $coluna->nome_coluna . '</label>' . $this->eol(1);
            $buf .= $this->tab(2) . '<input type="text" class="form-control" id="' . $coluna->nome_coluna . '" name="' . $coluna->nome_coluna . '" value="<?=$' . lcfirst($this->tabela) . 'Vo->' . $coluna->nome_coluna . '?>">' . $this->eol(1);
            $buf .= $this->tab(1) . '</div>' . $this->eol(2);
        }
        
        $buf .= $this->tab(1) . '<div class="form-group">' . $this->eol(1);
        $buf .= $this->tab(2) . '<button type="button" class="btn btn-default" onclick="history.back();">Voltar</button>' . $this->eol(1);
        $buf .= $this->tab(2) . '<button type="reset" class="btn btn-default">Limpar</button>' . $this->eol(1);
        $buf .= $this->tab(2) . '<button type="submit" class="btn btn-default">Salvar</button>' . $this->eol(1);
        $buf .= $this->tab(1) . '</div>' . $this->eol(1);
        $buf .= $this->tab(0) . '</form>' . $this->eol(1);
        
        return $buf;
    }
    
    private function tab($numTab) {
        return str_repeat("    ", $numTab);
    }
    
    private function eol($numQuebraLinhas) {
        return str_repeat(PHP_EOL, $numQuebraLinhas);
    }
}
?>