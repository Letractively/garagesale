<?php

Class Html{
	
	// Menus laterais
	static $PRINCIPAL = 1;
	static $CADASTRAR_PRODUTO = 2;
	static $LISTA_PRODUTOS = 3;
	static $BUSCAR = 4;
	static $PERFIL = 5;
	static $MENSAGENS = 6;
	static $CADASTRAR_SE = 7;
	static $ENTRAR_link = 8;
	static $PROCURAS = 9;
	static $ADMIN = 10;
	
	// styles
	static $AUTOCOMPLETE = 100;
	
	// portlets
	static $TITULO = 21;
	static $PESQUISA = 22;
	static $USUARIO_LOGADO = 23;
	static $SAIR = 24;
	static $CADASTRAR = 25;
	static $ENTRAR = 26;
	static $TOPNAV = 27;
	
	static function menuItem($menu){
		
		switch ($menu) {
	    case self::$PRINCIPAL:
	    	?>
<li><a href="index.php" onclick="javascript:carregaConteudo('page_1.php', '#content');" title="">
<img src="images/go-home.png">Página principal</a></li>
	    	<?php 
	        break;
	    case self::$CADASTRAR_PRODUTO:
	    	?>
<li><a href="?pg=formProduto"  title="">
<img src="images/list-add.png">Cadastrar Produto</a></li>
			<?php 
	        break;
	    case self::$LISTA_PRODUTOS:
	    	?>
<li><a href="?pg=ProdutosControl.class&funcProd=showProdutosList&user=<?php echo UsuariosControl::getLoggedUserId()?>" title="">
<img src="images/produtos.png">Meus Produtos</a></li>
			<?php 
	        break;
		case self::$BUSCAR:
			?>
<li><a href="?pg=formBusca" title="">
<img src="images/zoom-in.png">Buscar</a></li>
		<?php
	        break;
		case self::$PERFIL:
	        ?>
<li><a href="?pg=perfil">
<img src="images/news-icon.png">Perfil</a></li>
	        <?php 
	        break;
	        case self::$MENSAGENS:
            ?>
<li><a href="?pg=mensagens">
<img src="images/mail-icon.png">Mensagens</a></li>
            <?php 
            break;
            case self::$CADASTRAR_SE:
            ?>
<li><a href="?pg=formUsuario">
<img src="images/go-up.png">Cadastrar-se</a></li>
            <?php 
            break;
            case self::$ENTRAR_link:
            ?>
<li><a href="?pg=signin">
<img src="images/entrar.png">Entrar</a></li>
            <?php 
            break;
            case self::$PROCURAS:
            ?>
<li><a href="?pg=procuras">
<img src="images/procura.png">Meus Avisos</a></li>
            <?php 
            break;
            case self::$ADMIN:
            ?>
<li><a href="?pg=admin">
<img src="images/admin.png">Admin</a></li>
            <?php 
            break;
		}
	}
	
	/*
	static function logo(){
		?>
<div id="p-logo" class="portlet">
<a title="Visite a página principal" href="index.php" ><br><img style="display: none;" src="images/drawingTeste.svg" width="150px"> </a>
</div>
		<?php 
	}
	*/
	
	static function css($arquivo){
		echo '<link type="text/css" href="' .$arquivo. '" rel="stylesheet" >
';
	}
	
	public static function jquery(){
		echo '<script type="text/javascript" src="js/jquery-1.5.2.min.js"></script>
';
	}

	static function jqueryui(){
		echo '<link type="text/css" href="css/custom-theme/jquery-ui-1.8.11.custom.css" rel="stylesheet" >
<script type="text/javascript" src="js/jquery-ui-1.8.11.custom.min.js"></script>
';
	}
	
	static function jqueryuiAdd($opcao){
		switch ($opcao) {
	    case self::$AUTOCOMPLETE:
		?>
<style>
.ui-autocomplete {
	max-height: 200px;
	overflow-y: auto;
	/* prevent horizontal scrollbar */
	overflow-x: hidden;
	/* add padding to account for vertical scrollbar */
	padding-right: 20px;
}

/* IE 6 doesnt support max-height
	 * we use height instead, but this forces the menu to always be this tall
	 */
* html .ui-autocomplete {
	height: 100px;
}
</style>
		<?php
			break;
		case 2:
	        echo "i equals 2";
	        break;
		case 3:
	        echo "i equals 2";
	        break;
		} 
	}
	
	static function portlets($portlet){
		
		switch ($portlet){
			case self::$TITULO:
				?>
<a id="p-titulo" title="Visite a página principal" href="index.php"><img src="images/drawingTeste.svg" width="50px"><label>garage sale</label></a>
				<?php 
				break;
			case self::$PESQUISA:
				?>
<li>
	<div id="pesquisa">
		<form action="index.php?pg=formBusca" method="post">
			<input type="text" name="categoria" title="Pesquisar nesta wiki" class="categoriaInput"> 
			<input type="submit" title="Procurar produtos que contêm este texto" value="Buscar" id="mw-searchButton" class="searchButton" name="fulltext">
		</form>
	</div>
</li>
				<?php 
				break;
			case self::$USUARIO_LOGADO:
				echo '<li class="menuPessoal" ><a title="titulo" href="?pg=perfil">'.UsuariosControl::getLoggedEmail().'</a></li>';
				break;
			case self::$SAIR:
				echo '<li class="menuPessoal" ><a href="javascript:void(0)" onclick="sair();" id="sair" title="Sair"  >Sair</a></li>'; 
				break;
			case self::$CADASTRAR:
				?>
<li class="menuPessoal" >
	<a title="titulo" href="?pg=formUsuario">Cadastrar</a>
</li>
				<?php 
				break;
			case self::$ENTRAR:
				?>
<li class="menuPessoal" >
	<a title="titulo"  href="?pg=signin">Entrar</a>
</li>			
				<?php 
				break;
			case self::$TOPNAV:
            ?>
    <li id="topnav" class="menuPessoal">
         Possui conta?&nbsp;&nbsp;<a href="login" id="signin" class="signin"><span>Entrar</span></a> 
    </li>
            <?php 
				
				
		}
		
	}
	
	
	static function topnav()
	{
?>




<style>
#statusLogin{
    display: none;
    color: red;
}
</style>

<link href="mycss/front.css" media="screen, projection" rel="stylesheet" type="text/css">

<fieldset id="signin_menu">
        <form method="post" id="signin" action="javascript:void(0);">
            <p>
            <label for="emailLogin">Email</label>
            <input id="emailLogin" name="emailLogin" title="emailLogin" tabindex="1" type="text">
            <label for="senhaLogin">Senha</label>
            <input id="senhaLogin" name="senhaLogin" title="senhaLogin" tabindex="2" type="password">
            <label id="statusLogin" ></label>
            </p>
            <input id="signin_submit" type="submit" tabindex="3" value="Enviar" />
            <a href="index.php?pg=formUsuario" id="cadastrar">Cadastrar-se</a>
        </form>            
<!--            <input id="remember" name="remember_me" value="1" tabindex="7" type="checkbox"><label for="remember">Remember me</label>-->
           
<!--          <p class="forgot"> <a href="#" id="resend_password_link">Forgot your password?</a> </p> <p class="forgot-username"> -->
<!--          <A id=forgot_username_link title="If you remember your password, try logging in with your email" href="#">Forgot your username?</A> </p>-->
       
    </fieldset>
    
    
    <script type="text/javascript" language="javascript">
$(function() {
    // Quando o formulario for enviado, essa funcao e chamada
    $("#signin_submit").click(function() {

        // Colocamos os valores de cada campo em uma variavel para facilitar a manipulacao
        var email = $("#emailLogin").val();
        var senha = $("#senhaLogin").val();
        
        // Exibe mensagem de carregamento
        $("#statusLogin").html("<img src='images/spinner.gif' alt='Enviando' />");
        // Fazemos a requisicao ajax com o arquivo enviaDadosUsuario.php e enviamos os valores de cada campo atraves do metodo POST
        $.post('includes/verificaDadosUsuario.php', {email: email, senha: senha }, function(resposta) {
                // Quando terminada a requisicao
                // Exibe a div status
                $("#statusLogin").fadeIn();
                // Se resposta for true, ou seja, nao ocorreu nenhum erro
                if (resposta == true) {
                    $("#statusLogin").fadeOut(); 
                    window.location="index.php";
                } 
                // Se a resposta e um erro
                else{
                    // Exibe o erro na div
                    $("#statusLogin").html(resposta);
                }
        });
    });
});
</script>
    
    
    <script type="text/javascript">
        $(document).ready(function() {

            $(".signin").click(function(e) {          
                e.preventDefault();
                $("fieldset#signin_menu").toggle();
                $(".signin").toggleClass("menu-open");
            });
            
            $("fieldset#signin_menu").mouseup(function() {
                return false
            });
            $(document).mouseup(function(e) {
                if($(e.target).parent("a.signin").length==0) {
                    $(".signin").removeClass("menu-open");
                    $("fieldset#signin_menu").hide();
                }
            });         
            
        });
</script>
<script src="js/jquery.tipsy.js" type="text/javascript"></script>
<script type='text/javascript'>
    $(function() {
      $('#forgot_username_link').tipsy({gravity: 'w'});   
    });
  </script>
    
<?php 
	}
	
	
	static function footer(){
		?>
<div id="footer">
<ul id="f-list">
	<li id="lastmod">Daniel Werle Arenhart</li>
	<li id="viewcount">arenhart.daniel@gmail.com</li>
	<li id="privacy"><a title="" href="index.php?pg=policy">política de privacidade</a></li>
	<li id="about"><a title="" href="index.php?pg=about">Sobre o site</a></li>
</ul>
</div>
		<?php 
	}
	
}
