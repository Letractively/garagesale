<?php
function replace_accents($string)
{
	return str_replace( array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $string);
}

function replace_dots($string)
{
	return str_replace( array('.',',',';',':'), array('','','',''), $string);
}

function replace_accents_percent($string)
{
	return str_replace( array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï',
                            'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á',
                            'Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò',
                            'Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), 
	array('%','%','%','%','%', '%','%','%','%', '%','%','%','%',
                            '%', '%','%','%','%','%', '%','%','%','%', '','%', '%','%',
                            '%','%','%', '%', '%','%','%','%', '%','%','%','%', '%', '%',
                            '%','%','%','%', '%','%','%','%', '%'), $string);
}

function arrumaNomeFoto($stringFoto)
{
	$stringFoto = strtolower($stringFoto);
	$stringFoto = replace_accents($stringFoto);
	$stringFoto = str_replace(' ', '_', $stringFoto);

	## retira pontos menos o da extencao
	$stringFoto = replace_dots(basename($stringFoto,'.jpg')).'.jpg';
	return $stringFoto;
}


function getExtension($str) {
	$i = strrpos($str,".");
	if (!$i) { return ""; }
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return $ext;
}

function showInformation($string)
{
	echo '<p class="information"><img src="images/info.png"> '.$string.'</p>';
	echo '<script> $(".information").click(function(){ $(this).slideUp(); }); </script>';
}
function showError($string)
{
	echo '<p class="error"><img src="images/error.png">'.$string.'</p>';
	echo '<script> $(".error").click(function(){ $(this).slideUp(); }); </script>';
}
function showWarning($string)
{
	echo '<p class="warning"><img src="images/warning.png">'.$string.'</p>';
	echo '<script>
            $(".warning").click(function(){ 
                $(this).slideUp(); 
            }); 
          </script>';
}
function semPermissao()
{
	echo '<p class="error"><img src="images/error.png">Sem permição de acesso a esta página.</p>';
	echo '<script> $(".error").click(function(){ $(this).slideUp(); }); </script>';
}

/* --------------------------------------------------------
 * Amadeu Weirich (2006)
 * Inverter data brasileira para o modelo do banco de dados,
 * caso a data já esteje em formato de banco, ele a mantém.
 * ------------------------------------------------------*/
function invDataBraUsa($data)
{
	$dataS = str_replace('/','-',$data);
	$dataS = substr($dataS,0,10);

	if (strlen($dataS) == 10)
	{
		$data_array = explode ('-',$dataS);
		if (strlen($data_array[0]) == 4)
		{
			return $data;
		}
		else
		{
			$dia  = substr($dataS, 0, 2);
			$mes  = substr($dataS, 3, 2);
			$ano  = substr($dataS, 6, 4);
			$dataS = "$ano-$mes-$dia";
			if (strlen($data) > 10)
			{
				$dataS .= ' '.substr($data,11,8);
			}
			return $dataS;
		}
	}
	else
	return false;
}

/* -------------------------------------------------------------
 * Amadeu Weirich (2006)
 * Inverter data de banco de dados para o modelo do brasileiro,
 * caso a data já esteje em formato de brasileiro, ele a mantém.
 * -----------------------------------------------------------*/
function invDataUsaBra($data)
{
	$dataS = str_replace('/','-',$data);
	$dataS = substr($dataS,0,10);

	if (strlen($dataS) == 10)
	{
		$data_array = explode ('-',$dataS);
		if (strlen($data_array[0]) == 2)
		{
			return $data;
		}
		else
		{
			$ano  = substr($dataS, 0, 4);
			$mes  = substr($dataS, 5, 2);
			$dia  = substr($dataS, 8, 2);
			$dataS = "$dia/$mes/$ano";
			if (strlen($data) > 10)
			{
				$dataS .= ' '.substr($data,11,8);
			}
			return $dataS;
		}
	}
	else
	return false;
}


?>