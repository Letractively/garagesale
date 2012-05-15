<?php

require_once 'Conexao.class.php';
require_once 'ProdutosControl.class.php';
require_once 'UsuariosControl.class.php';
require_once 'Util.php';

if ($_GET)
{
	if ($funcao = $_GET['funcMens'])
	{
		MensagensControl::$funcao();
	}
}

class MensagensControl
{

	public static function add($mensagem=null)
	{
		$con = new Conexao();
		$con->open();

		if ($mensagem->mensagem )
		{
			$mensagem->mensagem = addslashes($mensagem->mensagem);
			$campo_referencia = 'ref_usuario_destinatario';
			$cod_destino = $mensagem->ref_usuario_destinatario;
		}
		else
		{
			#### Se mensagem vem por POST
			if ($_POST['mensagem'])
			{
				$mensagem->mensagem = addslashes($_POST['mensagem']);
				$mensagem->ref_usuario = UsuariosControl::getLoggedUserId();
			}

			if ($_POST['ref_usuario_destinatario'])
			{
				$campo_referencia = 'ref_usuario_destinatario';
				$cod_destino = $_POST['ref_usuario_destinatario'];
			}
			elseif ($_POST['ref_produto'])
			{
				$campo_referencia = 'ref_produto';
				$cod_destino = $_POST['ref_produto'];
			}
			elseif ($_POST['ref_mensagem_parent'])
			{
				$campo_referencia = 'ref_mensagem_parent';
				$cod_destino = $_POST['ref_mensagem_parent'];
			}
			elseif (!$mensagem->mensagem)
			{
				return;
			}
		}
		## Se for um novo topico, nao inclui o ref_mensagem_parent no INSERT
		$sql = "INSERT INTO mensagens
                    ( {$campo_referencia}, ref_usuario, mensagem ) VALUES
                    ({$cod_destino}, {$mensagem->ref_usuario}, '{$mensagem->mensagem}')";
		$query = pg_query($sql);

		if ($query)
		{
			$con->close();
			if ($_POST['mensagem'])
			{
				echo true;
			}
			else
			{
				return true;
			}
		}
		else{
			showError("Não foi possível enviar a mensagem no momento.");
			return;
		}
	}

	static function getMensagem($id)
	{
		if ($id)
		{
			$con = new Conexao();
			$con->open();
			$sql = "SELECT *
                from mensagens 
                where id = " . $id;

			if (!$result = pg_query($sql))
			{
				showError('Erro ao obter mensagem');
			}
			if ($row = pg_fetch_row($result))
			{
				$mensagem = new stdClass();
				$mensagem->id            = $row[0];
				$mensagem->ref_usuario_destinatario    = $row[1];
				$mensagem->ref_produto      = $row[2];
				$mensagem->ref_usuario         = $row[3];
				$mensagem->ref_mensagem_parent   = $row[4];
				$mensagem->mensagem     = $row[5];
				$mensagem->data      = $row[6];
			}
			pg_free_result($result);
			$con->close();
			return $mensagem;
		}
		return false;
	}

	//	static function getIdLastMensagem($mensagem)
	//	{
	//		if ($mensagem)
	//		{
	//
	//			if ($mensagem->ref_usuario_destinatario)
	//			{
	//				$whereRefUsrDest = ' and ref_usuario_destinatario = '.$mensagem->ref_usuario_destinatario;
	//			}
	//			if ($mensagem->ref_produto)
	//			{
	//				$whereRefProd = ' and ref_produto = '.$mensagem->ref_produto;
	//			}
	//			if ($mensagem->ref_mensagem_parent)
	//			{
	//				$whereRefMens = ' and ref_mensagem_parent = '.$mensagem->ref_mensagem_parent;
	//			}
	//
	//			$con = new Conexao();
	//			$con->open();
	//			$sql = 'SELECT id
	//                from mensagens
	//                where ref_usuario = '.UsuariosControl::getLoggedUserId()
	//			.$whereRefUsrDest
	//			.$whereRefProd
	//			.$whereRefUsr
	//			.$whereRefMens.
	//                ' order by id desc limit 1 ';
	//
	//			if (!$result = pg_query($sql))
	//			{
	//				showError('Erro ao obter ultima mensagem');
	//			}
	//			while ($row = pg_fetch_row($result))
	//			{
	//				$id = $row[0];
	//			}
	//			pg_free_result($result);
	//			$con->close();
	//			return $id;
	//		}
	//		return false;
	//	}

