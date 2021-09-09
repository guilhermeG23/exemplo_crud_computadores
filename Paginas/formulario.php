<?php

#Arquivos necessarios
include_once "../Banco/banco.php";
include_once "../Chamadas/cabecalho.php";
include_once "../Chamadas/menu.php";

#Registrar ou atualizar os ativo 
#So vai realizar alguma operacao se todos os campos existirem no POST -> Tem todos os valores para se trabalhar
if (isset($_POST["serial"]) && isset($_POST["SO"]) && isset($_POST["marca"]) && isset($_POST["setor"])) {

    #Capturandos os valores POST
    $SO = $_POST["SO"];
    $serial = $_POST["serial"];
    $marca = $_POST["marca"];
    $setor = $_POST["setor"];
    $IDPC = $_POST["IDPC"];

    #Primeiro realizando a pesquisa e confirmando se existe o valor a ser trabalho
    $pesquisar = mysqli_query($conexao_banco, "select * from PCGerais where IDPC = '{$IDPC}';");

    #Conferindo existencia
    if (mysqli_num_rows($pesquisar) > 0) {
    	    #Ele existe, entao sera atualizado seu estado	    
	    mysqli_query($conexao_banco, "update PCGerais set SO = '{$SO}', Marca = '{$marca}', Serial = '{$serial}' where IDPC = '{$IDPC}';");
	    mysqli_query($conexao_banco, "update Relacionamento set PKSetor = '{$setor}' where PKPC = '{$IDPC}';");
	    echo "<hr><p>Alterado registro!</p>";
    } else {
	    #Ele nao existe, entao sera inserido o novo valor
	    mysqli_query($conexao_banco, "insert into PCGerais values (0, '{$serial}', '{$SO}', '{$marca}');");
	    #Pegando o ultimo registrado e usando ele para completar a tabela de relacionamento
	    $IDPC = mysqli_query($conexao_banco, "select IDPC from PCGerais order by IDPC desc limit 1;");
	    $IDPCAUsar = "";
            while($procuraIDPC=mysqli_fetch_array($IDPC)) {
	    	$IDPCAUsar = $procuraIDPC['IDPC'];
	    }
	    mysqli_query($conexao_banco, "insert into Relacionamento values ('{$IDPCAUsar}', '{$setor}');");
	    echo "<hr><p>Novo ativo registrado!</p>";
    
    }

} 

#Deletar todo o ativo
if (isset($_POST['deletar'])) {
	$deletar  = $_POST['deletar'];
	#Deletando primeiro o relacionamento para nao conflitar na outra tabela
	mysqli_query($conexao_banco, "delete from Relacionamento where PKPC = '{$deletar}';");
	#Deletando o ativo dos PCs
	mysqli_query($conexao_banco, "delete from PCGerais where IDPC = '{$deletar}';");
	echo "<hr><p>Deletado registro!</p>";
}


#Buscando valores para o update
$serial = "";
$SO = "";
$marca = "";
$setor = "";
$IDPC = "";
#Se o IDPC existe no GET, significa que esse cara existe no banco, procure e preencha na tabela para facilitar a atualizacao
if (isset($_GET["IDPC"])) {	
	$IDPC = $_GET["IDPC"];
	$pesquisa = mysqli_query($conexao_banco, "select R.PKSetor, S.Setor, P.Serial, P.SO, P.Marca from Relacionamento as R inner join Setores as S on R.PKSetor = S.IDSetor inner join PCGerais as P on P.IDPC = R.PKPC where R.PKPC = '{$IDPC}';");
	#Capturando os valores e armazenando para ser usado nas variaveis do formulario
	while($resultado=mysqli_fetch_array($pesquisa)) {
		$serial = $resultado["Serial"];
		$SO = $resultado["SO"];
		$marca = $resultado["Marca"];
		$setor = "<option value='{$resultado['PKSetor']}'>{$resultado['Setor']}</option>";
	}	
}

?>
<!--formulario html-->
<div class="container">
    <form action="" method="POST">
	<hr>
	<input type="hidden" value="<?=$IDPC;?>" id="IDPC" name="IDPC"/>
	<label for="serial">Serial: </label>
	<input type="text" id="serial"  value="<?=$serial;?>" name="serial" required/>
        <br>
	<label for="SO">Sistema operacional: </label>
        <input type="text" id="SO" value="<?=$SO;?>" name="SO" required/>
        <br>
        <label for="marca">Marca do equipamento: </label>
        <input type="text" id="marca"  value="<?=$marca;?>" name="marca" required/>
	<hr>	
        <label for="setor">Relacionar a departamento: </label>
	<select id="setor" name="setor" required>
<?php	
		#Valor que tem chance ou nao de existir
		echo $setor;
?>
		<option default/>
<?php
	#Buscando valores na tabela para preencher o campo de option
	$setores = mysqli_query($conexao_banco, "select * from Setores;");
	while($setor=mysqli_fetch_array($setores)) {
		echo "<option value='{$setor['IDSetor']}'>{$setor['Setor']}</option>";
	}
?>
	</select>
	<hr>
	<?php
	#Aqui só altera o texto que vai aparecer no botao se existir um get ou nao
	if (!isset($_GET["IDPC"])) {	
		echo "<button>Registrar</button>";
	} else {
		echo "<button>Atualizar</button>";
	}
	?>
	<hr>
    </form>
<?php
#So da a opcao de deletar se existir o GET, que comprova que a registro do valor
if (isset($_GET["IDPC"])) {	
?>
     <form action="" method="POST">
    	<input type="hidden" id="deletar" name="deletar" value="<?=$IDPC;?>">
    	<button>Deletar</button>
    </form>
<?php
     }	
?>

   </div>
</div>
