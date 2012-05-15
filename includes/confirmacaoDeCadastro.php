<?php

require_once 'UsuariosControl.class.php';

$hash = $_GET['hash'];

if (!$hash)
{
	echo 'Email nao confirmado. o hash nao existe.';
	return;
}
else
{
	if ($email = UsuariosControl::confirmEmail($hash, PALAVRA_CHAVE_EMAIL)){
		echo 'Email confirmado com sucesso!';
		
		$_SESSION['email'] = $email;
		$_SESSION['user'] = UsuariosControl::getId($email);
	}
	else
	{
		echo 'Email nao confirmado.';
	}
}





