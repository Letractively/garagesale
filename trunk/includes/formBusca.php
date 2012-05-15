<?php
require_once 'includes/MapControl.php';
require_once 'includes/ProdutosControl.class.php';

if ($_POST['descricao'] ||$_POST['detalhes'] ||$_POST['precoMin']||$_POST['precoMax'] ||  $_POST['categoria'] || $_POST['localInput'] || $_POST['user'])
{
	$descricao = $_POST['descricao'];
	$detalhes  = $_POST['detalhes'];
	$precoMin  = $_POST['precoMin'];
	$precoMax  = $_POST['precoMax'];
	$categoria = $_POST['categoria'];
	$local     = $_POST['localInput'];
	$user      = $_POST['user'];
	$pagina    = $_POST['page'];
}
else
{
	$descricao = $_GET['descricao'];
	$detalhes  = $_GET['detalhes'];
	$precoMin  = $_GET['precoMin'];
	$precoMax  = $_GET['precoMax'];
	$categoria = $_GET['categoria'];
	$local     = $_GET['localInput'];
	$user      = $_GET['user'];
	$pagina    = $_GET['page'];
}
?>

<?php 
if (!$user)
{
?>
<p class="titulo_pagina" >Busca por produtos</p>

	<!-- Formulario de busca -->
	<form id="formBusca" action="javascript:void(0);" method="POST">
<!--	<form id="formBusca" action="index.php?pg=formBusca" method="POST">-->

	<strong>Descricao:</strong>
	<br />
	<input type="text" name="descricao" value="<?=$descricao?>"
		id="descricao" maxlength="100" size="30" /> <br />
	<br />
	
	<strong>Detalhes:</strong>
	<br />
	<input type="text" name="detalhes" value="<?=$detalhes?>"
		id="detalhes" maxlength="500" size="30" /> <br />
	<br />
	
	<strong>Preco minimo:</strong>
	<br />
	<input type="text" name="precoMin" value="<?=$precoMin?>"
		id="precoMin" maxlength="10" size="30" onkeypress='validate(event)' /> <br />
	
	<strong>Preco maximo:</strong>
	<br />
	<input type="text" name="precoMax" value="<?=$precoMax?>"
		id="precoMax" maxlength="10" size="30" onkeypress='validate(event)'/> <br />
	<br />
	
	
	<strong>Categoria:</strong>
	<br />
	<input id="categoria" type="text" name="categoria" value="<?=$categoria?>"
		class="categoriaInput" maxlength="100" size="30" /> <br />
	<br />
	
	<?php MapControl::getHtmlAutocompleteCidade($local);?>
	
	 <input id="buscar" type="submit" value="Buscar" /> 
	 <label	id="statusFormProduto" style="display: none;"></label>
	 </form>

<?php 
}
?> 
 
<div id="resultadoBusca">

<?php

if ( $categoria || $descricao|| $detalhes|| $precoMin|| $precoMax || $local || $user )
{
	ProdutosControl::showProdutosList($descricao,$detalhes,$precoMin,$precoMax,$categoria, $local,$pagina, $user);
}
?>

</div>

<script type="text/javascript" language="javascript">
    $("#formBusca").submit(function() 
    {
        var descricao = $("#descricao").val();
        var detalhes = $("#detalhes").val();
        var precoMin = $("#precoMin").val();
        var precoMax = $("#precoMax").val();
        var categoria = $("#categoria").val();
        var localizacao = $("#localInput").val();
        if (descricao || detalhes || precoMin || precoMax || categoria || localizacao )
        {
        	$("#statusFormProduto").html("<img src='images/spinner.gif' alt='Enviando' />");
            $("#statusFormProduto").fadeIn();
	        $.post('includes/ProdutosControl.class.php?funcProd=showProdutosList', {descricao: descricao, detalhes: detalhes, precoMin: precoMin, precoMax: precoMax, categoria: categoria, localizacao: localizacao }, function(resposta) {
	
	            if (resposta == false) {
	                $("#resultadoBusca").html(resposta);
	            } 
	            else {
	                // Exibe o mapa na div
	                $("#resultadoBusca").fadeIn();
	                $("#resultadoBusca").html(resposta);
	            }
	            $("#statusFormProduto").slideUp();
	        });
        }
    });
</script>
