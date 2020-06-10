<?php
require_once "../vo/EsquemaVo.php";
require_once "../dao/TabelaDao.php";

class BuilderDevMenu {
    public $listaTabelas;
    
    public function __construct(EsquemaVo $esquemaVo) {
        $tabelaDao = new TabelaDao();
        $this->listaTabelas = $tabelaDao->getAllBySchemaId($esquemaVo->getId());
    }
    
    public function build() {
        $buf  = $this->build_html_passo_1();
        $buf .= $this->build_lista_links();
        $buf .= $this->build_html_passo_2();
        
        return $buf;
    }
    
    function build_html_passo_1() {
        $buf  = $this->tab(0) . "<!DOCTYPE html>" . $this->eol(1);
        $buf .= $this->tab(0) . "<html>" . $this->eol(1);
        $buf .= $this->tab(1) . "<head>" . $this->eol(1);
        $buf .= $this->tab(2) . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"/>" . $this->eol(1);
        $buf .= $this->tab(2) . "<title>Menu de Desenvolvimento</title>" . $this->eol(1);
        $buf .= $this->tab(1) . "</head>" . $this->eol(1);
        $buf .= $this->tab(1) . "<body>" . $this->eol(1);
        $buf .= $this->tab(2) . "<h3>Dev Menu</h3>" . $this->eol(1);
        
        return $buf;
    }
    
    function build_lista_links() {
        $buf  = $this->tab(2) . "<ul>" . $this->eol(1);
        
        foreach ($this->listaTabelas as $tabela) {
            $buf .= $this->tab(3) . "<li><a href=\"/ui/{$tabela->nome_tabela_bd}/index.php\">Ver {$tabela->nome_tabela_bd} List</a></li>" . $this->eol(1);
        }
        
        $buf .= $this->tab(2) . "</ul>" . $this->eol(1);
        
        return $buf;
    }
    
    function build_html_passo_2() {
        $buf  = $this->tab(1) . "</body>" . $this->eol(1);
        $buf .= $this->tab(0) . "</html>" . $this->eol(1);
        
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