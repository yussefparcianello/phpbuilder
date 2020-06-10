<?php

class BuilderList {
    
    protected $tabela;
    protected $listaColunas;

    public function __construct($tabela, $listaColunas) {

        $this->tabela = ucfirst($tabela);
        $this->listaColunas = $listaColunas;
    }

    public function build() {
        $buf  = $this->build_trecho_php();
        $buf .= $this->build_cabecalho_lista();
        $buf .= $this->build_itens_lista();
        
        return $buf;
    }
    
    private function build_trecho_php() {
        $buf  = $this->tab(0) . "<?php" . $this->eol(1);
        $buf .= $this->tab(1) . 'require_once $_SERVER[\'DOCUMENT_ROOT\'] . \'/bal/' . $this->tabela . 'Bal.php\';' . $this->eol(2);
        $buf .= $this->tab(1) . "\${$this->tabela}Bal = new {$this->tabela}Bal();" . $this->eol(1);
        $buf .= $this->tab(1) . "\${$this->tabela}List = \${$this->tabela}Bal->getAll();" . $this->eol(1);
        $buf .= $this->tab(0) . "?>" . $this->eol(2);
        
        return $buf;
    }
    
    private function build_cabecalho_lista(){
        $buf  = $this->tab(0) . "<h1>Lista de {$this->tabela}</h1>" . $this->eol(1);
        $buf .= $this->tab(0) . "<table class=\"Tabela\">" . $this->eol(1);
        $buf .= $this->tab(1) . "<tr>" . $this->eol(1);
        
        foreach ($this->listaColunas as $coluna) {
            $buf .= $this->tab(2) . "<td>{$coluna->nome_coluna}<td>" . $this->eol(1);
        }
        
        $buf .= $this->tab(1) . "</tr>" . $this->eol(2);
        return $buf;
    }
    
    private function build_itens_lista() {
        $buf  = $this->tab(1) . "<?php" . $this->eol(1);
        $buf .= $this->tab(2) . "foreach (\${$this->tabela}List as \${$this->tabela}) {" .$this->eol(1);
        $buf .= $this->tab(3) . "\$linha  = '<tr>';" . $this->eol(1);
        
        foreach ($this->listaColunas as $coluna) {
            $buf .= $this->tab(3) . "\$linha .= '<td>' . \${$this->tabela}->{$coluna->nome_coluna} . '<td>';" . $this->eol(1);
        }
        
        $buf .= $this->tab(3) . "\$linha .= '</tr>';" . $this->eol(2);
        $buf .= $this->tab(3) . "echo \$linha;" . $this->eol(1);
        $buf .= $this->tab(2) . "}" . $this->eol(1);
        $buf .= $this->tab(1) . "?>" . $this->eol(1);
        $buf .= $this->tab(0) . "</table>" . $this->eol(1);
        return $buf;
    }
    
    private function tab($numTab) {
        return str_repeat('    ', $numTab);
    }
    
    private function eol($numQuebraLinhas) {
        return str_repeat(PHP_EOL, $numQuebraLinhas);
    }
}
?>