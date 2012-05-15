<?php

Class Conexao
{
	protected $host = "localhost";
	protected $user = "postgres";
	protected $pswd = "postgres";
	protected $dbname = "tcc";
	protected $con = null;

	function __construct(){} //m�todo construtor

	#m�todo que inicia conexao
	function open(){
		$this->con = @pg_connect("host=$this->host user=$this->user password=$this->pswd dbname=$this->dbname");
		return $this->con;
	}

	#m�todo que encerra a conexao
	function close(){
		@pg_close($this->con);
	}

	#m�todo verifica status da conexao
	function statusCon(){
		if(!$this->con){
			echo "<h3>O sistema nao esta conectado   [$this->dbname] em [$this->host].</h3>";
			exit;
		}
		else{
			echo "<h3>O sistema esta conectado   [$this->dbname] em [$this->host].</h3>";
		}
	}
}

?>
