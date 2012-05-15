<style>
#status{
	style="display: none;"
}
#enviar{
	margin: .5em 1em 1em .5em;
}
</style>


<div id="escrever">
<form id="formularioCadastroUsuario" action="javascript:void(0);" method="post"><strong>Nome:</strong>
<br />
<input name="nomeCadastro" type="text" id="nomeCadastro" size="45" /> <br />
<br />

<strong>Email:</strong> <br />
<input name="emailCadastro" type="text" id="emailCadastro" size="35" /> <br />
<br />

<strong>senha:</strong> <br />
<input name="senhaCadastro" type="password" id="senhaCadastro" size="35" /><br />
<br />

<input id="enviar" type="submit" value="Enviar" />
<label id="statusCadastroUsuario" style="display: none;"></label>
</form>

</div>

<br><br>

<script type="text/javascript" language="javascript">
	// Quando o formulario for enviado, essa funcao e chamada
	$("#formularioCadastroUsuario").submit(function() {
		$("#enviar").hide();
		// Exibe mensagem de carregamento
		$("#statusCadastroUsuario").html("<img src='images/spinner.gif' alt='Enviando' />");
		// Colocamos os valores de cada campo em uma variavel para facilitar a manipulacao
		var nome = $("#nomeCadastro").val();
		var email = $("#emailCadastro").val();
		var senha = $("#senhaCadastro").val();
			// Exibe a div status
			$("#statusCadastroUsuario").slideDown();
		// Fazemos a requisicao ajax com o arquivo enviaDadosUsuario.php e enviamos os valores de cada campo atraves do metodo POST
		$.post('includes/createNewUser.php', {nome: nome, email: email, senha: senha }, function(resposta) {
			// Quando terminada a requisicao

			if (resposta == true) {
				window.location="index.php?pg=confirmacaoEnviado";
			} 
			else {
				$("#enviar").show();
				// Exibe o erro na div
				$("#statusCadastroUsuario").html(resposta);
			}
		});
	});
</script>