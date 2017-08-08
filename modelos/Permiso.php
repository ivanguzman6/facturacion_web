<?php 
//Incluimos inicialmente la conexion con la base de datos
require "../config/Conexion.php";

Class Permiso
{
	//Implementamos el constructor
	public function _construct()
	{

	}

	
	//Implementar un metod para listar los registros
	public function listar()
	{
		$sql="SELECT * FROM permiso";
		return ejecutarConsulta($sql); 
	}
}

?>