<?php
require_once 'UsuariosControl.class.php';
require_once 'ProdutosControl.class.php';

$UPLOADS_DIR = '../uploads';

$ref_produto = $_GET['prod'];

## se estiver excluindo um produto
if ($_GET['excluir'])
{
	$ref_produto_excluir = $_GET['excluir'];
	$produto = ProdutosControl::getProduto($ref_produto_excluir);
	if (!ProdutosControl::remove($ref_produto_excluir))
	{
		## para identificar que o produto nao foi excluido com sucesso
		$produto->descricao = 'erro';
	}
	header('Location: ../index.php?pg=ProdutosControl.class&funcProd=showProdutosList&user='.UsuariosControl::getLoggedUserId()."&prod_excl={$produto->descricao}");
	exit;
}
## Se estiver deletando uma foto
elseif ($_GET['prod'] && $_GET['name_foto_del'])
{
	$produto = ProdutosControl::getProduto($ref_produto);
	$ref_usuario = $produto->ref_usuario;
	$foto_name = $_GET['name_foto_del'];

	## Se nao for o dono do produto
	if (!UsuariosControl::isOwner($ref_usuario))
	{
		$tipo_retorno = 'permissao';
		return;
	}

	## Diretorio das fotos
	$diretorio_fotos = $UPLOADS_DIR.'/'.$ref_usuario.'/'.$ref_produto.'/';

	$caminho = $diretorio_fotos . $foto_name . '.jpg';
	$caminho_thumb = $diretorio_fotos . $foto_name . '_thumb.jpg';

	## Se nao encontrar a foto
	if ( !file_exists($caminho) || !file_exists($caminho_thumb) )
	{
		$retorno = urlencode("Foto <b>$foto_name.jpg</b> não encontrada.");
		$tipo_retorno = 'warning';
	}
	else
	{
		## Deleta foto e thumb
		unlink($caminho);
		unlink($caminho_thumb);
		$retorno = urlencode("Foto <b>$foto_name.jpg</b> excluida com sucesso.");
		$tipo_retorno = 'information';
	}
}
## Se estiver adicionando uma foto para o produto
elseif ($_FILES && $_GET['idaddFoto'])
{
	$retornou_erro = verificaFiles($_FILES);
	## Se os dados do arquivo estiverem corretos

	$ref_produto = $_GET['idaddFoto'];
	$produto = ProdutosControl::getProduto($ref_produto);
	$ref_usuario = $produto->ref_usuario;
	$foto_name = $_GET['name_foto_del'];
	## Se nao for o dono do produto
	if (!UsuariosControl::isOwner($ref_usuario))
	{
		$tipo_retorno = 'permissao';
		return;
	}
	if (!$retornou_erro)
	{

		## Diretorio das fotos
		$diretorio_fotos = $UPLOADS_DIR.'/'.$ref_usuario.'/'.$ref_produto.'/';
	
		## Cria os diretorios de usuario e produto caso nao existam
		geraDiretoriosDeFotos($ref_usuario, $ref_produto, $UPLOADS_DIR);

		$file_name = basename(arrumaNomeFoto($_FILES['uploadedfile']['name']));
		$orig_name = $file_name;

		## coloca um numero no final do arquivo se ele ja existe.
		$count=2;
		while (file_exists($diretorio_fotos.$file_name))
		{
			$file_name = basename($orig_name, '.jpg') .'_'. $count . '.jpg';
			$count++;
		}

		$caminho_completo = $diretorio_fotos . $file_name;

		## Se a imagem for salva no servidor com sucesso
		if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $caminho_completo))
		{
			## Se for criado o thumbnail da foto com sucesso
			if (criaThumbnail($diretorio_fotos, $file_name, 200))
			{
				$retorno = urlencode('O arquivo <b>'.$orig_name. '</b> foi carregado com sucesso.');
				$tipo_retorno = 'information';
			}
			else
			{
				$retorno = urlencode('Erro ao criar thumbnail da foto.');
				$tipo_retorno = 'error';
			}
		}
		else
		{
			$retorno = urlencode('Houve um erro ao carregar imagem, por favor tente novamente.');
			$tipo_retorno = 'error';
		}
	}
	else
	{
		$retorno = urlencode($retornou_erro);
		$tipo_retorno = 'warning';
	}
}

header('Location: ../index.php?pg=detailsProduto&prod='.$ref_produto.'&retorno='.$retorno.'&tipo='.$tipo_retorno);
exit;

function verificaFiles($files)
{
	if (!$files['uploadedfile']['type'])
	{
		return 'Nenhuma foto encontrada ou tamanho limite do arquivo ultrapassado.';
	}
	elseif ($files['uploadedfile']['type'] != 'image/jpeg')
	{
		return 'Apenas sao aceitos arquivos de imagens .jpg ';
	}
	elseif ($files['uploadedfile']['size'] > 3000000 || $files['uploadedfile']['size'] == 0)
	{
		return 'Limite maximo do tamanho do arquivo: 5MB.';
	}
}

function geraDiretoriosDeFotos($ref_usuario, $ref_produto, $upload_dir)
{
	if (!file_exists($upload_dir .'/'.$ref_usuario))
	{
		mkdir($upload_dir.'/'.$ref_usuario);
	}
	if (!file_exists($upload_dir.'/'.$ref_usuario.'/'.$ref_produto))
	{
		mkdir($upload_dir.'/'.$ref_usuario.'/'.$ref_produto);
	}
}

function criaThumbnail ($pathToImages, $fname, $thumbWidth)
{
	// parse path for the extension
	$info = pathinfo($pathToImages . $fname);

	// continue only if this is a JPEG image
	if ( strtolower($info['extension']) == 'jpg' )
	{
		// load image and get image size
		if (!$img = imagecreatefromjpeg( "{$pathToImages}{$fname}" ))
		{
			return false;
		}

		$width = imagesx( $img );
		$height = imagesy( $img );
		// calculate thumbnail size
		$new_width = $thumbWidth;
		$new_height = floor( $height * ( $thumbWidth / $width ) );

		// create a new tempopary image
		$tmp_img = imagecreatetruecolor( $new_width, $new_height );

		// copy and resize old image into new image
		if (!imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height ))
		{
			return;
		}

		$destination_file =  $pathToImages . basename($fname,'.jpg').'_thumb.jpg' ;
		$qualidade_foto = 90;
		// save thumbnail into a file
		if (!imagejpeg( $tmp_img, $destination_file, $qualidade_foto))
		{
			return;
		}
		return true;
	}
}
