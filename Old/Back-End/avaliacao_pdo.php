<?php

include 'conf.php';

if (isset($_POST['acao'])) $acao = $_POST['acao'];
else if (isset($_GET['acao'])) $acao = $_GET['acao'];
else $acao = '';

require_once "autoload.php";

#### Construção do objeto ##########################################################################

if (!$acao == '') {	
	echo "Ação: ".$acao."<br>";
	
	$aluno = new Aluno;
	if(isset($_POST['matricula'])) $aluno->setMatricula($_POST['matricula']);
	echo $aluno;
	//echo "Senha: ".$_POST['senha'];
}

#### PDO ###########################################################################################

$pdo = new PDO('mysql:host=localhost;dbname=prove_sistema_avaliacao',"root","");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
	switch ($acao) {
		case 'cadastrar':
			insertPDO();
			break;
		case 'editar':
			updatePDO();
			break;
		case 'deletar':
			deletePDO();
			break;
	}	
} catch (PDOException $e) {
	echo "Erro: ".$e->getMessage();
}

#### Funções ###############################################

function selectPDO($criterio = 'Nome', $pesquisa = '') {
	try {
		$sql = "SELECT * FROM ".$GLOBALS['tb_alunos']." WHERE ".$criterio." ";
		if ($criterio == 'Nome' || $criterio = 'Matricula') 
			$sql .= " like '%".$pesquisa."%'";
		else $sql .= ' = '.$pesquisa;
		$sql .= ";";
		//var_dump($sql); echo "<br>";

		$consulta = $GLOBALS['pdo']->query($sql);

		$registros = array();

		for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
			$registros[$i] = array();
			array_push($registros[$i], $linha['Matricula']);
			array_push($registros[$i], $linha['Senha']);
			array_push($registros[$i], $linha['Nome']);
			array_push($registros[$i], $linha['Data_Nascimento']);
			array_push($registros[$i], $linha['Ultimo_Login']);
			array_push($registros[$i], $linha['Email']);
		}

		return $registros;
	} catch (PDOException $e) {
		echo "Erro: ".$e->getMessage();
	}
}

function printSelectPDOAsTable ($registros) {
	# $registros deve ser o retorno da função selectPDO()
	# ou seja, poderia-se chamar essa função assim: prinSelectPDOasTable(selectPDO());
	
	echo "<table class='highlight centered responsive-table'>
	<thead class='black white-text'>
	<tr>
		<th>Matrícula</th>
		<th>Senha</th>
		<th>Nome</th>
		<th>Data de nascimento</th>
		<th>Último login</th>
		<th>E-mail</th>
	</tr>
	</thead>
	<tdbody>";

	for ($i=0; $i < count($registros); $i++) {
		echo "<tr>";
		for ($j=0; $j < count($registros[$i]); $j++) { 
			echo "<td>".$registros[$i][$j]."</td>";
		}
		echo "<tr>";
	}
	echo "</tbody>
	</table>";

}

function insertPDO() {
	$stmt = $GLOBALS['pdo']->prepare("INSERT INTO ".$GLOBALS['tb_alunos']." (Matricula, Senha, Nome, Data_Nascimento, Ultimo_Login, Email) VALUES (:Matricula, :Senha, :Nome, :Data_Nascimento, :Ultimo_Login, :Email)");

	$stmt->bindParam(':Matricula', $matricula);
	$stmt->bindParam(':Senha', $senha);
	$stmt->bindParam(':Nome', $nome);
	$stmt->bindParam(':Data_Nascimento', $data_nascimento);
	$stmt->bindParam(':Ultimo_Login', $ultimo_login);
	$stmt->bindParam(':Email', $email);

	$matricula = $GLOBALS['aluno']->getMatricula();
	$senha = $GLOBALS['aluno']->getSenha();
	$nome = $GLOBALS['aluno']->getNome();
	$data_nascimento = $GLOBALS['aluno']->getDataNascimento();
	$ultimo_login = $GLOBALS['aluno']->getUltimoLogin();
	$email = $GLOBALS['aluno']->getEmail();

	$stmt->execute();

	echo "Linhas afetadas: ".$stmt->rowCount();
}

function updatePDO() {
	$stmt = GLOBALS['pdo']->prepare("UPDATE ".$GLOBALS['tb_alunos']." SET Matricula = :Matricula, Senha = :Senha, Nome = :Nome, Data_Nascimento = :Data_Nascimento, Ultimo_Login = :Ultimo_Login, Email = :Email");

	$stmt->bindParam(':Matricula', $matricula);
	$stmt->bindParam(':Senha', $senha);
	$stmt->bindParam(':Nome', $nome);
	$stmt->bindParam(':Data_Nascimento', $data_nascimento);
	$stmt->bindParam(':Ultimo_Login', $ultimo_login);
	$stmt->bindParam(':Email', $email);

	$matricula = $GLOBALS['aluno']->getMatricula();
	$senha = $GLOBALS['aluno']->getSenha();
	$nome = $GLOBALS['aluno']->getNome();
	$data_nascimento = $GLOBALS['aluno']->getDataNascimento();
	$ultimo_login = $GLOBALS['aluno']->getUltimoLogin();
	$email = $GLOBALS['aluno']->getEmail();

	$stmt->execute();

	echo "Linhas afetadas: ".$stmt->rowCount();
}

function deletePDO() {
	$stmt = $GLOBALS['pdo']->prepare("DELETE FROM ".$GLOBALS['tb_alunos']." WHERE Matricula = :Matricula");

	$stmt->bindParam(':Matricula', $matricula);

	$matricula = $GLOBALS['aluno']->getMatricula();

	$stmt->execute();

	echo "Linhas afetadas: ".$stmt->rowCount();
}

?>