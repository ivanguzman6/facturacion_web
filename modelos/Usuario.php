<?php 
//Incluimos inicialmente la conexion con la base de datos
require "../config/Conexion.php";

Class Usuario
{
	//Implementamos el constructor
	public function _construct()
	{

	}

	public function insertar($nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email,$cargo,$login,$clave,$imagen,$permisos)
	{
		$sql="INSERT INTO usuario (nombre,tipo_documento,num_documento,direccion,telefono,email,cargo,login,clave,imagen,condicion)
		VALUES ('$nombre','$tipo_documento','$num_documento','$direccion','$telefono','$email','$cargo','$login','$clave','$imagen','1')";

		//Se inserta el registro y se toma el id devuelto
		$idusuarionew=ejecutarConsulta_retornarID($sql);	
		$num_elementos = 0;
		$resultado=true;

		while($num_elementos<count($permisos))
		{
			$sql_detalle = "INSERT INTO usuario_permiso(idusuario,idpermiso) values ('$idusuarionew','$permisos[$num_elementos]')";
			ejecutarConsulta($sql_detalle) or $resultado = false;
			$num_elementos=$num_elementos+1;
		}
		return $resultado;
	}

	//Implementamos un metodo para editar registros
	public function editar($idusuario,$nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email,$cargo,$login,$clave,$imagen,$permisos)
	{
		$sql="UPDATE usuario SET nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email',cargo='$cargo',login='$login',clave='$clave',imagen='$imagen' 
		WHERE idusuario='$idusuario'";

		ejecutarConsulta($sql); 

		//Borramos los permisos del usuario para insertarlos de nuevo
		$sqlDel = "DELETE FROM usuario_permiso WHERE idusuario='$idusuario'";
		ejecutarConsulta($sqlDel);

		//Insertando los permisos del array
		$num_elementos = 0;
		$resultado=true;

		while($num_elementos<count($permisos))
		{
			$sql_detalle = "INSERT INTO usuario_permiso(idusuario,idpermiso) values ('$idusuario','$permisos[$num_elementos]')";
			ejecutarConsulta($sql_detalle) or $resultado = false;
			$num_elementos=$num_elementos+1;
		}
		return $resultado;
	}

	//Implementamos un metodo para desactivar registros
	public function desactivar($idusuario)
	{
		$sql="UPDATE usuario SET condicion='0' WHERE idusuario='$idusuario'";

		return ejecutarConsulta($sql); 
	}

	//Implementamos un metodo para activar registros
	public function activar($idusuario)
	{
		$sql="UPDATE usuario SET condicion='1 ' WHERE idusuario='$idusuario'";

		return ejecutarConsulta($sql); 
	}    

	//Implementar un metodo para mostrar los datos de un registro o modificar
	public function mostrar($idusuario)
	{
		$sql="SELECT * FROM usuario WHERE idusuario='$idusuario'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un metod para listar los registros
	public function listar()
	{
		$sql="SELECT * FROM usuario";
		return ejecutarConsulta($sql); 
	}

	//Implementar un metod para listar los registros y mostrar el select
	public function select()
	{
		$sql="SELECT * FROM usuario WHERE condicion=1";
		return ejecutarConsulta($sql); 
	}

	//Implementar un metod para listar los permisos marcados
	public function listarmarcados($idusuario)
	{
		$sql="SELECT * FROM usuario_permiso WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql); 
	}

	//Funcion para verificar el acceso al sistema
	public function verificar($login,$clave)
	{
		$sql="SELECT idusuario,nombre,tipo_documento,num_documento,telefono,email,cargo,imagen,login FROM usuario WHERE login='$login' AND clave='$clave' AND condicion='1'";
 
		return ejecutarConsulta($sql);	 
	}
}

?>