<?php
require_once 'Conexao.class.php';
require_once 'Util.php';

session_start();

Class UsuariosControl
{

	static function add($usuario){
		$con = new Conexao();
		$con->open();
		$query = pg_query("INSERT INTO usuarios
		                  (nome,email,senha) VALUES 
		                  ( '".$usuario->nome."', '".$usuario->email."', '".md5($usuario->senha)."')");
		if ($query){
			return true;
		}
		else{
			return false;
		}
	}
	
    /**
     * 
     * Retorna se o ref_usuario passado por parametro é igual ao usuario logado
     * @param unknown_type $ref_produto
     */
	static function isOwner($ref_usuario)
	{
		if (self::getLoggedUserId() == $ref_usuario)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	static function isValidUser($email, $senha){
		if ($email && $senha){
			$con = new Conexao();
			$con->open();
			$sql = pg_query("select * from usuarios where email = ' $email '");
			$cont = pg_num_rows($sql);
			if ($cont != 1){
				if ($email != pg_result($sql,0,"email") || md5($senha) != pg_result($sql,0,"senha")){
					$con->close();
					return FALSE;
				} else {
					$con->close();
					return true;
				}
			}

		}
		else {
			return FALSE;
		}
	}

	static function exists($email){

		if ($email){
			$con = new Conexao();
			$con->open();
			$sql = pg_query("select * from usuarios where email = '$email'");
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

	static function isLogado(){
		if ($_SESSION['email']){
			return true;
		}
		else{
			return false;
		}
	}

	static function getNome($email){
		if ($email){
			$con = new Conexao();
			$con->open();
			$result = pg_query("select nome from usuarios where email = '$email'");
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
	
	static function getNomeById($ref_usuario){
		if ($ref_usuario){
			$con = new Conexao();
			$con->open();
			$result = pg_query("select nome from usuarios where id = '$ref_usuario'");
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

	static function getEmail($id_usuario){
		if ($email){
			$con = new Conexao();
			$con->open();
			$result = pg_query("select email from usuarios where id = '$id_usuario'");
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

	static function getId($email){
		if ($email){
			$con = new Conexao();
			$con->open();
			$result = pg_query("select id from usuarios where email = '$email'");
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

	static function getLoggedEmail(){
		return $_SESSION['email'];
	}
	static function getLoggedUserId(){
		return $_SESSION['user'];
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $hash
	 * @return o email logado
	 */
	static function confirmEmail($hash, $palavra_chave)
	{
		if ($hash)
		{
			$con = new Conexao();
			$con->open();
			$sql = "select email from usuarios group by 1 having md5(email||'".$palavra_chave."') = '$hash'";
			$result = pg_query($sql);
			$cont = pg_num_rows($result);
			$email = pg_fetch_row($result);
			if ($cont >= 1){
				$sql = "update usuarios set email_confirmado = 'true' where email = '".$email[0]."'";
				$query = pg_query($sql);
				if ($query)
				{
					return $email[0];
				}
				else
				{
					showError('Erro ao dar update no banco de confirmacao de email.');
				}
			}
		}
		return false;
	}

}