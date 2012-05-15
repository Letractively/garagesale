<?php

require_once "CategoriasControl.class.php";

Class Script{
	
	static function basico()
	{
		?>
		<script type="text/javascript">
function validate(evt) {
	  var theEvent = evt || window.event;
	  var key = theEvent.keyCode || theEvent.which;
	  key = String.fromCharCode( key );
	  var regex = /[0-9]|\./;
	  if( !regex.test(key) ) {
	    theEvent.returnValue = false;
	    if(theEvent.preventDefault) theEvent.preventDefault();
	  }
	}

function getNow()
{
    data = new Date();
    dia = data.getDate();
    if (dia < 10)
    {
     dia = "0"+dia;
    }
    mes = data.getMonth() + 1;
    if (mes < 10)
    {
     mes = "0"+mes;
    }
    ano = data.getFullYear();
    return dia+"/"+mes+"/"+ano;
}
</script>

		
		<?php 
	}
	
	
	static function autocompleteCategorias(){
		?>
<!-- Script jquery-ui que auto-completa -->
<script>
	$(function() {
        var availableTags = [<?=CategoriasControl::getCategorias()?>];    
	    $( ".categoriaInput" ).autocomplete({
	        // caracteres a digitar até que começa o autocomplete
	        minLength: 1,
		    source: availableTags
	    });

	});
</script>
<?php 
		
	}

	static function fotoLightBox()
	{
?>
    <!-- Include Lightbox (Production) -->
    <!--<script type="text/javascript" src="js/jquery.lightbox.min.js"></script>-->
    <!-- Include Lightbox (Development/Testing) -->
    <script type="text/javascript" src="js/jquery.lightbox.js"></script>
    <!-- Include Lightbox (Production, No linkback) -->
    <!--<script type="text/javascript" src="js/jquery.lightbox.min.js?show_linkback=false"></script>-->
    <!-- Include Lightbox (Production, Manual baseurl) -->
    <!--<script type="text/javascript" src="js/jquery.lightbox.min.js?baseurl=http://www.your.com/baseurl/jquery-lightbox/"></script>-->
    <!-- Include Lightbox (Production, Disable scrolling) -->
    <!--<script type="text/javascript" src="js/jquery.lightbox.min.js?scroll=disabled"></script>-->
    <!-- Include Lightbox (Production, colorBlend forced support) -->
    <!--<script type="text/javascript" src="js/jquery.lightbox.min.js?colorBlend=true"></script>-->
    <!-- Include Lightbox (Production, No linkback + Disable scrolling) -->
    <!--<script type="text/javascript" src="js/jquery.lightbox.min.js?show_linkback=false&amp;scroll=disabled"></script>-->

<?php 
		
	}
	
	static function dialogUpload()
	{
        ?>
<script>
//Dialog            
$('#dialogUploadFoto').dialog({
    autoOpen: false,
    width: 500,
    buttons: {
        "Cancel": function() { 
            $(this).dialog("close"); 
        } 
    }
});

// Dialog Link
$('#botao-adicionar').click(function(){
    $('#dialogUploadFoto').dialog('open');
    return false;
});
</script>
        <?php

    }
	
    static function dialogDeleteFoto($foto)
    {
        ?>
<script>
//Dialog            
$('#dialogDeleteFoto<?=$foto?>').dialog({
    autoOpen: false,
    width: 500,
    buttons: {
        "Cancel": function() { 
            $(this).dialog("close"); 
        } 
    }
});

// Dialog Link
$('#deletar-foto<?=$foto?>').click(function(){
    $('#dialogDeleteFoto<?=$foto?>').dialog('open');
    return false;
});
</script>
        <?php
    }
    
static function dialogExcluirProduto()
	{
        ?>
<script>
//Dialog            
$('#dialogExclusaoProduto').dialog({
    autoOpen: false,
    width: 500,
    buttons: {
        "Cancel": function() { 
            $(this).dialog("close"); 
        } 
    }
});

// Dialog Link
$('#botao-excluir-produto').click(function(){
    $('#dialogExclusaoProduto').dialog('open');
    return false;
});
</script>
        <?php
	}
	
static function dialogAddProcura()
	{
        ?>
<script>
//Dialog            
$('#dialogAddProcura').dialog({
    autoOpen: false,
    width: 500,
    buttons: {
        "Cancel": function() { 
            $(this).dialog("close"); 
        } 
    }
});

// Dialog Link
$('#botao-add-procura').click(function(){
    $('#dialogAddProcura').dialog('open');
    return false;
});
</script>
        <?php
	}
	
	static function carregaConteudo(){
	?>
<!-- carrega conteudo do tiago -->
<script type="text/javascript">
	function carregaConteudo(link, div){
		var $j = jQuery.noConflict();
		for(i=0; i< link.length; i++){ if(link[i] == "?"){	var encontrou = true; } }
		if(encontrou) var randobj = link +"&timestamp="+ new Date().getTime();
		else var randobj = link +"?timestamp="+ new Date().getTime();
		//alert(randobj);
		$j.get(randobj,'',function(data){
			$j(div).html(data);
		});
	}
</script>
	<?php 	
	}
	
	static function gmap(){
	?>
<!-- gmap: 	carrega os scripts do gmap, 
			inclue css do gmap, 
			inclue o script de geracao do mapa
			-->
<script type="text/javascript"	src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAVobc0vtTDtGqnxd8xzLt6xRCR_-gSGnTuAyUKw8sfdF5-8FpWBTYUPWUYfM31vqsFb40r4fCFw1WWw"></script>
<script type="text/javascript" src="js/jquery.gmap-1.1.0-min.js"></script>
<style type="text/css">
#mapa {
	height: 250px;
	width: 70%;
	border-style: solid;
}
</style>

<script type="text/javascript" >
<?php

include "includes/geraMapa.class.php";
geraMapa::criaScriptMapa();

?>
</script>
	
	
	<?php 
	}
	
	static function sair(){
		?>
<script type="text/javascript">
$("#sair").click(function() {
	$.ajax({
		  url: "includes/logout.php",
		  context: document.body,
		  success: function(){
			window.location="index.php";
		  }
	});
});
</script>
	<?php 	
	}
	
	static function signin(){
	?>
<script type="text/javascript" language="javascript">
$(function() {
	// Quando o formulario for enviado, essa funcao e chamada
	$("#formulario").submit(function() {
		// Colocamos os valores de cada campo em uma variavel para facilitar a manipulacao
		var email = $("#email").val();
		var senha = $("#senha").val();
		
		// Exibe mensagem de carregamento
		$("#status").html("<img src='images/ajax-loader.gif' alt='Enviando' />");
		// Fazemos a requisicao ajax com o arquivo enviaDadosUsuario.php e enviamos os valores de cada campo atraves do metodo POST
		$.post('includes/verificaDadosUsuario.php', {email: email, senha: senha }, function(resposta) {
			// Quando terminada a requisicao
			// Exibe a div status
			$("#status").fadeIn();
			// Se a resposta e um erro
			if (resposta != false) {
				// Exibe o erro na div
				$("#status").html(resposta);
			} 
			// Se resposta for false, ou seja, nao ocorreu nenhum erro
			else{
				//window.location="index.php";
			}
		});
	});
});
</script>
	<?php 
		
	}
	
}