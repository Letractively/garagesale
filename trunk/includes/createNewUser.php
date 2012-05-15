<?php

require_once 'UsuariosControl.class.php';
require_once 'MailGarage.class.php';
require_once 'Util.php';

// Realiza insert de usuário recebido pelo post
if ($_POST){

	$usuario = new stdClass();

	// Recuperamos os valores dos campos através do método POST
	$usuario->nome = addslashes($_POST["nome"]);
	$usuario->email = strtolower($_POST["email"]);
	$usuario->senha = $_POST["senha"];

	// Verifica se o nome foi preenchido
	if (empty($usuario->nome)) {
		showWarning('Escreva seu nome');
	}
	// Verifica se o email é válido
	elseif (!eregi("^[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\-]+\.[a-z]{2,4}$", $usuario->email)) {
		showWarning('Digite um email válido');
	}
	// Verifica se o email ja existe
	elseif (UsuariosControl::exists($usuario->email)) {
		showWarning('Este email já está cadastrado');
	}
	// Verifica se a senha foi digitada
	elseif (empty($usuario->senha)) {
		showWarning('Escreva uma senha');
	}
	// Verifica se a mensagem nao ultrapassa o limite de caracteres
	elseif (strlen($usuario->nome) > 500) {
		showWarning('O nome deve ter no máximo 50 caracteres');
	}
	// Verifica se a mensagem nao ultrapassa o limite de caracteres
	elseif (strlen($usuario->senha) > 50) {
		showWarning('A senha deve ter no máximo 50 caracteres');
	}
	// Verifica se a mensagem nao ultrapassa o limite de caracteres
	elseif (strlen($usuario->email) > 100) {
		showWarning('O email deve ter no máximo 100 caracteres');
	}
	// Se não houver nenhum erro
	else {
		// Se inserido com sucesso
		if (UsuariosControl::add($usuario)) 
		{
			
			$to = $usuario->email;
			$subject = 'Confirmar email';
			$palavra_chave_diferenciar_md5 = 'frangoassado';
			$hash = md5($usuario->email . $palavra_chave_diferenciar_md5);
			$body = 'Este é um email gerado automaticamente, não responda. Obrigado por cadastrar-se no Garage Sale.
            		Clique no link abaixo para confirmar seu cadastro.<br><br>';
			$body .= '<a href="localhost/site/index.php?pg=confirmacaoDeCadastro&hash='.$hash.'" >Confirmacao</a>';

			if (SendMailGarage($to, $subject, $body))
			{
				echo true;
			}
			else 
			{
				showError('Erro ao enviar email de confirmacao de cadastro');
			}
		}
		// Se houver algum erro ao inserir
		else {
			showError("Não foi possível inserir o usuario no momento.");
		}
	}
}
