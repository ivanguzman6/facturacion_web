<?php 
//Incluimos inicialmente la conexion con la base de datos
require "../config/Conexion.php";

Class Consultas
{
	//Implementamos el constructor
	public function _construct()
	{

	}

	
	//Implementar un metod para listar los registros
	public function comprasfecha($fecha_desde,$fecha_hasta)
	{
		$sql="SELECT DATE(i.fecha_hora) as fecha, u.nombre as usuario,p.nombre as proveedor, i.tipo_comprobante,i.serie_comprobante, i.num_comprobante, i.total_compra, i.impuesto,i.estado FROM ingreso i INNER JOIN persona p on i.idproveedor=p.idpersona INNER JOIN usuario u on i.idusuario=u.idusuario WHERE date(i.fecha_hora)>='$fecha_desde' and DATE(i.fecha_hora)<='$fecha_hasta'";
		return ejecutarConsulta($sql); 
	}

    //Implementar un metod para listar los registros
    public function ventasfechacliente($fecha_desde,$fecha_hasta,$idcliente)
    {
        $sql="SELECT DATE(v.fecha_hora) as fecha, u.nombre as usuario,p.nombre as cliente, v.tipo_comprobante,v.serie_comprobante, v.num_comprobante, v.total_venta, v.impuesto,v.estado FROM venta v INNER JOIN persona p on v.idcliente=p.idpersona INNER JOIN usuario u on v.idusuario=u.idusuario WHERE date(v.fecha_hora)>='$fecha_desde' and DATE(v.fecha_hora)<='$fecha_hasta' and v.idcliente='$idcliente'";
        return ejecutarConsulta($sql);
    }

    public function totalcomprahoy()
    {
        $sql="SELECT IFNULL(SUM(total_compra),0) as total_compra FROM ingreso WHERE DATE(fecha_hora)=curdate()";

        return ejecutarConsulta($sql);
    }

    public function totalventahoy()
    {
        $sql="SELECT IFNULL(SUM(total_venta),0) as total_venta FROM venta WHERE DATE(fecha_hora)=curdate()";

        return ejecutarConsulta($sql);
    }

    public function comprasultimos_10dias()
    {
        $sql="SELECT CONCAT(DAY(fecha_hora),'-',MONTH(fecha_hora)) as fecha,SUM(total_compra) as total FROM ingreso WHERE estado='Aceptado' GROUP by fecha_hora ORDER BY fecha_hora DESC limit 0,10";

        return ejecutarConsulta($sql);

    }

    public function ventasultimos_12meses()
    {
        $sql="SELECT DATE_FORMAT(fecha_hora,'%M') as fecha,SUM(total_venta) as total FROM venta WHERE estado='Aceptado' GROUP by MONTH(fecha_hora) ORDER BY fecha_hora DESC limit 0,12";

        return ejecutarConsulta($sql);

    }

}

?>