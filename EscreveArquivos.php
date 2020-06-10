<?php
//define a url do diretorio destino
$url_diretorio_destino = $_SERVER['DOCUMENT_ROOT'] . '/arquivosGerados/' . $dir . '/';

//cria diretorio caso nao exista
if (!file_exists($url_diretorio_destino)){
    mkdir($url_diretorio_destino, 0770, true);
}

//define o caminho absoluto do arquivo a ser criado
$caminho_absoluto = $url_diretorio_destino . $nome_arquivo;

//gera o arquivo de texto
$handle = fopen($caminho_absoluto, 'w');                        
fwrite($handle, $buf);                                          
fclose($handle);                                                

//exibe uma mensagem de retorno
echo 'Arquivo "' . $nome_arquivo . '" gerado.<br/>';
