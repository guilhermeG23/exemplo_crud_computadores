<?php
#Conexao com o banco
$host = "localhost";
$usuario = "teste";
$senha = "teste";
$bd = "Computadores";
$conexao_banco = mysqli_connect($host, $usuario, $senha, $bd);

/*
#Teste de conexao
if ($conexao_banco) {
    echo "Conectado";
} else {
    echo "problemas";
}
*/
