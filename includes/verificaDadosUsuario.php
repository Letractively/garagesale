<?php
// Incluimos o arquivo de conexão
require_once("Conexao.class.php");

$email = strtolower($_POST["email"]);
$senha = $_POST["senha"];

// Validacoes
if (!$email) {
	echo "Escreva um email";
}
elseif (!eregi("^[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\-]+\.[a-z]{2,4}$", $email)) {
	echo "Digite um email válido";
}
elseif (!$senha) {
	echo "Escreva uma senha";
}
else {

	// verificamos banco de dados
	$con = new Conexao();
	$con->open();
	$result = pg_query("select email, senha, id from usuarios where email = '".$email."'");
	if ($row = pg_fetch_row($result)){
		if (md5($senha) == $row[1]){

			## Verifica se usuario apenas nao esta confirmado
			$sql = "select email, senha, id from usuarios where email = '".$email."' and email_confirmado = 'true'";
			$result2 = pg_query($sql);
			if (pg_num_rows($result2) <= 0 )
			{
				echo "Aguardando confirmação de email";
				return;
			}

			session_start();
			$_SESSION['email'] = $row[0];
			$_SESSION['user'] = $row[2];
			echo true;
		}
		else {
			echo "O usuário ou a senha estão incorretos.";
		}
	}
	else {
		echo "O usuário ou a senha estão incorretos.";
	}
}
?>