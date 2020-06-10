<?php
    //instancia um objeto EsquemaVo
    require_once 'ConexaoVo.php';
    $conexao = new ConexaoVo();
        
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        //extrai variaveis do post
        extract($_POST);
        
        //popula o objeto
            $conexao->banco = $banco;
            $conexao->host = $host;
            $conexao->login = $login;
            $conexao->senha = $senha;




        //gera o arquivo de conexao
            require_once 'BuilderConexao.php';
            $builderConexao = new BuilderConexao($conexao);
            $buf = $builderConexao->build();

            //define o nome e diretório do arquivo a ser gerado
            $nome_arquivo = 'ConexaoDao.php';
            $dir = 'dao';

            //escreve o arquivo no disco
            include 'EscreveArquivos.php';
        
        

        
        //conecta no banco
            $dsn = 'mysql:dbname=' . $banco . ';host=' . $host;
            $con = new PDO($dsn, $login, $senha);




        //busca a lista de tabelas
            $sql =  "SELECT table_name FROM information_schema.tables WHERE table_schema = :banco";
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':banco', $banco, PDO::PARAM_STR);
            $stmt->execute();

            $lista_tabelas = array();
            while ($linha = $stmt->fetch(pdo::FETCH_OBJ)) {
                array_push($lista_tabelas, $linha);
            }

            


        //busca a lista de colunas de cada tabela
            foreach ($lista_tabelas as $tabela) {
                
                $lista_colunas = [];

                $sql = "SELECT column_name as nome_coluna FROM information_schema.columns WHERE table_name = :table_name and table_schema = :banco";

                $stmt = $con->prepare($sql);
                $stmt->bindValue(':banco', $banco, PDO::PARAM_STR);
                $stmt->bindValue(':table_name', $tabela->TABLE_NAME, PDO::PARAM_STR);
                $stmt->execute();

                while ($linha = $stmt->fetch(pdo::FETCH_OBJ)) {
                    array_push($lista_colunas, $linha);
                }

                


                //gera as classes model
                        require_once 'BuilderModel.php';
                        $builderConexao = new BuilderModel($tabela->TABLE_NAME, $lista_colunas);
                        $buf = $builderConexao->build();

                        //define o nome e diretório do arquivo a ser gerado
                        $nome_arquivo = ucfirst($tabela->TABLE_NAME) . 'Vo.php';
                        $dir = 'vo';

                        //escreve o arquivo no disco
                        include 'EscreveArquivos.php';


                

                //gera as classes dao
                        require_once 'BuilderDao.php';
                        $builderDao = new BuilderDao($tabela->TABLE_NAME, $lista_colunas);
                        $buf = $builderDao->build();

                        //define o nome e diretório do arquivo a ser gerado
                        $nome_arquivo = ucfirst($tabela->TABLE_NAME) . 'Dao.php';
                        $dir = 'dao';

                        //escreve o arquivo no disco
                        include 'EscreveArquivos.php';


                

                //gera as classes bal
                        require_once 'BuilderBal.php';
                        $builderBal = new BuilderBal($tabela->TABLE_NAME);
                        $buf = $builderBal->build();

                        //define o nome e diretório do arquivo a ser gerado
                        $nome_arquivo = ucfirst($tabela->TABLE_NAME) . 'Bal.php';
                        $dir = 'bal';

                        //escreve o arquivo no disco
                        include 'EscreveArquivos.php';


            

                //gera as ui/list
                        require_once 'BuilderList.php';
                        $builderList = new BuilderList($tabela->TABLE_NAME, $lista_colunas);
                        $buf = $builderList->build();

                        //define o nome e diretório do arquivo a ser gerado
                        $nome_arquivo = 'index-' . $tabela->TABLE_NAME . '.php';
                        $dir = 'ui';

                        //escreve o arquivo no disco
                        include 'EscreveArquivos.php';


            

                //gera as ui/form
                        require_once 'BuilderForm.php';
                        $builderForm = new BuilderForm($tabela->TABLE_NAME, $lista_colunas);
                        $buf = $builderForm->build();

                        //define o nome e diretório do arquivo a ser gerado
                        $nome_arquivo = 'form-' . $tabela->TABLE_NAME . '.php';
                        $dir = 'ui';

                        //escreve o arquivo no disco
                        include 'EscreveArquivos.php';
            }
    }
?>

<h1>Conexão com Base de Dados</h1>

<form role="form" method="POST" action="">
    
    <br/>Banco:<input type="text" name="banco" value="<?=$conexao->banco?>"/>
    
    <br/>Host:<input type="text" name="host" value="<?=$conexao->host?>"/>
    
    <br/>Login:<input type="text" name="login" value="<?=$conexao->login?>"/>
    
    <br/>Senha:<input type="text" name="senha" value="<?=$conexao->senha?>"/>
    
    <br/><input type="submit" value="Próximo"/>
    
</form>