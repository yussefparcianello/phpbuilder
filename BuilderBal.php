<?php

class BuilderBal {

    protected $tabela;

    public function __construct($tabela) {

        $this->tabela = ucfirst($tabela);
    }

    public function build() {

        $buf  = $this->build_abre_classe_e_arquivo();
        $buf .= $this->build_declaracao_variaveis();
        $buf .= $this->build_metodo_construtor();
        $buf .= $this->build_metodo_busca_todos();
        $buf .= $this->build_metodo_busca_todos_paginados();
        $buf .= $this->build_metodo_busca_por_id();
        $buf .= $this->build_metodo_insere_registro();
        $buf .= $this->build_metodo_atualiza_registro();
        $buf .= $this->build_metodo_deleta_registro();
        $buf .= $this->build_metodo_redirect();
        $buf .= $this->build_fecha_classe_e_arquivo();
        
        return $buf;
    }
    
    private function build_abre_classe_e_arquivo(){

        $buf  = $this->tab(0) . '<?php' . $this->eol(1);
        $buf .= $this->tab(1) . 'require_once $_SERVER[\'DOCUMENT_ROOT\'] . \'/dao/' . $this->tabela . 'Dao.php\';' . $this->eol(1);
        $buf .= $this->tab(1) . 'require_once $_SERVER[\'DOCUMENT_ROOT\'] . \'/vo/' . $this->tabela . 'Vo.php\';' . $this->eol(2);

        $buf .= $this->tab(1) . 'class ' . $this->tabela . 'Bal {' . $this->eol(2);

        return $buf;
    }
    
    private function build_declaracao_variaveis(){
        $buf  = $this->tab(2) . 'protected $' . lcfirst($this->tabela) . 'Dao;' . $this->eol(2);
        return $buf;
    }
    
    private function build_metodo_construtor() {
        $buf  = $this->tab(2) . 'function __construct() {' . $this->eol(1);
        $buf .= $this->tab(3) . '$this->' . lcfirst($this->tabela) . 'Dao = new ' . $this->tabela . 'Dao();' . $this->eol(1);
        $buf .= $this->tab(2) . '}' . $this->eol(2);
        
        return $buf;
    }

    private function build_metodo_busca_todos_paginados() {
        $buf  = $this->tab(2) . 'public function getAllPaginated($datatable_page, $filter_data) {' . $this->eol(2);
        $buf .= $this->tab(3) . 'return $this->' . lcfirst($this->tabela) . 'Dao->getAllPaginated($datatable_page, $filter_data);' . $this->eol(1);
        $buf .= $this->tab(2) . '}' . $this->eol(2);

        return $buf;
    }

    private function build_metodo_busca_todos() {
        $buf  = $this->tab(2) . 'public function getAll() {' . $this->eol(2);
        $buf .= $this->tab(3) . 'return $this->' . lcfirst($this->tabela) . 'Dao->getAll();' . $this->eol(1);
        $buf .= $this->tab(2) . '}' . $this->eol(2);

        return $buf;
    }
    
    private function build_metodo_busca_por_id() {
        $buf  = $this->tab(2) . 'public function getById($id) {' . $this->eol(2);
        $buf .= $this->tab(3) . 'return $this->' . lcfirst($this->tabela) . 'Dao->getById($id);' . $this->eol(1);
        $buf .= $this->tab(2) . '}' . $this->eol(2);
        
        return $buf;
    }
    
    private function build_metodo_deleta_registro() {
        $buf  = $this->tab(2) . 'public function delete($id) {' . $this->eol(2);
        $buf .= $this->tab(3) . '$retorno = $this->' . lcfirst($this->tabela) . 'Dao->delete($id);' . $this->eol(2);
        $buf .= $this->tab(3) . 'if($retorno){' . $this->eol(2);
        $buf .= $this->tab(4) . '$this->redirect();' . $this->eol(1);
        $buf .= $this->tab(3) . '}' . $this->eol(1);
        $buf .= $this->tab(2) . '}' . $this->eol(2);
        
        return $buf;
    }
    
    private function build_metodo_insere_registro() {
        $buf  = $this->tab(2) . 'public function insert(' . $this->tabela. 'Vo $' . lcfirst($this->tabela) . 'Vo) {' . $this->eol(2);
        $buf .= $this->tab(3) . '$retorno = $this->' . lcfirst($this->tabela) . 'Dao->insert($' . lcfirst($this->tabela) . 'Vo);' . $this->eol(2);
        $buf .= $this->tab(3) . 'if($retorno){' . $this->eol(2);
        $buf .= $this->tab(4) . '$this->redirect();' . $this->eol(1);
        $buf .= $this->tab(3) . '}' . $this->eol(1);
        $buf .= $this->tab(2) . '}' . $this->eol(2);
        
        return $buf;
    }

    private function build_metodo_atualiza_registro() {
        $buf  = $this->tab(2) . 'public function update(' . $this->tabela. 'Vo $' . lcfirst($this->tabela) . 'Vo) {' . $this->eol(2);
        $buf .= $this->tab(3) . '$retorno = $this->' . lcfirst($this->tabela) . 'Dao->update($' . lcfirst($this->tabela) . 'Vo);' . $this->eol(2);
        $buf .= $this->tab(3) . 'if($retorno){' . $this->eol(2);
        $buf .= $this->tab(4) . '$this->redirect();' . $this->eol(1);
        $buf .= $this->tab(3) . '}' . $this->eol(1);
        $buf .= $this->tab(2) . '}' . $this->eol(2);
        
        return $buf;
    }

    private function build_metodo_redirect() {
        $buf  = $this->tab(2) . 'public function redirect() {' . $this->eol(2);
        $buf .= $this->tab(3) . 'header(\'Location: /' . $this->tabela . '/index\');' . $this->eol(1);
        $buf .= $this->tab(2) . '}' . $this->eol(2);
        
        return $buf;
    }

  
    private function build_fecha_classe_e_arquivo(){
        $buf  = $this->tab(1) . '}' . $this->eol(1);
        $buf .= $this->tab(0) . '?>' . $this->eol(1);

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