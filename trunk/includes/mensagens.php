<?php
require_once 'UsuariosControl.class.php';
require_once 'MensagensControl.class.php';
require_once 'Util.php';

$ref_usuario = $_GET['user'];

if (!$ref_usuario)
{
	$ref_usuario = UsuariosControl::getLoggedUserId();
}

if (UsuariosControl::isOwner($ref_usuario))
{
	echo '<div id="mensagens" >';

	MensagensControl::showTopicos($ref_usuario);

	$mensagem = new stdClass();
	$mensagem->ref_usuario_destinatario = $ref_usuario;
	$mensagem->ref_usuario = UsuariosControl::getLoggedUserId();
	MensagensControl::showNovaMensagem($mensagem);
	echo '</div>';
}
else
{
	MensagensControl::showTopicos(null, null, $ref_usuario);
}