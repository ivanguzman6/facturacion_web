<?php 
session_start();
require_once "../modelos/Usuario.php";

$usuario=new Usuario();

$idusuario=isset($_POST["idusuario"])? limpiarCadena($_POST["idusuario"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$tipo_documento=isset($_POST["tipo_documento"])? limpiarCadena($_POST["tipo_documento"]):"";
$num_documento=isset($_POST["num_documento"])? limpiarCadena($_POST["num_documento"]):"";
$direccion=isset($_POST["direccion"])? limpiarCadena($_POST["direccion"]):"";
$telefono=isset($_POST["telefono"])? limpiarCadena($_POST["telefono"]):"";
$email=isset($_POST["email"])? limpiarCadena($_POST["email"]):"";
$cargo=isset($_POST["cargo"])? limpiarCadena($_POST["cargo"]):"";
$login=isset($_POST["login"])? limpiarCadena($_POST["login"]):"";
$clave=isset($_POST["clave"])? limpiarCadena($_POST["clave"]):"";
$imagen=isset($_POST["imagen"])? limpiarCadena($_POST["imagen"]):"";

$operacion=$_GET["op"];

switch ($operacion){
	case 'guardaryeditar':
		if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name']))
		{
			$imagen=$_POST["imagenactual"];
		}
		else
		{
			$ext = explode(".", $_FILES['imagen']['name']);
			if($_FILES['imagen']['type']=="image/jpg" || $_FILES['imagen']['type']=="image/jpeg" || $_FILES['imagen']['type']=="image/png" 
			|| $_FILES['imagen']['type']=="image/bmp")
			{
				//renombrando imagen por un nombre aleatorio (compuesto por el tiempo) mas la extension original
				$imagen = round(microtime(true)) . '.' . end($ext);

				move_uploaded_file($_FILES['imagen']['tmp_name'],'../files/usuarios/' . $imagen);
			}
		}
		//Esto es para encryptar la clave	
		$clavehash=hash("SHA256",$clave);

		if (empty($idusuario)){
			$rspta=$usuario->insertar($nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email,$cargo,$login,$clavehash,$imagen,$_POST["permiso"]);
			echo $rspta ? "Usuario registrado" : "Usuario no se pudo registrar los datos del usuario";
		}
		else {
			$rspta=$usuario->editar($idusuario,$nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email,$cargo,$login,$clavehash,$imagen,$_POST["permiso"]);
			echo $rspta ? "Usuario actualizado" : "Usuario no se pudo actualizar los datos del usuario";
		}
		break;

	case 'desactivar':
		$rspta=$usuario->desactivar($idusuario);
 		echo $rspta ? "Usuario Desactivado" : "Usuario no se puede desactivar";
 		break;

	case 'activar':
		$rspta=$usuario->activar($idusuario);
 		echo $rspta ? "Usuario activado" : "Usuario no se puede activar";
 		break;

	case 'mostrar':
		$rspta=$usuario->mostrar($idusuario);
 		//Codificar el resultado utilizando json
 		echo json_encode($rspta);
 		break;

	case 'listar':
		$rspta=$usuario->listar();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object())
 		{
 			$data[]=array(
 				"0"=>($reg->condicion)?'<button class="btn btn-warning" onclick="mostrar('.$reg->idusuario.')"><i class="fa fa-pencil"></i></button>'.
 					' <button class="btn btn-danger" onclick="desactivar('.$reg->idusuario.')"><i class="fa fa-close"></i></button>':
 					' <button class="btn btn-warning" onclick="mostrar('.$reg->idusuario.')"><i class="fa fa-pencil"></i></button>'.
 					' <button class="btn btn-primary" onclick="activar('.$reg->idusuario.')"><i class="fa fa-check"></i></button>',
 				"1"=>$reg->idusuario,
 				"2"=>$reg->nombre,
 				"3"=>$reg->tipo_documento,
 				"4"=>$reg->num_documento,
 				"5"=>$reg->telefono,
 				"6"=>$reg->email,
 				"7"=>$reg->login,
 				"8"=>"<img src='../files/usuarios/".$reg->imagen."' height='50px' width='50px' >",
 				"9"=>($reg->condicion)?'<span class="label bg-green" style="font-size:100%">Activado</span>':'<span class="label bg-red" style="font-size:100%">Desactivado</span>'
 				);
 		}
 		$results = array(
 			//"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

		break;

	case 'permisos':
		//Obtenemos todos los permisos de la trabla de permisos
		require_once "../modelos/Permiso.php";
		$permiso = new Permiso();
		$rspta = $permiso->listar();

		//obtener los permisos marcados del usuario
		$id=$_GET['id'];
		$marcados=$usuario->listarmarcados($id);
		$valores=array();


		//Almacenar los permisos almacenados al usuario en el array
		while($per=$marcados->fetch_object())
		{
			//Almacenando los permisos en el array de valores
			array_push($valores, $per->idpermiso);
		}	 

		//Mostramos la lista de permisos en la vista y si esta marcados o no
		while ($reg=$rspta->fetch_object())
 		{
 			//Esta variable tendra asignado un string dependiendo si el idpermiso esta o no dentro del array de permisos del usuario
 			$vchequed=in_array($reg->idpermiso,$valores)?'checked':'';

 			echo '<li> <input type="checkbox" '.$vchequed.' name="permiso[]" value="'.$reg->idpermiso.'">'.$reg->nombre.'</li>';
		}
		break;

	case 'verificar':
		$login_acceso = $_POST['login_acceso'];
		$clave_acceso = $_POST['clave_acceso'];
		$clavehash=hash("SHA256",$clave_acceso);

		$rspta=$usuario->verificar($login_acceso,$clavehash);
		$fetch=$rspta->fetch_object();

		//si existe el objeto reg
		if(isset($fetch))
		{
			$_SESSION['idusuario']=$fetch->idusuario;
			$_SESSION['nombre']=$fetch->nombre;
			$_SESSION['imagen']=$fetch->imagen;
			$_SESSION['login']=$fetch->login;

			//Obtenemos los permisos marcados para este usuario
			$marcados = $usuario->listarmarcados($fetch->idusuario);

			//Declaramos un array para almacenar todos los permisos marcados
			$valores=array();

			//Guardando permisos en el array
			while ($permiso=$marcados->fetch_object())
	 		{
	 			array_push($valores, $permiso->idpermiso);
			}

			//Determinamos los accesos del usuario
			in_array(1,$valores)?$_SESSION['escritorio']=1:$_SESSION['escritorio']=0;
			in_array(2,$valores)?$_SESSION['almacen']=1:$_SESSION['almacen']=0;
			in_array(3,$valores)?$_SESSION['compras']=1:$_SESSION['compras']=0;
			in_array(4,$valores)?$_SESSION['ventas']=1:$_SESSION['ventas']=0;
			in_array(5,$valores)?$_SESSION['acceso']=1:$_SESSION['acceso']=0;
			in_array(6,$valores)?$_SESSION['consultac']=1:$_SESSION['consultac']=0;
			in_array(7,$valores)?$_SESSION['consultav']=1:$_SESSION['consultav']=0;

		}
		echo json_encode($fetch);
		break;

	case 'salir':
		//Limpiamos las variables de sesion
		session_unset();
		//Destruimos la sesion
		session_destroy();
		//Redireccionamos al Login
		header("Location: ../index.php");

		break;

}
?>