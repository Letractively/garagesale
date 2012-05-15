<?php

require_once 'ProdutosControl.class.php';
require_once 'MapControl.php';
require_once 'UsuariosControl.class.php';
require_once 'LocalidadesControl.class.php';
require_once 'MensagensControl.class.php';
require_once 'Script.php';
require_once 'Util.php';

if (!$ref_produto = $_GET['prod'])
{
	showWarning('Produto não encontrado');
}
else
{
	## Se o produto nao for encontrado na base de dados
	if (!$produto = ProdutosControl::getProduto($ref_produto))
	{
		showWarning('Produto não encontrado');
	}
	else
	{
		$ref_usuario = $produto->ref_usuario;


		mostraRetornoDeMensagemDaUrl();

		if ($_GET['saved'] == 'true')
		{
			showInformation('Produto salvo com sucesso.');
		}

		## Mostra link de voltar
//		echo '<a href="javascript:void(0)" onClick="history.back();"> < lista de produtos</a>';

		echo '<p class="titulo_pagina" >Detalhes do produto</p>';
		
		## Diretorio das fotos
		$diretorio_fotos = 'uploads/'.$ref_usuario.'/'.$ref_produto.'/';

		## Mostra os botoes de opcoes do produto
		echo '<ul id="opcoes-do-produto" >';
		# se item for do usuario
		if (UsuariosControl::isOwner($ref_usuario))
		{
			echo '<li ><a class="special_link" href="index.php?pg=formProduto&prod='.$produto->id.'" >Editar dados do produto</a></li>';
			linkExcluirProduto($produto->id);
		}
		echo '</ul>';

		mostraDadosDoProduto($produto);

		## Mostra o botao para abrir mapa do produto
		MapControl::getHtmlViewLocation($produto->ref_localidade);

		##########################
		### Cria div das fotos do produto com o botao de adicionar foto e os botoes de excluir foto
		##########################
		echo "<div id='fotosProduto'>  ";
		echo 'Fotos:<br>';

		## Se for dono do produto, mostra o botao adicionar foto
		if (UsuariosControl::isOwner($ref_usuario))
		{
			botaoAdicionarFoto($produto->id);
		}

		## Se existir a pasta das fotos do produto
		if (file_exists($diretorio_fotos))
		{
			Html::css('mycss/fotos.css');
			Script::fotoLightBox();

			echo '<div class="gallery">
                <ul class="images">';
			$dir = opendir( $diretorio_fotos);

			$fotos = array();
			while (false !== ($fname = readdir( $dir )))
			{
				if (end(split('_',basename($fname,'.jpg'))) == 'thumb')
				{
					$nome_sem_thumb = basename($fname,'_thumb.jpg').'.jpg';
					echo '<li class="image">
					       <a class="link-imagem" rel="lightbox-mygallery" href="'.$diretorio_fotos . $nome_sem_thumb.'" title="'. $nome_sem_thumb.'">
					           <img class="imagem-thumb" src="'.$diretorio_fotos . $fname.'" >
					       </a>';

					## Se usuario estiver logado, mostra o link de deletar foto
					if (UsuariosControl::isOwner($ref_usuario))
					{
						echo' <a id="deletar-foto' . basename($nome_sem_thumb,'.jpg').'" class="deletar-foto" href="#" title="Excluir foto">
					           <img  src="images/close.png" >
					          </a> ';
					}
					echo '</li>';

					$fotos[] = $diretorio_fotos . $nome_sem_thumb;
					$existeFoto = true;
				}
			}
			echo '</ul>
			 </div>';
		}

		if (!$existeFoto)
		{
			echo 'Nenhuma foto';
		}
		## Cria dialogs e scripts jqueryUI de exclusao de fotos
		else
		{
			foreach ($fotos as $foto)
			{
				$nome_foto = basename($foto,'.jpg');
				echo '<div id="dialogDeleteFoto'.$nome_foto.'" class="ui-dialog-content ui-widget-content" style="width: auto; min-height: 59.4px; height: auto;">
		            <p>
		            <form enctype="multipart/form-data" action="includes/operacoesProduto.php?prod='.$produto->id.'&name_foto_del='.$nome_foto.'" method="POST">
		                <p>Deletar a foto '.basename($foto).'?</p>
		                <br><input type="submit" value="Deletar" />
		            </form>
		            </p>
		            </div>';
				Script::dialogDeleteFoto($nome_foto);
			}
		}
		echo '</div>';
		##############

		if (UsuariosControl::isLogado())
		{
			echo '<div id="mensagens" >';
				
			MensagensControl::showTopicos(null, $ref_produto);

			$mensagem = new stdClass();
			$mensagem->ref_produto = $produto->id;
			$mensagem->ref_usuario = UsuariosControl::getLoggedUserId();
			MensagensControl::showNovaMensagem($mensagem);
			echo '</div>';
		}

	}
}

