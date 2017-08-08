<?php 
//Si aun no se ha iniciado la sesion
if(strlen(session_id()) < 1)
  session_start();

require_once "../modelos/Venta.php";

$venta=new Venta();

$idventa=isset($_POST["idventa"])? limpiarCadena($_POST["idventa"]):"";
$idcliente=isset($_POST["idcliente"])? limpiarCadena($_POST["idcliente"]):"";
$idusuario=$_SESSION["idusuario"];
$tipo_comprobante=isset($_POST["tipo_comprobante"])? limpiarCadena($_POST["tipo_comprobante"]):"";
$serie_comprobante=isset($_POST["serie_comprobante"])? limpiarCadena($_POST["serie_comprobante"]):"";
$num_comprobante=isset($_POST["num_comprobante"])? limpiarCadena($_POST["num_comprobante"]):"";
$fecha_hora=isset($_POST["fecha_hora"])? limpiarCadena($_POST["fecha_hora"]):"";
$impuesto=isset($_POST["impuesto"])? limpiarCadena($_POST["impuesto"]):"";
$total_venta=isset($_POST["total_venta"])? limpiarCadena($_POST["total_venta"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (empty($idventa)){
			$rspta=$venta->insertar($idcliente,$idusuario,$tipo_comprobante,$serie_comprobante,$num_comprobante,$fecha_hora,$impuesto,$total_venta,$_POST["idarticulo"],$_POST["cantidad"],$_POST["precio_venta"],$_POST["descuento"]	);
			echo $rspta ? "Venta registrada" : "Venta no se pudo registrar";
		}
		else {
			
		}
		break;

	case 'anular':
		$rspta=$venta->anular($idventa);
 		echo $rspta ? "Venta Anulada" : "Venta no se puede anular";
		break;
 
	case 'mostrar':
		$rspta=$venta->mostrar($idventa);
 		//Codificar el resultado utilizando json
 		echo json_encode($rspta);
		break;

	case 'listarDetalle':
		//Recibimos el idventa

		$id=$_GET['id'];
		$rspta=$venta->listarDetalle($id);
		$total=0;

		echo '<thead style="background-color:#A9D0F5">
                                  <th>Opciones</th>
                                  <th>Artículo</th>
                                  <th>Cantidad</th>
                                  <th>Precio Venta</th>
                                  <th>Descuento</th>
                                  <th>Subtotal</th>                                  
                                </thead>  ';

		while ($reg=$rspta->fetch_object())
 		{
 			echo '<tr class="filas"><td></td><td>'. $reg->nombre .'</td><td>'. $reg->cantidad .'</td><td>'. $reg->precio_venta .'</td><td>'. $reg->descuento .'</td><td>'. (($reg->precio_venta * $reg->cantidad)-$reg->descuento) .'</td></tr>';

 			$total=$total + (($reg->precio_venta * $reg->cantidad)-$reg->descuento);
 		}


 		echo '<tfoot>
                                  <th>TOTAL</th>
                                  <th></th>
                                  <th></th>
                                  <th></th>
                                  <th></th>
                                  <th><h4 id="total">$ '. $total . '</h4><input type="hidden" name="total_venta" id="total_venta"></th>        
                                </tfoot>';

		break;
	
	case 'listar':
		$rspta=$venta->listar();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object())
 		{
 			$data[]=array(
 				"0"=>($reg->estado=='Aceptado')?'<button class="btn btn-warning" onclick="mostrar(' . $reg->idventa . ')"><i class="fa fa-eye"></i></button>'.
 					' <button class="btn btn-danger" onclick="anular(' . $reg->idventa . ')"><i class="fa fa-close"></i></button>':
 					' <button class="btn btn-warning" onclick="mostrar(' . $reg->idventa . ')"><i class="fa fa-pencil"></i></button>',
 				"1"=>$reg->idventa,
 				"2"=>$reg->fecha,
 				"3"=>$reg->cliente,
 				"4"=>$reg->usuario,
 				"5"=>$reg->tipo_comprobante,
 				"6"=>$reg->serie_comprobante . '-' . $reg->num_comprobante,
 				"7"=>$reg->total_venta,
 				"8"=>($reg->estado=='Aceptado')?'<span class="label bg-green" style="font-size:100%">Aceptado</span>':'<span class="label bg-red" style="font-size:100%">Anulado</span>'
 				);
 		}
 		$results = array(
 			//"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

		break;


	case 'selectCliente':
		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarC();

		while ($reg = $rspta->fetch_object())
		{
			echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . '</option>'; 
		}
		break;

	case 'listarArticulos':
		require_once "../modelos/Articulo.php";

		$articulo = new Articulo();

		$rspta=$articulo->listarActivosVentas();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object())
 		{
 			$data[]=array(
 				"0"=>'<button class="btn btn-warning" onclick="agregarDetalle(' . $reg->idarticulo . ',\'' . $reg->nombre . '\',' . $reg->precio_venta . ')"><span class="fa fa-plus"></span></button>',
 				"1"=>$reg->idarticulo,
 				"2"=>$reg->categoria,
 				"3"=>$reg->nombre,
 				"4"=>$reg->codigo,
 				"5"=>$reg->stock,
 				"6"=>$reg->precio_venta,
 				"7"=>$reg->descripcion,
 				"8"=>"<img src='../files/articulos/".$reg->imagen."' height='50px' width='50px' >"
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