	public static function removeByRefProduto($ref_produto)
	{
		if ($ref_produto)
		{
			$produto = ProdutosControl::getProduto($ref_produto);
			if (!UsuariosControl::isOwner($produto->ref_usuario))
			{
				return;
			}

			$con = new Conexao();
			$con->open();

			$sql = "DELETE FROM mensagens where ref_produto = '$ref_produto' or ref_mensagem_parent in (select id from mensagens where ref_produto = '$ref_produto')";

			$query = pg_query($sql);

			if ($query){
				$con->close();
				return true;
			}
			else{
				return false;
			}
		}
	}

	public static function getTopicos($ref_usuario_destinatario=null,$ref_produto=null, $ref_usuario=null)
	{
		if (!$ref_usuario_destinatario && !$ref_produto && !$ref_usuario)
		{
			return;
		}

		if ($ref_produto)
		{
			$where = " ref_produto = '$ref_produto' ";
		}
		elseif ($ref_usuario_destinatario)
		{
			$where = " ref_usuario_destinatario = '$ref_usuario_destinatario' ";
		}

		if ($ref_usuario)
		{
			if ($where)
			{
				$where .= ' and ';
			}
			$where .= " ref_usuario = '$ref_usuario' ";
		}

		if ($where)
		{
			$where .= ' and ';
		}
		$where .= '  ref_mensagem_parent is null ';

		$con = new Conexao();
		$con->open();

		$sql = "SELECT * from mensagens where $where";

		if (!$result = pg_query($sql))
		{
			showError('Erro ao obter tópicos de mensagens');
			return;
		}

		while ($row = pg_fetch_row($result))
		{
			$topico = new stdClass();
			$topico->id = $row[0];
			$topico->ref_usuario_destinatario = $row[1];
			$topico->ref_produto = $row[2];
			$topico->ref_usuario = $row[3];
			$topico->ref_mensagem_parent = $row[4];
			$topico->mensagem = $row[5];
			$topico->data = $row[6];
			$topicos[] = $topico;
		}
		pg_free_result($result);
		$con->close();
		return $topicos;
	}

	public static function showTopicos($ref_usuario_destinatario=null,$ref_produto=null,$ref_usuario=null)
	{
		if (!$ref_usuario_destinatario && !$ref_produto && !$ref_usuario)
		{
			return;
		}

		$topicos = self::getTopicos($ref_usuario_destinatario,$ref_produto, $ref_usuario);

		echo '<div id="topicos" >';
		echo '<p class="titulo_pagina" >Mensagens</p>';
		echo '<ul id="lista-mensagens" >';
		if ($topicos)
		{
			foreach ($topicos as $topico)
			{
				echo self::getItemTopico($topico);
			}
		}
		else
		{
		}
		echo '<ul>';
		echo '</div>';

		echo '
        <script type="text/javascript" language="javascript">
        $(".link-post").click(function() 
        {
            var link_post = $(this);
            var li_post = $(this).parent();
            var ref_mensagem_parent = link_post.attr("id").split("_")[1];
            link_post.parent().html("<img src=\'images/spinner.gif\' />");
            $.post("includes/MensagensControl.class.php?funcMens=showPosts", {ref_mensagem_parent: ref_mensagem_parent }, function(resposta) 
            {
                li_post.hide();
                li_post.html(resposta);
                li_post.slideDown();
            });
        });
        </script>
        ';
	}

