<?php


require_once 'ProcuraControl.class.php';
require_once 'UsuariosControl.class.php';
require_once 'CategoriasControl.class.php';


$ref_usuario = UsuariosControl::getLoggedUserId();


if ($categoria = $_POST['categoria'])
{
    $procura->ref_usuario = $ref_usuario;

    //FIXME verificar se existe a categoria
    
    $procura->ref_categoria = CategoriasControl::getId($categoria);
    
    ProcuraControl::add($procura);
}

if ($ref_procura_delete = $_GET['delete'])
{
    ProcuraControl::remove($ref_procura_delete);
}

$procuras = ProcuraControl::getProcurasUsuario($ref_usuario);

?>

<form id="formularioCadastroUsuario" action="index.php?pg=procuras" method="post">
<strong>Categoria:</strong> <br />
<input id="categoria" type="text" name="categoria"
	class="categoriaInput" maxlength="100"
	size="30" /> <br />
<br />
<input id="enviar" type="submit" value="Enviar" />
</form>

<?php


echo '<table>';
foreach ($procuras as $procura)
{
    echo '<tr>';

    echo '<td><a title="Excluir" href="index.php?pg=procuras&delete='.$procura->id.'" ><img src="images/delete.png"></a></td>';
    echo '<td>'.$procura->categoria.'</td>';

    echo '</tr>';
}

echo '</table>';

?>
