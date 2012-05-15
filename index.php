<?php
require_once("includes/UsuariosControl.class.php");
require_once("includes/Html.php");
require_once("includes/Script.php");


### DEFINES
define('ADMIN', 'garagesale.open@gmail.com');
define('PALAVRA_CHAVE_EMAIL', 'frangoassado');

session_start();

?>

<html lang="pt" dir="ltr">
<head>
<title>garage sale</title>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
<meta content="banha virtual garage sale" name="generator">

<!-- Icone que fica na aba do navegador -->
<link href="images/favicon.png" rel="shortcut icon">

<?php

Html::css('mycss/mycss.css'); 

Html::jquery();

Html::jqueryui();

Script::basico();

//Script::carregaConteudo();

?>

</head>
<body>
	
	<div id="externa">
	
		<?php 
		Html::topnav();
		?>
		
		<!-- superior head  -->
		<div id="topo-head">
			<?php 
			Html::portlets(Html::$TITULO);
			?> 
			<!-- menu superior (usuário) -->
			<div id="menu-usuario-pesquisa" class="portlet">
				<ul>
				
				<?php
				Html::portlets(Html::$PESQUISA);
				Script::autocompleteCategorias();
				
				if (UsuariosControl::isLogado())
				{
					Html::portlets(Html::$USUARIO_LOGADO);
					Html::portlets(Html::$SAIR);
				}
				else
				{
					//Html::portlets(Html::$CADASTRAR);
					//Html::portlets(Html::$ENTRAR);
					Html::portlets(Html::$TOPNAV);
				}
				?>
				
				
				</ul>
			</div>

		<!-- Fim da div superior head -->
		</div>
		
		<div id="menus-laterais">
		<!-- navegacao principal -->
            <div id="p-navigation" class="portlet">
                <h5>Navegação</h5>
                <div class="pBody">
                    <ul>
                    
                    <?php
                    Html::menuItem(Html::$PRINCIPAL);
                    if (UsuariosControl::isLogado())
                    {
                        Html::menuItem(Html::$CADASTRAR_PRODUTO);
                    }
                    else
                    {
                    Html::menuItem(Html::$CADASTRAR_SE);
                    Html::menuItem(Html::$ENTRAR_link);
                    
                    	
                    }
                    Html::menuItem(Html::$BUSCAR);
                    ?>
                    
                    </ul>
                </div>
            </div>
            
            <?php 
            if (UsuariosControl::isLogado())
            {
            ?>
            
            <!-- ferramentas -->
            <div id="p-tb" class="portlet">
                <h5>Ferramentas</h5>
                <div class="pBody">
                    <ul>
                    
                    <?php
                        Html::menuItem(Html::$LISTA_PRODUTOS);
                        Html::menuItem(Html::$MENSAGENS);
                        Html::menuItem(Html::$PROCURAS);
                        
                        if (UsuariosControl::getLoggedEmail() == ADMIN)
                        {
                     	   Html::menuItem(Html::$ADMIN);
                        }
                    ?>
                    
                    </ul>
                </div>
            </div>
		
		  <?php
            } 
		  ?>
		
		</div>
		
		<!-- Main -->
		<div id="content-main">
			
			<?php
			
			$page = $_GET['pg'];
			
			if (!$page)
			{
				$page = 'perfil';
			}
				
			include 'includes/'.$page .'.php';
				
			?>
		
		</div>
		
	<!-- Fim da div Externo -->
	</div>
	
	<?php
	Html::footer();
	
	Script::sair();
	
	?>

</body>
</html>