	public static function getPosts($ref_mensagem_parent)
	{
		if (!$ref_mensagem_parent)
		{
			return;
		}

		$con = new Conexao();
		$con->open();

		$sql = "SELECT * from mensagens where ref_mensagem_parent = $ref_mensagem_parent order by data asc";

		if (!$result = pg_query($sql))
		{
			showError('Erro ao obter mensagens');
			return;
		}

		while ($row = pg_fetch_row($result))
		{
			$post = new stdClass();
			$post->id = $row[0];
			$post->ref_usuario_destinatario = $row[1];
			$post->ref_produto = $row[2];
			$post->ref_usuario = $row[3];
			$post->ref_mensagem_parent = $row[4];
			$post->mensagem = $row[5];
			$post->data = $row[6];
			$posts[] = $post;
		}
		pg_free_result($result);
		$con->close();
		return $posts;

	}

	public static function showPosts($ref_mensagem_parent=null)
	{

		if (!$ref_mensagem_parent)
		{
			$ref_mensagem_parent = $_POST['ref_mensagem_parent'];
		}

		if (!$ref_mensagem_parent)
		{
			return;
		}

		$mensagem_parent = self::getMensagem($ref_mensagem_parent);
		$posts = self::getPosts($ref_mensagem_parent);
		echo '<div class="posts" >';
		echo '<ul class="lista-posts" >';
		echo self::getItemPost($mensagem_parent);
		echo '<hr>';
		if ($posts)
		{
			foreach ($posts as $post)
			{
				echo self::getItemPost($post);
			}
		}

		$mensagem = new stdClass();
		$mensagem->ref_mensagem_parent = $ref_mensagem_parent;
		echo '</ul>';
		self::showNovaMensagem($mensagem);
		echo '</div>';

	}

	public static function showNovaMensagem($mensagem)
	{
		## Ou eh um novo topico ou eh um post para um topico
		if (!$mensagem->ref_mensagem_parent)
		{
			$novoTopico = true;
		}

		if ($novoTopico)
		{
			if ($mensagem->ref_usuario_destinatario)
			{
				$id_diferenciador =  'user';
			}
			elseif ($mensagem->ref_produto)
			{
				$id_diferenciador =  'prod';
			}
		}
		else
		{
			$id_diferenciador =  $mensagem->ref_mensagem_parent;
		}

		echo '<button class="botao_novo_comentario" type="button">Nova mensagem</button> ';
		echo '<div class="div_novo_comentario" style="display: none;">';
		echo '    <form class="form_nova_mensagem" action="javascript:void(0);" method="POST">';
		if ($novoTopico){echo '<strong>Nova mensagem:</strong>';}
		echo '        <br><textarea class="texto-mensagem" rows="2" cols="35" name="comentario" ></textarea>';
		echo '        <input id="enviar-mensagem'.$id_diferenciador.'" class="enviar-mensagem" type="submit" value="Comentar" />';
		echo '        <label class="status-enviar-mensagem" style="display: none;"></label>';
		echo '    </form>';
		echo '</div>';

		## Script pro botão novo comentario
		echo '<script type="text/javascript" language="javascript">
            $(".botao_novo_comentario").click(function() 
            {
                $(this).hide();
                $(this).next("div").fadeIn(300);
            });
            </script>';

		$mens = new stdClass();
		$mens->mensagem = '"+mensagem+"';
		$mens->ref_usuario = UsuariosControl::getLoggedUserId();
		unset($itemTopico);
		if ($novoTopico)
		{
			$itemTopico = '<div class=\'posts\' > <ul class=\'lista-posts\' >';
			$itemTopico .= self::getItemPost($mens);
			$itemTopico .= '</ul></div>';
			if ($mensagem->ref_usuario_destinatario)
			{
				$param = 'ref_usuario_destinatario';
				$param_id = $mensagem->ref_usuario_destinatario;
			}
			elseif ($mensagem->ref_produto)
			{
				$param = 'ref_produto';
				$param_id = $mensagem->ref_produto;
			}
			$lista_to_append = '$("#topicos").children("ul")';
		}
		## se for novo post
		else
		{
			$itemTopico = self::getItemPost($mens);
			$param = 'ref_mensagem_parent';
			$param_id = $mensagem->ref_mensagem_parent;
			$lista_to_append = 'div_mens.parent().children("ul")';
		}
		echo '
		<script type="text/javascript" language="javascript">
	    $("#enviar-mensagem'.$id_diferenciador.'").click(function() 
	    {
	       var enviar_mensagem = $(this);
	       // Seleciona o form pai
	        var form_mens = enviar_mensagem.parent();
	        var mensagem = form_mens.children(".texto-mensagem").val().trim();
	        // Seleciona a div pai da form
            var div_mens = form_mens.parent();
            // quando post
            var lista_to_append = '.$lista_to_append.';
            
	        if (mensagem)
	        {
	            enviar_mensagem.hide();
	            form_mens.children(".status-enviar-mensagem").html("<img src=\'images/spinner.gif\' alt=\'Enviando\' />");
		        form_mens.children(".status-enviar-mensagem").slideDown();
		        var '.$param.' = "'.$param_id.'";
		        $.post("includes/MensagensControl.class.php?funcMens=add", {mensagem: mensagem, '.$param.': '.$param.' }, function(resposta) 
		        {
		            enviar_mensagem.show();
		            // Se for um inteiro
		            if (resposta.toString().search(/^-?[0-9]+$/) == 0) 
			        {
			            form_mens.children(".texto-mensagem").val("");
		                form_mens.children("label").hide();
		                div_mens.hide();
		                div_mens.prev().show();
		                lista_to_append.append("'.$itemTopico.'");
		            } 
		            else 
			        {
		                // Exibe o erro na div
		                form_mens.children("label").html(resposta);
		            }
		        });
	        }
	    });
	    
	    </script>
	    ';

		//		echo 'function resumirMensagem(string, tamanho_resumo)
		//        {
		//            if (string.length > tamanho_resumo)
		//            {
		//                var mens_resumo = string.substr(0,tamanho_resumo) + "...";
		//            }
		//            else
		//            {
		//                var mens_resumo = string;
		//            }
		//            return mens_resumo;
		//        }
		//        ';

	}

