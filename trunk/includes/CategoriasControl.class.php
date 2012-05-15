<?php
require_once ("Conexao.class.php");

class CategoriasControl{

	static function add($categoria){
		$con = new Conexao();
		$con->open();

		## devo gravar a categoria parent...... FIXME
		if (!$categoria->ref_categoria_parent){
			$categoria->ref_categoria_parent = 'null';
		}

		$sql = "INSERT INTO categorias
                          (descricao, ref_categoria_parent) VALUES 
                          ( '".$categoria->descricao."', ".$categoria->ref_categoria_parent.")";
		$query = pg_query($sql);
		if ($query){
			return true;
		}
		else{
			return false;
		}
	}

	static function getId($categoria){
		if ($categoria){
			$con = new Conexao();
			$con->open();
			$result = pg_query("select id from categorias where descricao = '$categoria'");
			if ($row = pg_fetch_row($result)){
				return $row[0];
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}
	
	static function getDescricao($ref_categoria){
		if ($ref_categoria){
			$con = new Conexao();
			$con->open();
			$result = pg_query("select descricao from categorias where id = '$ref_categoria'");
			if ($row = pg_fetch_row($result)){
				return $row[0];
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	// Funcao que pega nomes de categorias do banco e retorna no formato "cat1", "cat2",...
	static function getCategorias()
	{
		$con = new Conexao();
		$con->open();
		$sql = "SELECT '\"'||descricao||'\",' as descricao from categorias;";
		$result = pg_query($sql);
		while ($row = pg_fetch_row($result)) {
			$categorias .= $row[0];
		}
		pg_free_result($result);
		$con->close();

		return $categorias;
	}

	static function exists($categoria){

		if ($categoria){
			$con = new Conexao();
			$con->open();
			$sql = pg_query("select * from categorias where descricao = '$categoria'");
			$cont = pg_num_rows($sql);
			if ($cont >= 1){
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

}
