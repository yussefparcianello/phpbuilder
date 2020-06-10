<?php

class BuilderDao {
    
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
        $buf .= $this->build_get_all();
        $buf .= $this->build_get_all_paginated();
        $buf .= $this->build_get_by_id();
        $buf .= $this->build_delete();
        $buf .= $this->build_insert();
        $buf .= $this->build_update();
        $buf .= $this->build_bindForPagination();
        $buf .= $this->build_fecha_classe_e_arquivo();
        
        return $buf;
    }
    
    private function build_abre_classe_e_arquivo(){

        $buf  = $this->tab(0) . '<?php' . $this->eol(1);
        $buf .= $this->tab(1) . 'require_once $_SERVER[\'DOCUMENT_ROOT\'] . \'/dao/ConexaoDao.php\';' . $this->eol(1);
        $buf .= $this->tab(1) . 'require_once $_SERVER[\'DOCUMENT_ROOT\'] . \'/vo/' . $this->tabela . 'Vo.php\';' . $this->eol(2);
        $buf .= $this->tab(1) . 'class ' . $this->tabela . 'Dao {' . $this->eol(2);

        return $buf;
    }
    
    private function build_declaracao_variaveis(){

        $buf = $this->tab(2) . 'protected $conexao = NULL;' . $this->eol(1);
        $buf = $this->tab(2) . 'protected $num_rows_per_page = 8;' . $this->eol(2);

        return $buf;
    }
    
    private function build_metodo_construtor() {

        $buf  = $this->tab(2) . 'function __construct() {' . $this->eol(1);
        $buf .= $this->tab(3) . '$this->conexao = new ConexaoDao();' . $this->eol(1);
        $buf .= $this->tab(2) . '}' . $this->eol(2);
        
        return $buf;
    }
    
    private function build_get_all() {

        $buf  = $this->tab(2) . 'public function getAll() {' . $this->eol(2);

        $buf .= $this->tab(3) . 'try {' . $this->eol(2);

        $buf .= $this->tab(4) . '$sql = "select * from ' . lcfirst($this->tabela) . ' order by id desc";' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt = $this->conexao->prepare($sql);' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt->execute();' . $this->eol(2);
        $buf .= $this->tab(4) . '$vector = array();' . $this->eol(1);
        $buf .= $this->tab(4) . 'while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {' . $this->eol(1);
        $buf .= $this->tab(5) . 'array_push($vector, $row);' . $this->eol(1);
        $buf .= $this->tab(4) . '}' . $this->eol(2);
        $buf .= $this->tab(4) . 'return $vector;' . $this->eol(2);

        $buf .= $this->tab(3) . '} catch (Exception $exc) {' . $this->eol(2);
        $buf .= $this->tab(4) . 'echo $exc->getMessage();' . $this->eol(2);
        $buf .= $this->tab(4) . 'return NULL;' . $this->eol(1);
        $buf .= $this->tab(3) . '}' . $this->eol(1);

        $buf .= $this->tab(2) . '}' . $this->eol(2);

        return $buf;
    }

    private function build_get_all_paginated() {

        $buf  = $this->tab(2) . 'public function getAllPaginated($datatable_page, $filter_data) {' . $this->eol(2);


        $buf .= $this->tab(3) . 'try {' . $this->eol(2);

        $buf .= $this->tab(4) . '//inicializa variáveis' . $this->eol(1);
        $buf .= $this->tab(4) . '$sql    = \'select * from ' . lcfirst($this->tabela) . '\';' . $this->eol(1);
        $buf .= $this->tab(4) . '$where  = \'where 1 = 1 \';' . $this->eol(2);

        $buf .= $this->tab(4) . '//monta as cláusulas where' . $this->eol(1);
        $buf .= $this->tab(4) . 'if (!empty($filter_data)){' . $this->eol(2);

        foreach ($this->listaColunas as $coluna) {
            $buf .= $this->tab(5) . '$where .= isset($filter_data[\'' . $coluna->nome_coluna . '\'])   ? \'and ' . $coluna->nome_coluna . ' = :' . $coluna->nome_coluna . ' \'    : \'\';' . $this->eol(1);
        }

        $buf .= $this->tab(4) . '}' . $this->eol(2);

        $buf .= $this->tab(4) . '//efetua a consulta para contabilizar os dados para paginação' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt = $this->conexao->prepare("select count(1) from ' . lcfirst($this->tabela) . ' " . $where);' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt = $this->bindValuesForPagination($stmt, $filter_data);' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt->execute();' . $this->eol(2);

        $buf .= $this->tab(4) . '//calcula os dados referentes a paginação' . $this->eol(1);
        $buf .= $this->tab(4) . '$total_records  = $stmt->fetchColumn();' . $this->eol(1);
        $buf .= $this->tab(4) . '$total_pages    = ceil($total_records / $this->num_rows_per_page);' . $this->eol(1);
        $buf .= $this->tab(4) . '$offset         = $datatable_page * $this->num_rows_per_page;' . $this->eol(2);

        $buf .= $this->tab(4) . '//efetua a consulta considerando a paginação' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt = $this->conexao->prepare($sql  . $where . \' order by id desc limit :offset, :num_rows_per_page\');' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt = $this->bindValuesForPagination($stmt, $filter_data);' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt->bindValue(\':offset\', $offset, PDO::PARAM_INT);' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt->bindValue(\':num_rows_per_page\',  $this->num_rows_per_page,   PDO::PARAM_INT);' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt->execute();' . $this->eol(2);

        $buf .= $this->tab(4) . '//transforma o resultado em uma lista de objetos' . $this->eol(1);
        $buf .= $this->tab(4) . '$vector = array();' . $this->eol(1);
        $buf .= $this->tab(4) . 'while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {' . $this->eol(1);
        $buf .= $this->tab(5) . 'array_push($vector, $row);' . $this->eol(1);
        $buf .= $this->tab(4) . '}' . $this->eol(2);

        $buf .= $this->tab(4) . 'return array(\'total_pages\'=>$total_pages, \'data\'=>$vector);' . $this->eol(2);

        $buf .= $this->tab(3) . '}catch (Exception $exc) {' . $this->eol(2);
        $buf .= $this->tab(4) . 'echo $exc->getMessage();' . $this->eol(1);
        $buf .= $this->tab(4) . 'return NULL;' . $this->eol(1);
        $buf .= $this->tab(3) . '}' . $this->eol(1);

        $buf .= $this->tab(2) . '}' . $this->eol(1);

        return $buf;
    }
    
    private function build_get_by_id() {

        $buf  = $this->tab(2) . 'public function getById($id) {' . $this->eol(2);

        $buf .= $this->tab(3) . 'try {' . $this->eol(2);

        $buf .= $this->tab(4) . '$sql = "select * from ' . lcfirst($this->tabela) . ' where id = :id";' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt = $this->conexao->prepare($sql);' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt->bindValue(\':id\', $id, PDO::PARAM_INT);' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt->execute();' . $this->eol(2);
        $buf .= $this->tab(4) . 'return $stmt->fetch(PDO::FETCH_OBJ);' . $this->eol(2);

        $buf .= $this->tab(3) . '} catch (Exception $exc) {' . $this->eol(2);
        $buf .= $this->tab(4) . 'echo $exc->getMessage();' . $this->eol(2);
        $buf .= $this->tab(4) . 'return NULL;' . $this->eol(1);
        $buf .= $this->tab(3) . '}' . $this->eol(1);

        $buf .= $this->tab(2) . '}' . $this->eol(2);

        return $buf;
    }
    
    private function build_delete() {

        $buf  = $this->tab(2) . 'public function delete($id) {' . $this->eol(2);

        $buf .= $this->tab(3) . 'try {' . $this->eol(2);

        $buf .= $this->tab(4) . '$sql = "delete from ' . lcfirst($this->tabela) . ' where id = :id";' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt = $this->conexao->prepare($sql);' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt->bindValue(\':id\', $id, PDO::PARAM_INT);' . $this->eol(2);
        $buf .= $this->tab(4) . 'return $stmt->execute();' . $this->eol(2);

        $buf .= $this->tab(3) . '} catch (Exception $exc) {' . $this->eol(2);
        $buf .= $this->tab(4) . 'if ($exc->getCode() == \'23000\'){' . $this->eol(2);
        $buf .= $this->tab(5) . 'showMessages([[\'type\' => \'danger\', \'msg\' => \'Não é possível excluir este registro de ' . $this->tabela . ' porque existem registros relacionadas a este.\']]);' . $this->eol(2);
        $buf .= $this->tab(4) . '} else {' . $this->eol(2);
        $buf .= $this->tab(5) . 'showMessages([[\'type\' => \'danger\', \'msg\' => $exc->getMessage()]]);' . $this->eol(1);
        $buf .= $this->tab(4) . '}' . $this->eol(2);
        $buf .= $this->tab(4) . 'return NULL;' . $this->eol(1);
        $buf .= $this->tab(3) . '}' . $this->eol(1);

        $buf .= $this->tab(2) . '}' . $this->eol(2);

        return $buf;
    }
    
    private function build_insert() {

        $buf  = $this->tab(2) . 'public function insert(' . $this->tabela . 'Vo $' . lcfirst($this->tabela) . 'Vo) {' . $this->eol(2);

        $buf .= $this->tab(3) . 'try {' . $this->eol(2);

        $buf .= $this->tab(4) . '$sql = "insert into ' . lcfirst($this->tabela) . ' values (' . $this->build_insert_clausula_sql() . ')";' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt = $this->conexao->prepare($sql);' . $this->eol(1);
        
        $buf .= $this->montaBind(4);
        
        $buf .= $this->eol(1);
    
        $buf .= $this->tab(4) . 'return $stmt->execute();' . $this->eol(2);

        $buf .= $this->tab(3) . '} catch (Exception $exc) {' . $this->eol(2);
        $buf .= $this->tab(4) . 'echo $exc->getMessage();' . $this->eol(1);
        $buf .= $this->tab(4) . 'return NULL;' . $this->eol(1);
        $buf .= $this->tab(3) . '}' . $this->eol(1);

        $buf .= $this->tab(2) . '}' . $this->eol(2);
        return $buf;
    }
    
    private function build_insert_clausula_sql() {
        $buf = '';
        foreach ($this->listaColunas as $coluna) {
            $buf .= ', :' . $coluna->nome_coluna;
        }
        
        return substr($buf, 2);
    }

    private function build_bindForPagination(){
        
        $buf  = $this->tab(2) . 'protected function bindValuesForPagination($stmt, $filter_data){' . $this->eol(2);
        $buf .= $this->tab(3) . 'if (!empty($filter_data)){' . $this->eol(2);

        foreach ($this->listaColunas as $coluna) {
            $buf .= $this->tab(4) . 'isset($filter_data[\''. $coluna->nome_coluna . '\']) ? $stmt->bindValue(\':' . $coluna->nome_coluna . '\', \'%\'.$filter_data[\''. $coluna->nome_coluna . '\'].\'%\'  , PDO::PARAM_STR)   : NULL;' . $this->eol(1);
        }

        $buf .= $this->tab(3) . '}' . $this->eol(2);
        $buf .= $this->tab(3) . 'return $stmt;' . $this->eol(1);
        $buf .= $this->tab(2) . '}' . $this->eol(2);

        return $buf;
    }





    
    private function build_update() {
        $buf  = $this->tab(2) . 'public function update(' . $this->tabela . 'Vo $' . lcfirst($this->tabela) . 'Vo) {' . $this->eol(2);
        $buf .= $this->tab(3) . 'try {' . $this->eol(2);
        $buf .= $this->tab(4) . '$sql = "update ' . $this->tabela . ' set ' . $this->build_update_clausula_sql() . ' where id = :id";' . $this->eol(1);
        $buf .= $this->tab(4) . '$stmt = $this->conexao->prepare($sql);' . $this->eol(1);
        $buf .= $this->montaBind(4);
        $buf .= $this->tab(4) . '$stmt->execute();' . $this->eol(2);
        $buf .= $this->tab(4) . 'return $' . lcfirst($this->tabela) . 'Vo->getId();' . $this->eol(2);
        $buf .= $this->tab(3) . '} catch (Exception $exc) {' . $this->eol(2);
        $buf .= $this->tab(4) . 'echo $exc->getMessage();' . $this->eol(1);
        $buf .= $this->tab(4) . 'return NULL;' . $this->eol(1);
        $buf .= $this->tab(3) . '}' . $this->eol(1);

        $buf .= $this->tab(2) . '}' . $this->eol(2);
            
        return $buf;
    }
    
    private function build_update_clausula_sql() {
        $buf = '';
        foreach ($this->listaColunas as $coluna) {
            $buf .= ', ' . $coluna->nome_coluna . '= :' . $coluna->nome_coluna;
        }
        
        return substr($buf, 1);
    }
    
    private function montaBind($numTab) {
        $buf = '';
        foreach ($this->listaColunas as $coluna) {
            $buf .= $this->tab($numTab) . '$stmt->bindValue(\':' . $coluna->nome_coluna . '\', $' . lcfirst($this->tabela) . 'Vo->' . $coluna->nome_coluna . ');' . $this->eol(1);
        }
        
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