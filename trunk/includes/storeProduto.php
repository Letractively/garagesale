<?php

require_once 'CategoriasControl.class.php';
require_once 'UsuariosControl.class.php';
require_once 'ProcuraControl.class.php';
require_once 'ProdutosControl.class.php';
require_once 'MensagensControl.class.php';
require_once 'Util.php';

if ($_POST){
	// Recuperamos os valores dos campos através do método POST
	$produto = new stdClass();
	$produto->id 			= $_POST['ref_produto'];
	$produto->descricao 	= addslashes($_POST['descricao']);
	$produto->detalhes	 	= addslashes($_POST['detalhes']);
	$produto->preco 		= addslashes($_POST['preco']);
	$produto->ref_localidade= $_POST['localizacao'];
	$categoriaDescricao 	= $_POST['categoria'];

	## Verificar se eh o dono do produto que esta editando
	if ($produto->id && ProdutosControl::getProduto($produto->id)->ref_usuario != UsuariosControl::getLoggedUserId())
	{
		semPermissao();
		return false;
	}

	if (empty($produto->descricao)) {
		showWarning('Campo <b>descrição</b> é obrigatório.');
	}
	elseif (strlen($produto->descricao) > 150) {
		showWarning('O nome deve ter no máximo 150 caracteres');
	}
	elseif (empty($categoriaDescricao)) {
		showWarning('Campo <b>categoria</b> é obrigatório.');
	}
	else {

		$produto->ref_usuario = UsuariosControl::getLoggedUserId();

		## Se nao existe a categoria, cria uma nova
		if (!CategoriasControl::exists($categoriaDescricao)){
			 
			$categoria->descricao = $categoriaDescricao;
			$categoria->ref_categoria_parent = 1;
			 
			if (!CategoriasControl::add($categoria)){
				showError('Erro ao adicionar categoria');
			}
		}

		if (!$produto->ref_categoria = CategoriasControl::getId($categoriaDescricao)){
			showError('Erro ao obter id de categoria');
		}

		if (ProdutosControl::add($produto))
		{
			$refs_usuarios = ProcuraControl::getUsuariosProcuras($categoriaDescricao);
			if ($refs_usuarios)
			{
				foreach ($refs_usuarios as $ref_usuario)
				{
					$mensagem = new stdClass();
					$mensagem->ref_usuario = UsuariosControl::getLoggedUserId();
					$mensagem->ref_usuario_destinatario = $ref_usuario;
					$mensagem->mensagem = 'Produto cadastrado: [mensagem automatica] Ola, este usuario cadastrou um produto da categoria '.$categoriaDescricao;
					MensagensControl::add($mensagem);
				}
			}
			echo true;
		}
		else {
			showError('Não foi possível inserir o produto no momento.');
		}
	}
}
?>