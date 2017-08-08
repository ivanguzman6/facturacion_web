<?php 
require_once "../modelos/Consultas.php";

$consultas=new Consultas();

$idcategoria=isset($_POST["idcategoria"])? limpiarCadena($_POST["idcategoria"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$descripcion=isset($_POST["descripcion"])? limpiarCadena($_POST["descripcion"]):"";

switch ($_GET["op"]){
	case 'comprasfecha':
		$fecha_desde=$_REQUEST["fecha_desde"];
		$fecha_hasta=$_REQUEST["fecha_hasta"];

		$rspta=$consultas->comprasfecha($fecha_desde,$fecha_hasta);
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object())
 		{
 			$data[]=array(
 				"0"=>$reg->fecha,
 				"1"=>$reg->usuario,
 				"2"=>$reg->proveedor,
 				"3"=>$reg->tipo_comprobante,
 				"4"=>$reg->serie_comprobante . ' ' . $reg->num_comprobante,
 				"5"=>$reg->total_compra,
 				"6"=>$reg->impuesto,
 				"7"=>($reg->estado=='Aceptado')?'<span class="label bg-green" style="font-size:100%">Aceptado</span>':'<span class="label bg-red" style="font-size:100%">Anulado</span>'
 				);
 		}
 		$results = array(
 			//"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

		break;

    case 'ventasfecha':
        $fecha_desde=$_REQUEST["fecha_desde"];
        $fecha_hasta=$_REQUEST["fecha_hasta"];
        $idcliente=$_REQUEST["idcliente"];

        $rspta=$consultas->ventasfechacliente($fecha_desde,$fecha_hasta,$idcliente);
        //Vamos a declarar un array
        $data= Array();

        while ($reg=$rspta->fetch_object())
        {
            $data[]=array(
                "0"=>$reg->fecha,
                "1"=>$reg->usuario,
                "2"=>$reg->cliente,
                "3"=>$reg->tipo_comprobante,
                "4"=>$reg->serie_comprobante . ' ' . $reg->num_comprobante,
                "5"=>$reg->total_venta,
                "6"=>$reg->impuesto,
                "7"=>($reg->estado=='Aceptado')?'<span class="label bg-green" style="font-size:100%">Aceptado</span>':'<span class="label bg-red" style="font-size:100%">Anulado</span>'
            );
        }
        $results = array(
            //"sEcho"=>1, //Información para el datatables
            "iTotalRecords"=>count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
            "aaData"=>$data);
        echo json_encode($results);

        break;

}
?>