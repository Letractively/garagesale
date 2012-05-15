<?php

require_once 'Conexao.class.php';

class LocalidadesControl
{

	static function searchByLocalName($filtro){

		$con = new Conexao();
		$con->open();
		$sql = 'SELECT l.name,
               e.name, 
               latitude, 
               longitude,
               l.id
            from localidades l, 
               estados e 
            where l.ref_estado = e.id and 
               l.ascii_name ilike \'%'.$filtro.'%\' 
            order by length(l.name), population desc , date desc 
            limit 1;';
		$result = pg_query($sql);
		if ($result){
			$localidade = new stdClass();
			if ($row = pg_fetch_row($result)) {
				$localidade->name = $row[0];
				$localidade->estado = $row[1];
				$localidade->latitude = $row[2];
				$localidade->longitude = $row[3];
				$localidade->id = $row[4];
				return $localidade;
			}
			return false;
		}
	}

	static function getPlaceOfId($id){
		if ($id)
		{
			$con = new Conexao();
			$con->open();

			$sql = 'SELECT l.name,
               e.name, 
               latitude, 
               longitude,
               l.id
            from localidades l, 
               estados e 
            where l.ref_estado = e.id and 
               l.id = \''.$id.'\' ;';
			$result = pg_query($sql);

			if ($result){
				$localidade = new stdClass();
				if ($row = pg_fetch_row($result)) {
					$localidade->name = $row[0];
					$localidade->estado = $row[1];
					$localidade->latitude = $row[2];
					$localidade->longitude = $row[3];
					$localidade->id = $row[4];
					return $localidade;
				}
				else
				{
					return false;
				}
			}
		}
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $string
	 */
	static function populaAutocompleteCidades($string)
	{
		include 'Util.php';

		$con = new Conexao();
		$con->open();

		$sql = '
		select distinct cid from (
SELECT \'{"label": "\' || l.name || \', \' || e.name || \'"},\' as cid 
    from localidades l, 
        estados e 
    where l.ref_estado = e.id and 
    l.ascii_name ilike \'%' .replace_accents(replace_dots($string)) . '%\' 
    order by length(l.name), population desc , date desc
    limit 8
    ) as foo
    ';
		$result = pg_query($sql);
		if ($result){
			$cidades = '[';
			while ($row = pg_fetch_row($result)) {
				$cidades .= $row[0];
			}
			$cidades = substr($cidades, 0, strlen($cidades)-1);
			$cidades .= "]";
			echo $cidades;
		}
		pg_free_result($result);
		$con->close();
	}

	static function populaAutocompleteCidadesV2($string)
	{
		include 'Util.php';

		$string = replace_accents(replace_dots($string));

		foreach (split(' ', $string) as $word)
		{
			$sqlEstado .= " or e.name ilike '%$word%' ";
		}

		$con = new Conexao();
		$con->open();

		$sql = '
        select distinct cid from (
SELECT \'{"id": "\' || l.id || \'", "label": "\' || l.name || \', \' || e.name || \'"},\' as cid 
    from localidades l, 
        estados e 
    where l.ref_estado = e.id and 
    l.ascii_name ilike \'%' .$string. '%\' '.
		$sqlEstado . '
    order by length(l.name), population desc , date desc
    limit 8
    ) as foo
    ';

		$result = pg_query($sql);
		if ($result){
			$cidades = '[';
			while ($row = pg_fetch_row($result)) {
				$cidades .= $row[0];
			}
			$cidades = substr($cidades, 0, strlen($cidades)-1);
			$cidades .= "]";
			echo $cidades;
		}
		pg_free_result($result);
		$con->close();
	}
}