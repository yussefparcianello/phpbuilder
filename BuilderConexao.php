<?php

class BuilderConexao {
    
    protected $conexao;
    
    function __construct(ConexaoVo $conexao) {
        $this->conexao = $conexao;
    }
    
    public function build() {
        $buf  = $this->build_abre_classe_e_arquivo();
        $buf .= $this->build_declaracao_variaveis();
        $buf .= $this->build_construct();
        $buf .= $this->build_destruct();
        $buf .= $this->build_fecha_classe_e_arquivo();
        
        return $buf;
    }
    
    private function build_abre_classe_e_arquivo(){
        $buf  = $this->tab(0) . '<?php' . $this->eol(1);
        $buf .= $this->tab(1) . 'class ConexaoDao extends PDO {' . $this->eol(2);

        return $buf;
    }
    
    private function build_declaracao_variaveis(){
        $buf  = $this->tab(2) . 'protected $dsn        = "mysql:dbname=' . $this->conexao->banco . ';host=' . $this->conexao->host . ';port=3306";' . $this->eol(1);
        $buf .= $this->tab(2) . 'protected $username   = "' . $this->conexao->login . '";' . $this->eol(1);
        $buf .= $this->tab(2) . 'protected $password   = "' . $this->conexao->senha . '";' . $this->eol(1);
        $buf .= $this->tab(2) . 'static    $connection = NULL;' . $this->eol(2);
        
        return $buf;
    }
    
    private function build_construct(){
        $buf  = $this->tab(2) . 'function __construct() {' . $this->eol(1);
        $buf .= $this->tab(3) . 'try {' . $this->eol(1);
        $buf .= $this->tab(4) . 'if (!isset($this->connection)) {' . $this->eol(1);
        $buf .= $this->tab(5) . '$conn = parent::__construct( $this->dsn , $this->username , $this->password, array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));' . $this->eol(1);
        $buf .= $this->tab(5) . 'self::$connection = $conn;' . $this->eol(1);
        $buf .= $this->tab(5) . 'return self::$connection;' . $this->eol(1);
        $buf .= $this->tab(4) . '}' . $this->eol(1);
        $buf .= $this->tab(3) . '}' . $this->eol(1);
        $buf .= $this->tab(3) . 'catch ( PDOException $e ) {' . $this->eol(1);
        $buf .= $this->tab(4) . 'echo "Conexão falhou. Erro: " . $e->getMessage( );' . $this->eol(1);
        $buf .= $this->tab(4) . 'return false;' . $this->eol(1);
        $buf .= $this->tab(3) . '}' . $this->eol(1);
        $buf .= $this->tab(2) . '}' . $this->eol(2);
        
        return $buf;
    }
    
    private function build_destruct(){
        $buf  = $this->tab(2) . 'function __destruct() {' . $this->eol(1);
        $buf .= $this->tab(3) . 'self::$connection = NULL;' . $this->eol(1);
        $buf .= $this->tab(2) . '}' . $this->eol(1);

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