<!DOCTYPE html>
<?php

	if (isset($_POST['codigo'])) {
		$codigo = $_POST['codigo'];
	} else if (isset($_GET['codigo'])) {
		$codigo = $_GET['codigo'];
	} else {
		$codigo = '';
	}

	include 'disciplinas_pdo.php';
	include 'avaliacao_pdo.php';
	include 'funcoes.php';
	include 'conf.php';
	date_default_timezone_set('America/Sao_Paulo');

	$registros = selectPDO_discaval($codigo, 'avaliacao');

	if($codigo != '') { 
			
		$cod_disciplina = $registros[0][0];
		$disciplina = $registros[0][1];
		$cod_avaliacao = $registros[0][2];
		$conteudo = $registros[0][3];
		$data_inicio = $registros[0][4];
		$data_fim = $registros[0][5];
		$peso = $registros[0][6];
		$embaralhar = $registros[0][7];

		$title = $disciplina." - ".$conteudo;

	} else {
		$title = '';
	}

?>
<html>
<head>
	<title><?php echo $title ?></title> 
	<meta charset="utf-8">

	<!-- Compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

	<!--Import Google Icon Font-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<style type="text/css">
		select { display: block; } /*Tive que colocar, porque por padrão o select estava com display:none por algum motivo*/
	</style>

	<!-- Compiled and minified JavaScript -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</head>
<body>
	<div class="container">
		
		<?php if($codigo != '') { ?>

			<?php if($_SESSION['tipo'] == 'aluno') {
				$reg_disc_alun = selectPDO_discalun($cod_disciplina);
				$alunos_na_disciplina = array();
				for ($i=0; $i < count($reg_disc_alun); $i++) { 
					array_push($alunos_na_disciplina, $reg_disc_alun[$i][0]);
				}
				if(in_array($_SESSION['matricula'], $alunos_na_disciplina)) {
					?>
					<form action="avaliacao_responder.php" method="post">
						<input type="hidden" name="codigo" value="<?php echo $cod_avaliacao; ?>">
						<input type="hidden" name="matricula" value="<?php echo $_SESSION['matricula']; ?>">
						<button type="submit" class="btn waves-effect waves-light">Responder</button>
					</form>
					<?php
				}
			} 

			// Restrição para que o botão "avaliacao_editar.php" seja mostrado apenas a professores
			if($_SESSION['tipo'] == 'professor') {
				if(prof_da_disciplina()) { // função está no fim do arquivo
					echo "<a class=\"btn waves-effect waves-light\" href=\"avaliacao_editar.php?codigo=".$codigo."\">Editar</a>";				
				}
			}

			?>

			<div id="info-avaliacao">
			<?php
				echo "<p style='color:lightgrey'>#".$cod_avaliacao."</p>";
				echo "<p><b>Conteúdo:</b> ".$conteudo."</p>";
				echo "<p><b>Disponível entre</b> ".$data_inicio." <b>e</b> ".$data_fim."</p>";
				echo "<p><b>Peso: </b>".$peso."</p>";
				echo "<p><b>Embaralhar questões:</b> ".$embaralhar."</p>";
			?>
			</div>
			
			<hr/>			
			
			<div id="questoes">
				<?php
					$questoes = selectPDO_avalques_all($codigo); //all = todas as questões, incluindo as alternativas e as questões discursivas
					mostrar_questoes($questoes); //função em funcoes
				?>
			</div>

		<?php } else { ?>

			<div id="erro_codigo">
				<p><b>Erro: </b> a página não recebeu nenhum código. Adicione, no final da URL, <code>?codigo=[codigo da disciplina]</code></p>
			</div>

		<?php } ?>
	</div>

	<!--  Scripts-->
	<script src="assets/js/jquery-2.1.1.min.js"></script>
	<script src="assets/js/materialize.min.js"></script>
	<script src="assets/js/init.js"></script>
	
	<?php

	function prof_da_disciplina() {
		$pdo = new PDO('mysql:host=localhost;dbname=prove_sistema_avaliacao',"root","");
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdo -> exec("SET CHARACTER SET utf8");

		try {
			// Primeiro se consulta o código da disciplina da qual a avaliação faz parte...
			$query1 = "SELECT Disciplina_Codigo_Disciplina FROM ".$GLOBALS['tb_avaliacoes']." WHERE Codigo_Avaliacao = ".$GLOBALS['codigo'];
			//var_dump($query1);

			$consulta = $GLOBALS['pdo']->query($query1);

			for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
				$cod_disciplina = $linha['Disciplina_Codigo_Disciplina'];
			}

			// ... então se consulta se o professor está nessa disciplina
			$query2 = "SELECT P.Matricula ";
			$query2 .= " FROM Professores P, Disciplinas D, Professores_has_Disciplina DP "; 
			$query2 .= " WHERE P.Matricula = DP.Professores_Matricula ";
			$query2 .= " AND D.Codigo_Disciplina = DP.Disciplina_Codigo_Disciplina ";
			$query2 .= " AND D.Codigo_Disciplina = ".$GLOBALS['codigo'];
			
			$consulta = $GLOBALS['pdo']->query($query2);

			$matriculas = array();
			for ($i = 0; $linha = $consulta->fetch(PDO::FETCH_ASSOC); $i++) {
				array_push($matriculas, $linha['Matricula']);
			}

			if (in_array($_SESSION['matricula'], $matriculas)) {
				return 1;
			} else {
				return 0;
			}
		} catch (PDOException $e) {
			echo "Erro: ".$e->getMessage();
		}
	}

	?>
</body>
</html>