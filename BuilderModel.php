<?php

class BuilderModel {
    
    protected $tabela;
    protected $listaColunas;

    public function __construct($tabela, $listaColunas) {

        $this->tabela = ucfirst($tabela);
        $this->listaColunas = $listaColunas;
    }

    public function build() {

        $buf  = $this->build_abre_classe_e_arquivo();
        $buf .= $this->build_declaracao_variaveis();
        $buf .= $this->build_metodo_construtor();
        $buf .= $this->build_getters();
        $buf .= $this->build_setters();
        $buf .= $this->build_validate();
        $buf .= $this->build_fecha_classe_e_arquivo();
        
        return $buf;
    }
    
    private function build_abre_classe_e_arquivo(){

        $buf  = $this->tab(0) . "<?php" . $this->eol(1);
        $buf .= $this->tab(1) . "class {$this->tabela}Vo {" . $this->eol(2);

        return $buf;
    }
    
    private function build_declaracao_variaveis(){

        $buf = "";
        foreach ($this->listaColunas as $coluna) {
            $buf .= $this->tab(2) . "protected \${$coluna->nome_coluna};" . $this->eol(1);
        }
        
        $buf .= $this->eol(1);
        $buf .= $this->tab(2) . "protected \$errors = [];" . $this->eol(1);
        $buf .= $this->eol(1);
        
        return $buf;
    }
    
    private function build_metodo_construtor() {

        $buf  = $this->tab(2) . "function __construct() {" . $this->eol(1);
        $buf .= $this->tab(2) . "}" . $this->eol(2);
        
        return $buf;
    }
    
    private function build_getters() {
        
        $buf   = $this->tab(2) . "public function __get(\$property) {" . $this->eol(1);
        $buf  .= $this->tab(3) . "if (property_exists(\$this, \$property)) {" . $this->eol(1);
        $buf  .= $this->tab(4) . "return \$this->\$property;" . $this->eol(1);
        $buf  .= $this->tab(3) . "}" . $this->eol(1);
        $buf  .= $this->tab(2) . "}" . $this->eol(2);

        return $buf;
    }

    private function build_setters() {
        
        $buf  = $this->tab(2) . "public function __set(\$property, \$value) {" . $this->eol(1);
        $buf .= $this->tab(3) . "if (property_exists(\$this, \$property)) {" . $this->eol(1);
        $buf .= $this->tab(4) . "\$this->\$property = \$value;" . $this->eol(1);
        $buf .= $this->tab(3) . "}" . $this->eol(1);
        $buf .= $this->tab(2) . "}" . $this->eol(2);

        return $buf;
    }

    private function build_validate() {
        
        $buf  = $this->tab(2) . "public function validate(\$validata_fields){" . $this->eol(2);

        foreach ($this->listaColunas as $coluna) {

            $buf .= $this->tab(3) . "if(in_array('{$coluna->nome_coluna}', \$validata_fields)){" . $this->eol(1);
            $buf .= $this->tab(4) . "if(empty(\$this->{$coluna->nome_coluna})) {" . $this->eol(1);
            $buf .= $this->tab(5) . "array_push(\$this->errors, ['type' => 'danger', 'msg' => 'Campo \"{$coluna->nome_coluna}\" não pode estar vazio']);" . $this->eol(1);
            $buf .= $this->tab(4) . "} " . $this->eol(1);
            $buf .= $this->tab(3) . "}" . $this->eol(2);
        }
        
        $buf .= $this->tab(3) . "return empty(\$this->errors);" . $this->eol(1);
        $buf .= $this->tab(2) . "}" . $this->eol(2);

        return $buf;
    }
    
    private function build_fecha_classe_e_arquivo(){
        $buf  = $this->tab(1) . "}" . $this->eol(1);
        $buf .= $this->tab(0) . "?>" . $this->eol(1);

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