<?php


require_once 'Conexao.class.php';
require_once 'ProdutosControl.class.php';
require_once 'UsuariosControl.class.php';
require_once 'Util.php';

if ($_GET)
{
	if ($funcao = $_GET['funcMens'])
	{
		ProcuraControl::$funcao();
	}
}

class ProcuraControl
{

	public static function add($procura=null)
	{
		$con = new Conexao();
		$con->open();
		$sql = "INSERT INTO procura
                    ( ref_usuario, ref_categoria ) VALUES
                    ({$procura->ref_usuario}, {$procura->ref_categoria})";

		$query = pg_query($sql);

		if ($query)
		{
			$con->close();

		}
		else{
			showError("Não foi possível adicionar procura no momento.");
			return;
		}
	}

	public static function remove($ref_procura)
	{
		$con = new Conexao();
		$con->open();

		$sql = 'DELETE FROM procura where id = '.$ref_procura;

		$query = pg_query($sql);

		if ($query){
			$con->close();
			return true;
		}
		else{
			return false;
		}
	}

	## obtem os ids de usuarios q estao procurando a categoria
	static function getUsuariosProcuras($categoria)
	{
		$con = new Conexao();
		$con->open();

		$sql = "SELECT u.id
                from procura p , categorias c, usuarios u
                where p.ref_categoria = c.id 
                and p.ref_usuario = u.id 
                and c.descricao ilike '%" . $categoria. "%'";

		if (!$result = pg_query($sql))
		{
			showError('Erro ao obter procuras');
		}

		$ids = array();
		while ($row = pg_fetch_row($result))
		{
			$ids[] = $row[0];
		}
		pg_free_result($result);
		$con->close();
		return $ids;

	}

	## obtem os ids de usuarios q estao procurando a categoria
	static function getProcurasUsuario($ref_usuario)
	{
		$con = new Conexao();
		$con->open();

		$sql = " SELECT p.id, c.descricao as categoria, p.ref_usuario from procura p, categorias c where c.id = p.ref_categoria and ref_usuario = " . $ref_usuario;

		if (!$result = pg_query($sql))
		{
			showError('Erro ao obter procuras de usuario');
		}

		while ($row = pg_fetch_row($result))
		{
			$procura = new stdClass();
			$procura->id = $row[0];
			$procura->categoria = $row[1];
			$procura->ref_usuario = $row[2];
			$procuras[] = $procura;
		}
		pg_free_result($result);
		$con->close();
		return $procuras;

	}

}
