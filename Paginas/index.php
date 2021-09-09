<?php

#Arquivos externos necessarios
include_once "../Banco/banco.php";
include_once "../Chamadas/cabecalho.php";
include_once "../Chamadas/menu.php";

#SQL para realizar a pesquisa padrao e demonstrar todos os valores
$comando = "select * from Relacionamento as R inner join PCGerais as PC on R.PKPC = PC.IDPC inner join Setores as S on R.PKSetor = S.IDSetor";

#Completando a pesquisa caso exista uma procura mais exata
if (isset($_POST["pesquisa"])) {
	$pesquisa = $_POST["pesquisa"];
	$comando = $comando . " where Setor like '%{$pesquisa}%' or Serial like '%{$pesquisa}%' or SO like '%{$pesquisa}%' or Marca like '%{$pesquisa}%'";
} 

#Fechando a pesquisa e iniciando a procura
$comando = $comando . ";";
?>
<hr>
<form action="" method="POST">
	<input type="text" id="pesquisa" name="pesquisa"/>
	<button>Pesquisar</button>
</form>
<hr>

<?php

#Iniciando a procura php + mysql
$retorno = mysqli_query($conexao_banco, $comando);
if (mysqli_num_rows($retorno) > 0) {
?>
<p>Arquivos disponíveis para download da tabela atual: <a href="csv_crud.csv" download>CSV</a>, <a href="json_crud.json" download>JSON</a></p>
<hr>
<table class="tabela">
	<thead>
		<tr>
			<td>Setor</td>
			<td>Serial</td>
			<td>S.O</td>
			<td>Marca</td>
			<td>Opções</td>
		</tr>
	</thead>
	<tbody>
<?php

	#Geranndo os arquivos em tempo do select
	#Fazendo na mao essa parte -> Necessita de melhoras
	$arquivoCSV = "csv_crud.csv"; 
	unlink($arquivoCSV); 
	
	$arquivoJSON = "json_crud.json"; 
	unlink($arquivoJSON); 

	#Arquivos
	$csvATrabalhar = fopen($arquivoCSV, "a+");
	$jsonATrabalhar = fopen($arquivoJSON, "a+");

	#Cabecalho
	fwrite($csvATrabalhar, "PKSetor;Setor;IDPC;Serial;SO;Marca\n");
	fwrite($jsonATrabalhar, "[");

	#laco para demonstracao e geracao dos arquivos
	while($demonstrar=mysqli_fetch_array($retorno)) {	

		#Demonstrativo nas tabelas
		echo "<tr>
			<td>{$demonstrar["Setor"]}</td>
			<td>{$demonstrar["Serial"]}</td>
			<td>{$demonstrar["SO"]}</td>
			<td>{$demonstrar["Marca"]}</td>
			<td><a href='formulario.php?IDPC={$demonstrar["IDPC"]}'>Alterar</a></td>
		</tr>";

		#Gerando o CSV dos valores importantes
		fwrite($csvATrabalhar, "{$demonstrar['PKSetor']};{$demonstrar['Setor']};{$demonstrar['IDPC']};{$demonstrar['Serial']};{$demonstrar['SO']};{$demonstrar['Marca']};\n");

		#Gerando o JSON dos valores importantes
		fwrite($jsonATrabalhar, "{\"PKSetor\": \"{$demonstrar['PKSetor']}\",\"Setor\": \"{$demonstrar['Setor']}\",\"IDPC\": \"{$demonstrar['IDPC']}\",\"Serial\": \"{$demonstrar['Serial']}\",\"SO\": \"{$demonstrar['SO']}\",\"Marca\": \"{$demonstrar['Marca']}\"},");
	}

	#Fechando CSV
	fclose($csvATrabalhar);

	#Fechando JSON
	fwrite($jsonATrabalhar, "{}]");
	fclose($jsonATrabalhar);

} else {
	echo "<p>Nenhum equipamento registrado ou informações inconsistentes</p>";
}
?>
