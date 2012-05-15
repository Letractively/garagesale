

<script type="text/javascript" language="javascript">
$(function() {
	// Quando o formulario for enviado, essa funcao e chamada
	$("#formularioSignin").submit(function() {
		// Colocamos os valores de cada campo em uma variavel para facilitar a manipulacao
		var email = $("#email").val();
		var senha = $("#senha").val();
		
		// Exibe mensagem de carregamento
		$("#status").html("<img src='images/spinner.gif' alt='Enviando' />");
		// Fazemos a requisicao ajax com o arquivo enviaDadosUsuario.php e enviamos os valores de cada campo atraves do metodo POST
		$.post('includes/verificaDadosUsuario.php', {email: email, senha: senha }, function(resposta) {
				// Quando terminada a requisicao
				// Exibe a div status
				$("#status").fadeIn();
				// Se resposta for true, ou seja, nao ocorreu nenhum erro
				if (resposta == true) {
					$("#status").fadeOut();	
					window.location="index.php";
				} 
				// Se a resposta e um erro
				else{
					// Exibe o erro na div
					$("#status").html(resposta);
				}
		});
	});
});
</script>

<style>
#status{
	style="display: none;"
}
#enviar{
	margin: .5em 1em 1em .5em;
}
</style>


<div id="escrever">
<form id="formularioSignin" action="javascript:void(0);" method="post">
<strong>Email:</strong> <br />
<input name="email" type="text" id="email" size="35" /> <br />
<br />
<strong>senha:</strong> <br />
<input name="senha" type="password" id="senha" size="35" /><br />
<br />
<input id="enviar" type="submit" value="Enviar" />
<label id="status" ></label>
</form>

</div>

