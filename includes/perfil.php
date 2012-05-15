<?php
require_once 'UsuariosControl.class.php';
require_once 'MensagensControl.class.php';
require_once 'Util.php';

$ref_usuario = $_GET['user'];

if (!$ref_usuario)
{
	$ref_usuario = UsuariosControl::getLoggedUserId();
}

echo '<p id="titulo_perfil">' . UsuariosControl::getNomeById($ref_usuario) . '</p>';

echo '<a id="link_to_user_produtos" class="special_link" href="index.php?pg=ProdutosControl.class&funcProd=showProdutosList&user=' . $ref_usuario . '" ><img src="images/produtos.png">Produtos</a>';

## se estiver logado mostra o link para mensagens.
if (UsuariosControl::isLogado())
{
		echo '<a id="link_to_user_mensagens" class="special_link" href="?pg=mensagens&user='.$ref_usuario.'" ><img src="images/mail-icon.png">Mensagens</a>';

	if (UsuariosControl::isOwner($ref_usuario))
	{
		echo '<div id="mensagens" >';
		$ref_usuario_destinatario = $ref_usuario;
		## Mostra todas mensagens que o usuario mando para tal usuario
		MensagensControl::showTopicos($ref_usuario_destinatario, null, UsuariosControl::getLoggedUserId());

		$mensagem = new stdClass();
		$mensagem->ref_usuario_destinatario = $ref_usuario_destinatario;
		$mensagem->ref_usuario = UsuariosControl::getLoggedUserId();
		MensagensControl::showNovaMensagem($mensagem);
		echo '</div>';

    }

}
else
{
    echo '<center><img src="images/drawingTeste.svg" width="250px"></center>';    
}


?>
