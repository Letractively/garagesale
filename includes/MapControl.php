<?php

require_once 'LocalidadesControl.class.php';

$funcao = $_GET['funcMap'];

if ($funcao){
	MapControl::$funcao();
}

class MapControl
{

	/**
	 *
	 * Enter description here ...
	 */
	static function getHtmlSearchPlace($place){

		self::getMapScripts();
		?>

<div id="div_searchplace"><strong>Local:</strong> <br>
<input type="text" id="local" value="" size="23" />
<button id="pesquisarCidade" class="botao" type="button">Pesquisar</button>
<label for="pesquisarCidade" id="localStatus" style="display: none;"></label>
<br>
<br>
<div id="divToMap" style="<?php if (!$place){echo 'display: none;';}?>">
		<?php
		## se for edicao de produto e o mesmo possui local, exibe o mapa do mesmo

		if ($place)
		{
			self::getMapOfPlace($place->id);
		}


		?></div>
</div>

<br>

<script type="text/javascript" language="javascript">

$("#pesquisarCidade").click(function() {
    $("#localStatus").html("<img src='images/spinner.gif' alt='Enviando' />");
    $("#localStatus").fadeIn();
    var local = $("#local").val();
    if (local != null)
    {
        $.post('includes/MapControl.php?funcMap=getMapOfPlace', {local: local }, function(resposta) {
            if (resposta != false){
                $("#divToMap").slideDown();
                $("#divToMap").html(resposta);
                
                $("#localStatus").fadeOut();
            }
            else{
                $("#localStatus").html("Local não encontrado");
            }
        });
    }
});
</script>


		<?php

	}

	static function getHtmlViewLocation($ref_localidade){

		if ($ref_localidade)
		{

			self::getMapScripts();
			?>

<div id="divToMap">
<button id="verLocalizacao" class="botao" type="button"><img
	src="images/map.png" style="margin-right: .5em;">Ver Localização</button>
</div>
<label
	for="verLocalizacao" id="localStatus" style="display: none;"></label>

<script type="text/javascript" language="javascript">

$("#verLocalizacao").click(function() {
    $("#localStatus").html("<img src='images/spinner.gif' alt='Enviando' />");
    $("#localStatus").fadeIn();
    var ref_localidade = <?=$ref_localidade?>;
    if (ref_localidade != null)
    {
        $.post('includes/MapControl.php?funcMap=getMapOfPlace', {ref_localidade: ref_localidade }, function(resposta) {
            if (resposta != false){
                $("#localStatus").fadeOut();
                $("#divToMap").html(resposta);
                $("#divToMap").slideDown();
            }
            else{
                $("#localStatus").html("Local não encontrado");
            }
        });
    }
});
</script>
			<?php
		}
		else
		{
			echo '<p> Este produto não possui localização </p>';
		}
			
	}

	/**
	 *
	 * Enter description here ...
	 */
	static function getMapOfPlace($ref_localidade=null){

		require_once 'Util.php';
		require_once 'LocalidadesControl.class.php';

		## Se estiver vindo um filtro de procura por local pelo post.
		if ($_POST['local'])
		{
			$localidade = LocalidadesControl::searchByLocalName(replace_accents(trim($_POST['local'])));
			## Se encontrou o local
			if ($localidade)
			{
				$ref_localidade = $localidade->id;
			}
			else
			{
				return false;
			}
		}
		## Se estiver vindo um id de localidade por parametro
		elseif ($_POST['ref_localidade'])
		{
			$ref_localidade = $_POST['ref_localidade'];
			$localidade = LocalidadesControl::getPlaceOfId($ref_localidade);
		}
		elseif ($ref_localidade)
		{
			$localidade = LocalidadesControl::getPlaceOfId($ref_localidade);
		}
		else
		{
			return false;
		}
		
		## obtem da localidade o endereco
		$address = $localidade->name . ', ' . $localidade->estado;

		## imprime o endereco
		echo $address . "\n";

		## Se for procura por local, coloca o campo hidden para armazenar o id da localidade
		echo '<input type="hidden" id="localizacao" value="'.$ref_localidade.'" />' . "\n";

		## Cria a div para o mapa
		$divMap = '<div id="map"></div>' . "\n";

		## cria o script de criacao do mapa
		$scriptMap = '<script type="text/javascript">' . "\n";
		$scriptMap .= '$("#map").gMap({ markers: [{' . "\n";

		## se for procura por local, coloca o endereco.
		if ($filtroLocal)
		{
			$scriptMap .= 'address: "'.$address.', brasil", html: "'.$address.'" }],zoom: 8, address: "'.$address.', brasil" });'  . "\n";
		}
		## se for um local fixo, coloca as coordenadas
		else
		{
			$latitude = $localidade->latitude;
			$longetude = $localidade->longitude;
			$scriptMap .= 'latitude:'.$latitude.',longitude:'.$longetude.',html:"'.$address.'", popup: true}],zoom: 8,latitude:'.$latitude.',longitude:'.$longetude.'});' . "\n";
		}
		$scriptMap .= '</script>' . "\n";

		echo $divMap;
		echo $scriptMap;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $local
	 */
	static function getHtmlAutocompleteCidade($local)
	{

		?>
<!-- Script jquery-ui que auto-completa -->
<style>
.ui-autocomplete-loading {
	background: white url('images/spinner.gif') right center no-repeat;
}
</style>
<script>
    $(function() {
        function log( message ) {
           // $( "#localInput" ).text(message);
        }

        $( "#localInput" ).autocomplete({
            source: "includes/MapControl.php?funcMap=onPopulaAutocompleteCidades",
            minLength: 3,
            select: function( event, ui ) {
                log( ui.item.label );
            }
        });
    });
    </script>

<div class="ui-widget"><strong>Local:</strong> <br />
<input id="localInput" value="<?=$local?>" /> <br />
<br />
</div>
		<?php

	}

	static function onPopulaAutocompleteCidades()
	{
		LocalidadesControl::populaAutocompleteCidades($_GET['term']);
	}

	static function getMapScripts(){
		?>
<script
	type="text/javascript"
	src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAVobc0vtTDtGqnxd8xzLt6xRCR_-gSGnTuAyUKw8sfdF5-8FpWBTYUPWUYfM31vqsFb40r4fCFw1WWw"></script>
<script
	type="text/javascript" src="js/jquery.gmap-1.1.0-min.js"></script>
		<?php

	}

	static function getMapOfProdutos($produtos)
	{
		require_once 'Util.php';

		## Cria a div para o mapa
		echo '<div id="map"></div>' . "\n";

		## cria o script de criacao do mapa
		echo '<script type="text/javascript">' . "\n";
		echo '$("#map").gMap({ markers: [' . "\n";

		foreach ($produtos as $produto)
		{
			$endereco = $produto->local . ', ' . $produto->estado;
			self::addMarker($latitude, $longitude, $endereco);
		}
		echo '],zoom: 8,latitude:'.$latitude.',longitude:'.$longetude.'});</script>' . "\n"; #FIXME
	}

	private static function addMarker($latitude, $longitude, $html, $address=null, $popup=false, $icon=null)
	{
		if ( $latitude && $longitude)
		{
			$latitude = ' latitude: '.$latitude . " \n";
			$longitude = ' longitude: '.$longitude . " \n";
		}
		elseif ($address)
		{
			$address = ' address: ' . $address . " \n";
		}
		if ($popup)
		{
			$popup = ' popup: true' . " \n";
		}
		if ($icon)
		{
			$icon = ' icon: ' . $icon . " \n";
		}
		echo '{';
		echo $latitude . $longitude . $html . $address . $popup . $icon;
		echo '},';
	}




}