###########################################
### FUNCTIONS
###########################################

function mostraDadosDoProduto($produto)
{
	echo '<table  class="tabela_produto_lista"';
//	echo "<tr><td>codigo:</td><td><b> {$produto->id}</b></td></tr>";
	echo "<tr><td>Descrição:</td><td><b> {$produto->descricao}</b></td></tr>";
	echo "<tr><td>Detalhes:</td><td><b> {$produto->detalhes}</b></td></tr>";
	echo '<tr><td>Dono:</td><td><b><a href="index.php?pg=perfil&user='.$produto->ref_usuario.'"> '.UsuariosControl::getNomeById($produto->ref_usuario).'</a></b></td></tr>';
	echo "<tr><td>Preco:</td><td><b> {$produto->preco}</b></td></tr>";
	echo "<tr><td>Categoria:</td><td><b> {$produto->categoria}</b></td></tr>";
	echo '<tr><td>Data de Cadastro:</td><td><b> '.substr(invDataUsaBra($produto->dt_cadastro), 0,10).'</b></td></tr>';
	echo "</table>";

}

function linkExcluirProduto($ref_produto)
{
	echo '<li ><a href="#" class="special_link" id="botao-excluir-produto" ><img  src="images/delete.png" >Excluir</a></li>';
	## Cria dialog e script jQueryUI para confirmacao da exclusao do produto
	echo '<div id="dialogExclusaoProduto" class="ui-dialog-content ui-widget-content" style="width: auto; min-height: 59.4px; height: auto;">
            <p>
            <form enctype="multipart/form-data" action="includes/operacoesProduto.php?excluir='.$ref_produto.'" method="POST">
                <p>Deseja excluir este produto?</p>
                <br><input type="submit" value="Excluir produto" />
            </form>
            </p>
            </div>';
	Script::dialogExcluirProduto();
}

function botaoAdicionarFoto($ref_produto)
{
	echo '<a id="botao-adicionar" href="#">+ Adicionar Foto</a><br><br>';
	## Cria dialog e script jQueryUI para upload de foto
	echo '<div id="dialogUploadFoto" class="ui-dialog-content ui-widget-content" style="width: auto; min-height: 59.4px; height: auto;">
            <p>
            <form enctype="multipart/form-data" action="includes/operacoesProduto.php?idaddFoto='.$ref_produto.'" method="POST">
                <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
                <input name="uploadedfile" type="file" /><br />
                <br><input type="submit" value="Carregar foto" />
                <p>Obs.: Apenas são aceitas imagens JPG</p>
            </form>
            </p>
            </div>';
	Script::dialogUpload();
}

function mostraRetornoDeMensagemDaUrl()
{
	if ($_GET['retorno'] && $_GET['tipo'])
	{
		if ($_GET['tipo'] == 'warning')
		{
			showWarning(urldecode($_GET['retorno']));
		}
		elseif ($_GET['tipo'] == 'information')
		{
			showInformation(urldecode($_GET['retorno']));
		}
		elseif ($_GET['tipo'] == 'error')
		{
			showError(urldecode($_GET['retorno']));
		}
		elseif ($_GET['tipo'] == 'permissao')
		{
			semPermissao();
		}
	}
}