	function resumirMensagem($string, $tamanho_resumo)
	{
		if (strlen($string) > $tamanho_resumo)
		{
			$mens_resumo = substr($string, 0,$tamanho_resumo).'...';
		}
		else
		{
			$mens_resumo = $string;
		}
		return $mens_resumo;
	}

	function getItemTopico($topico, $isInJavaScript=null)
	{
		$resumo_tamanho = 20;

		$nome_usuario = UsuariosControl::getNomeById($topico->ref_usuario);
		if (!$nome_usuario)
		{
			$nome_usuario = UsuariosControl::getNomeById(UsuariosControl::getLoggedUserId());
		}
		if ($isInJavaScript)
		{
			$topico->id = '"+resposta+"';
			$topico->mensagem = '"+resumirMensagem(mensagem,'.$resumo_tamanho.') +"';
			$topico->data = '"+getNow()+"';
		}
		else
		{
			$topico->mensagem = self::resumirMensagem($topico->mensagem, $resumo_tamanho);
			$topico->data = substr(invDataUsaBra($topico->data),0,10);
		}
		return '<li>
			       <a id=\'topico_'.$topico->id.'\' class=\'link-post\' href=\'javascript:void(0)\'  >
				       '.$topico->mensagem.' --- 
				   </a> 
				   <label class=\'lb_nome_user\'>'.$nome_usuario.'</label>
				   <label class=\'lb_data\'> - '.$topico->data .'</label> 
		      </li>';
	}

	function getItemPost($post)
	{
		if (!$post->data)
		{
			$post->data = '"+getNow()+"';
		}
		else
		{
			$post->data = substr(invDataUsaBra($post->data),0,10);
		}
		$nome_usuario = UsuariosControl::getNomeById($post->ref_usuario);
		return '<li> <a href=\'index.php?pg=perfil&user='.$post->ref_usuario.'\' class=\'link_usuario_coment\' >'.$nome_usuario.' </a> <label class=\'lb_data\'> - '.$post->data.'</label> <p class=\'mensagem_post\'>'.$post->mensagem .'</p> </li>';
	}


}
