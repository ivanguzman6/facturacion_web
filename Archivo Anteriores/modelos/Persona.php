<?php 
//Incluimos inicialmente la conexion con la base de datos
require "../config/Conexion.php";

Class Persona
{
	//Implementamos el constructor
	public function _construct()
	{

	}

	public function insertar($tipo_persona,$nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email)
	{
		$sql="INSERT INTO persona (tipo_persona,nombre,tipo_documento,num_documento,direccion,telefono,email)
		VALUES ('$tipo_persona','$nombre','$tipo_documento','$num_documento','$direccion','$telefono','$email')";

		return ejecutarConsulta($sql); 

	}

	//Implementamos un metodo para editar registros
	public function editar($idpersona,$tipo_persona,$nombre,$tipo_documento,$num_documento,$direccion,$telefono,$email)
	{
		$sql="UPDATE persona SET tipo_persona='$tipo_persona',nombre='$nombre',tipo_documento='$tipo_documento',
		num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email'
		WHERE idpersona='$idpersona'";

		return ejecutarConsulta($sql); 
	}

	//Implementamos un metodo para elimiar registros
	public function eliminar($idpersona)
	{
		$sql="DELETE from persona WHERE idpersona='$idpersona'";

		return ejecutarConsulta($sql); 
	}

	//Implementar un metodo para mostrar los datos de un registro o modificar
	public function mostrar($idpersona)
	{
		$sql="SELECT * FROM persona WHERE idpersona='$idpersona'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un metod para listar los proveedores
	public function listarp()
	{
		$sql="SELECT * FROM persona WHERE tipo_persona='Proveedor'";
		return ejecutarConsulta($sql); 
	}

	//Implementar un metod para listar los clientes
	public function listarc()
	{
		$sql="SELECT * FROM persona WHERE tipo_persona='Cliente'";
		return ejecutarConsulta($sql); 
	}

}

?>