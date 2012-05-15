<?php 

require_once 'ProdutosControl.class.php';
require_once 'LocalidadesControl.class.php';
require_once 'UsuariosControl.class.php';

## nao permitir acesso a esta pagina para users nao logados
if (!UsuariosControl::isLogado())
{
	semPermissao();
	return;
}


if ($ref_produto = $_GET['prod'])
{
	$produto = ProdutosControl::getProduto($ref_produto);

	## nao permitir usuarios nao donos do produto acessarem o formulario do produto
	if (!UsuariosControl::isOwner($produto->ref_usuario))
	{
		semPermissao();
        return;
	}
	
	$place = LocalidadesControl::getPlaceOfId($produto->ref_localidade);
}

?>

<p class="titulo_pagina" >Cadastro de Produto</p>

<!-- Formulario do produto -->
<form id="formProduto" action="javascript:void(0);" method="POST">

<input type="hidden" name="id" id="ref_produto" value="<?php echo $produto->id?>" maxlength="100" size="30" /> <br />

<strong>Descricao:</strong>
<br />
<input type="text" name="descricao" id="descricao" value="<?php echo $produto->descricao?>" maxlength="100" size="30" /> <br />
<br />

<strong>Detalhes:</strong>
<br />
<textarea rows="2" cols="40" name="detalhes" id="detalhes" ><?php echo $produto->detalhes?></textarea><br />
<br />

<strong>Preco:</strong>
<br />
<input type="text" name="preco" id="preco" onkeypress='validate(event)' value="<?php echo $produto->preco?>" maxlength="10" size="30" /> <br />
<br />


<strong>Categoria:</strong> 
<br />
<input type="text" name="categoria" id="categoria" class="categoriaInput" value="<?php echo $produto->categoria?>" maxlength="100" size="30" /> <br />
<br />


<?php
include 'includes/MapControl.php';

MapControl::getHtmlSearchPlace($place);

?> 

<input id="enviar" type="submit" value="Enviar" /> <br>
<div id="statusFormProduto" style="display: none;"></div>

</form>

<script type="text/javascript" language="javascript">
	$("#formProduto").submit(function() {

		$("#enviar").hide();
		
		$("#statusFormProduto").html("<img src='images/spinner.gif' alt='Enviando' />");
		$("#statusFormProduto").slideDown();
		var ref_produto = $("#ref_produto").val();
		var descricao = $("#descricao").val();
		var detalhes = $("#detalhes").val();
		var preco = $("#preco").val();
		var categoria = $("#categoria").val();
		var localizacao = $("#localizacao").val();
		$.post('includes/storeProduto.php', {ref_produto: ref_produto, descricao: descricao,detalhes: detalhes,preco: preco, categoria: categoria, localizacao: localizacao }, function(resposta) {

			if (resposta == true) {

				// Se estiver editando um produto, volta pra os detalhes, se tiver criando vai pra lista de produtos
				if (ref_produto)
				{
					window.location="index.php?pg=detailsProduto&prod="+ref_produto+"&saved=true";
				}
				else 
				{
				    window.location="index.php?pg=ProdutosControl.class&funcProd=showProdutosList&user=<?php echo UsuariosControl::getLoggedUserId()?>&saved=true";
				}
            } 
            else {
            	$("#enviar").show();
				$("#statusFormProduto").fadeIn();
				$("#statusFormProduto").html(resposta);
				
            }
		});
	});
</script>
