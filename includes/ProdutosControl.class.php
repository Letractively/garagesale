<?php
require_once 'Conexao.class.php';
require_once 'MensagensControl.class.php';
require_once 'UsuariosControl.class.php';
require_once 'Util.php';

if ($_GET)
{
	if ($funcao = $_GET['funcProd'])
	{
		ProdutosControl::$funcao();
	}
}

class ProdutosControl
{

	public static $PRODUTOS_POR_PAGINA = 5;

	/**
	 *
	 * Adiciona produto ao banco de dados
	 * @param $produto
	 */
	static function add($produto){
		$con = new Conexao();
		$con->open();

		if (!UsuariosControl::isOwner($produto->ref_usuario))
		{
			return;
		}

		if (!$produto->detalhes){
			$detalhes = 'null';
		}else{
			$detalhes = "'{$produto->detalhes}'";
		}
		if (!$produto->preco){
			$preco = 'null';
		}else{
			$preco = "'{$produto->preco}'";
		}
		if (!$produto->ref_localidade){
			$ref_localidade = 'null';
		}else{
			$ref_localidade = "'{$produto->ref_localidade}'";
		}

		if ($produto->id)
		{
			$sql = "UPDATE produtos
                        set descricao = '{$produto->descricao}', detalhes = $detalhes,preco = $preco, ref_categoria = '{$produto->ref_categoria}', ref_localidade = $ref_localidade 
                        where id = {$produto->id}";
			$query = pg_query($sql);
		}
		else
		{
			$sql = 'INSERT INTO produtos
                        (descricao, detalhes, preco, ref_usuario, ref_categoria, ref_localidade) VALUES 
                        ( \''.$produto->descricao."', ".$detalhes.", ".$preco.", ".$produto->ref_usuario.", ".$produto->ref_categoria.", ". $ref_localidade ." )";
			$query = pg_query($sql);
		}

		if ($query){
			return true;
		}
		else{
			return false;
		}
	}

	static function remove($ref_produto)
	{
		if ($ref_produto)
		{
			$produto = self::getProduto($ref_produto);
			$ref_usuario = $produto->ref_usuario;

			if (!UsuariosControl::isOwner($ref_usuario))
			{
				return;
			}

			$diretorio_fotos = '../uploads/'.$ref_usuario.'/'.$ref_produto;
			self::rrmdir($diretorio_fotos);

			if (!MensagensControl::removeByRefProduto($ref_produto))
			{
				return;
			}

			$con = new Conexao();
			$con->open();

			$sql = "DELETE FROM produtos where id = '$ref_produto'";
			$query = pg_query($sql);
			if ($query)
			{
				$con->close();
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	static function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}

	private static function montaWhereFilter($descricao=null, $detalhes=null , $precoMin=null, $precoMax=null ,$categoria=null,$localizacao=null, $user=null)
	{
		if ($descricao){
			$sqlWhere  = ' and p.descricao ilike \'%' . addslashes($descricao) . '%\' ';
		}
		if ($detalhes){
			$sqlWhere .= ' and p.detalhes ilike \'%' . addslashes($detalhes) . '%\' ';
		}
		if ($precoMin){
			$sqlWhere .= ' and p.preco >= ' . $precoMin . ' ';
		}
		if ($precoMax){
			$sqlWhere .= ' and p.preco <= ' . $precoMax . ' ';
		}
		if ($categoria){
			$sqlWhere .= ' and c.descricao ilike \'%' . addslashes($categoria) . '%\' ';
		}
		if ($localizacao){
			$sqlWhere .= ' and l.ascii_name || \', \' || e.name ilike \'%' . addslashes($localizacao) . '%\' ';
		}
		if ($user){
			$sqlWhere .= ' and p.ref_usuario = \'' . $user . '\' ';
		}
		return $sqlWhere;

	}

	/**
	 *
	 * obtem um array de produtos a partir de filtros
	 */
	static function getProdutosWithFilter($descricao=null, $detalhes=null , $precoMin=null, $precoMax=null ,$categoria=null,$localizacao=null, $page=1, $user=null)
	{
		$con = new Conexao();
		$con->open();
		$produtos = array();

		$sqlWhere = self::montaWhereFilter($descricao, $detalhes, $precoMin, $precoMax, $categoria, $localizacao, $user);

		$prodsPorPage = self::$PRODUTOS_POR_PAGINA;

		$sql = "SELECT
					p.descricao,
					detalhes,
					preco,
                    status,
                    c.descricao as categoria ,
                    p.id,
                    l.name,
                    e.name,
                    l.longitude,
                    l.latitude,
                    l.name || ', ' || e.name as endereco
                from produtos p 
                    left join localidades l on (l.id = p.ref_localidade) 
                    left join estados e on (l.ref_estado = e.id),
                    categorias c
                where p.ref_categoria = c.id ". 
		$sqlWhere .
		" order by p.dt_cadastro desc 
		limit $prodsPorPage
		offset ". ($prodsPorPage * ($page-1));

		if (!$result = pg_query($sql)){
			showError('Erro ao obter produtos');
			return;
		}

		while ($row = pg_fetch_row($result)) {
			$produto = new stdClass();
			$produto->descricao  = $row[0];
			$produto->detalhes   = $row[1];
			$produto->preco		 = $row[2];
			$produto->status     = $row[3];
			$produto->categoria  = $row[4];
			$produto->id         = $row[5];
			$produto->local      = $row[6];
			$produto->estado     = $row[7];
			$produto->longitude  = $row[8];
			$produto->latitude   = $row[9];
			$produto->endereco   = $row[10];

			$produtos[] = $produto;
		}
		pg_free_result($result);
		$con->close();
		return $produtos;
	}

	/**
	 *
	 * Obtem a quantidade de produtos com filtro
	 */
	static function getQtdProdutosWithFilter($descricao=null, $detalhes=null , $precoMin=null, $precoMax=null ,$categoria=null,$localizacao=null, $user=null)
	{
		$con = new Conexao();
		$con->open();

		$sqlWhere = self::montaWhereFilter($descricao, $detalhes, $precoMin, $precoMax, $categoria, $localizacao, $user);

		$sql = "SELECT count(p.id)
                from produtos p 
                    left join localidades l on (l.id = p.ref_localidade) 
                    left join estados e on (l.ref_estado = e.id),
                    categorias c
                where p.ref_categoria = c.id". 
		$sqlWhere;

		if (!$result = pg_query($sql)){
			showError('Erro ao obter quantidade de produtos');
			return;
		}

		if (!$qtdProdutos = pg_fetch_row($result))
		{
			showError('Erro ao obter quantidade de produtos');
			return;
		}

		pg_free_result($result);
		$con->close();
		return $qtdProdutos[0];
	}

	/**
	 *
	 * Obtem o produto do id passado como parametro
	 * @param unknown_type $id
	 */
	static function getProduto($id)
	{
		if ($id){
			$con = new Conexao();
			$con->open();
			$sql = "SELECT p.id,
				p.descricao,
				detalhes,
				preco,
				ref_usuario,
				c.descricao as categoria,
				status,
				ref_localidade,
				p.dt_cadastro
				from produtos p,
					categorias c
					where p.ref_categoria = c.id and
				p.id = " . $id;

			if (!$result = pg_query($sql)){
				showError('erro ao obter produto');
			}
			while ($row = pg_fetch_row($result)) {
				$produto = new stdClass();
				$produto->id 			= $row[0];
				$produto->descricao 	= $row[1];
				$produto->detalhes	 	= $row[2];
				$produto->preco		 	= $row[3];
				$produto->ref_usuario 	= $row[4];
				$produto->categoria 	= $row[5];
				$produto->status 		= $row[6];
				$produto->ref_localidade= $row[7];
				$produto->dt_cadastro 	= $row[8];
			}
			pg_free_result($result);
			$con->close();

			return $produto;
		}
		return false;
	}

	/**
	 *
	 * Mostra uma lista de produtos de acordo com o filtro e a pagina da lista passados por parametro
	 * @param $descricao
	 * @param $categoria
	 * @param $localizacao
	 * @param $page pagina da lista
	 * @param $user
	 * @param boolean $isDono se eh dono dos produtos ou nao
	 */
	public static function showProdutosList($descricao=null , $detalhes=null, $precoMin=null, $precoMax=null,$categoria=null , $localizacao=null , $page=1 , $user=null)
	{
		if ($_GET['prod_excl'])
		{
			if ($_GET['prod_excl'] == 'erro')
			{
				showError('Erro ao excluir produto');
			}
			else
			{
				showInformation('Produto "'.$_GET['prod_excl'].'" excluido com sucesso');
			}
		}

		## se for ver os produtos do usuario logado
		if ($_GET['user'])
		{
			$user = $_GET['user'];
		}

		## Se os parametros vem por post..
		if ($_POST['descricao'] ||$_POST['detalhes'] ||$_POST['precoMin'] ||$_POST['precoMax'] || $_POST['categoria'] || $_POST['localizacao'] || $_POST['user'])
		{
			$descricao   = $_POST['descricao'];
			$detalhes    = $_POST['detalhes'];
			$precoMin    = $_POST['precoMin'];
			$precoMax    = $_POST['precoMax'];
			$categoria   = $_POST['categoria'];
			$localizacao = $_POST['localizacao'];
			$page        = $_POST['page'];
			$user 		 = $_POST['user'];
			if (!$page)
			{
				$page = 1;
			}
		}

		$produtos = ProdutosControl::getProdutosWithFilter($descricao , $detalhes, $precoMin, $precoMax, $categoria , $localizacao, $page, $user);
		$qtd_produtos = ProdutosControl::getQtdProdutosWithFilter($descricao , $detalhes, $precoMin, $precoMax,$categoria , $localizacao, $user);

		if ($_GET['saved'] == 'true')
		{
			showInformation('Produto salvo com sucesso.');
		}
		
		if ($user)
		{
			echo '<p class="titulo_pagina" >Produtos</p>';
			if (UsuariosControl::isOwner($user))
			{
				echo '<a href="?pg=formProduto" class="special_link" >Cadastrar produto</a>';
			}
		}
		
		if (!$produtos)
		{
			showWarning('Nenhum produto encontrado');
			return;
		}

		echo '<br><br>';
		echo '<label id="status" style="display: none;"></label>';

		## Se der mais de 1 pagina de produtos
		if (ceil($qtd_produtos/self::$PRODUTOS_POR_PAGINA) != 1)
		{
			echo "Mostrando ".sizeof($produtos)." de $qtd_produtos produtos.";
		}
		else
		{
			echo "$qtd_produtos produtos encontrados.";
		}

		echo '<ul id="listProdutos" >';
		foreach ($produtos as $produto)
		{
			echo '<li>';
			echo '<a class="titulo_lista_produto" href="index.php?pg=detailsProduto&prod='.$produto->id.'" >'.$produto->descricao.'</a>';
			echo '<table  class="tabela_produto_lista">
			<tr><td>Detalhes:</td><td> '.$produto->detalhes.'</td></tr>';
			echo '<tr><td>Preco:</td><td> '.$produto->preco.'</td></tr>';
			echo '<tr><td>Categoria:</td><td> '.$produto->categoria .'</td></tr>';
			echo '<tr><td>Localização:</td><td> '.$produto->endereco.'</td></tr>';
			echo '</table>';
			if ($isDono)
			{
				echo '<br><a href="index.php?pg=formProduto&ref_produto='.$produto->id.'" >Editar</a>';

				#FIXME javascript de confirmacao
				echo '<br><a href="index.php?pg=ProdutosControl.class&funcProd=delete&prod='.$produto->id.'" >Deletar</a>';
			}
			echo '</li>';
		}
		echo '</ul>';

		if (ceil($qtd_produtos/self::$PRODUTOS_POR_PAGINA) != 1)
		{
			echo '<ul id="produtosPagination">';
			for ($i = 1; $i <= ceil($qtd_produtos/self::$PRODUTOS_POR_PAGINA); $i++)
			{
				if ($i == $page){
					echo "<li id='paginaAtual'>$i</li>";
				}
				else {
					echo "<li><a class='pagina' href='index.php?pg=formBusca&descricao=$descricao&categoria=$categoria&localInput=$localizacao&user=$user&page=$i' >$i</a></li>";
				}
					
			}
			echo '</ul>';
		}

		if ($descricao){
			$descricao = ' descricao: '.$descricao . ', ';
		}
		if ($detalhes){
			$categoria = ' categoria: '.$categoria . ', ';
		}
		if ($precoMin){
			$precoMin = ' precoMin: '.$precoMin . ', ';
		}
		if ($precoMax){
			$precoMax = ' precoMax: '.$precoMax. ', ';
		}
		if ($categoria){
			$categoria = ' categoria: '.$categoria . ', ';
		}
		if ($localizacao){
			$localizacao = ' localizacao: \''.$localizacao .'\', ';
		}
		if ($user){
			$user = ' user: '.$user;
		}

		echo '
<script type="text/javascript" language="javascript">
$(".pagina").click(function() {
    $("#statusFormProduto").html("<img src=\'images/spinner.gif\' alt=\'Enviando\' />");
    $("#statusFormProduto").slideDown();
    $.post(\'includes/ProdutosControl.class.php?funcProd=showProdutosList\', {'. $descricao . $categoria . $localizacao .'}, function(resposta) 
    {
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
});
</script>
		';


	}


}
?>
