//Variable que almacena todos los datos
var tabla;

//FUncion que se ejecuta al inicio
function init()
{
	mostrarform(false);
	listar();

	$("#formulario").on("submit",function(e)
	{
		guardaryeditar(e);	
	});

	$.post("../ajax/venta.php?op=selectCliente",function(r){
		$("#idcliente").html(r);
		$("#idcliente").selectpicker('refresh');
	});
}
//Funcion limpiar
function limpiar()
{

	$("#idventa").val("");
	$("#idcliente").val("");
	$("#idusuario").val("");
	$("#tipo_comprobante").val("");
	$("#serie_comprobante").val("");
	$("#num_comprobante").val("");
	$("#fecha_hora").val("");
	$("#impuesto").val("0");	
	$("#tipo_comprobante").prop("selectedIndex", -1);

	$("#total_venta").val("");
	$(".filas").remove();
	$("#total").html("0");

	//Obtenemos la fecha actual
	var now=new Date();
	var day = ("0" + now.getDate()).slice(-2);
	var month = ("0" + (now.getMonth() + 1)).slice(-2);
	var today = now.getFullYear()+"-"+(month)+"-"+(day);
	$('#fecha_hora').val(today);

	//Marcamos el primer tipo_documento
	$("#tipo_comprobante").val("Boleta");
	$("#tipo_comprobante").selectpicker('refresh');


} 

//Función mostrar formulario, recibe valores en la variable llamada flag
function mostrarform(flag)
{
	limpiar();
	//si el flag es true
	if(flag)
	{
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		//$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
		listarArticulos();	

		$("#btnGuardar").hide();
		$("#btnCancelar").show();
		detalles=0;
		$("#btnAgregarArt").show();

	}
	else
	{
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();	
	}
}

//Funcion cancelarform
function cancelarform()
{
	limpiar();
	mostrarform(false);
}

//funcion Listar
function listar()
{
	tabla=$('#tbllistado').dataTable(
	{
		"aProcessing":true,//Activamos el procesamiento del datatables
		"aServerSide":true,//Paginación y filtrado realizados por el servidor
		dom: 'Bfrtip',//Definimos los elementos del control de tabla
		buttons: [
					'copyHtml5',
					'excelHtml5',
					'csvHtml5',
					'pdf'
				],
		"ajax":
				{
					url: '../ajax/venta.php?op=listar',
					type: "get",
					dataType: "json",
					error: function(e)
					{
						console.log(e.responseText);
					} 

				},
		"bDestroy":true,
		"iDisplayLenght": 10, //Paginación
		"aLengthMenu": [[10, 20, 40, 60, -1], [10, 20, 40, 60, "All"]],
		"order": [[0,"desc"]] //ordar por la primera columna, descendente		
	})
}

function listarArticulos()
{
	tabla=$('#tblarticulos').dataTable(
	{
		"aProcessing":true,//Activamos el procesamiento del datatables
		"aServerSide":true,//Paginación y filtrado realizados por el servidor
		dom: 'Bfrtip',//Definimos los elementos del control de tabla
		buttons: [
					
				],
		"ajax":
				{
					url: '../ajax/venta.php?op=listarArticulos',
					type: "get",
					dataType: "json",
					error: function(e)
					{
						console.log(e.responseText);
					} 

				},
		"bDestroy":true,
		"iDisplayLenght": 10, //Paginación
		"aLengthMenu": [[10, 20, 40, 60, -1], [10, 20, 40, 60, "All"]],
		"order": [[0,"desc"]] //ordar por la primera columna, descendente		
	})
}

//Funcion para guardar o editar
function guardaryeditar(e)
{
	e.preventDefault(); //No se activara la accion predeterminada del evento
	//$("#btnGuardar").prop("disabled",true);
	//el nombre 'formulario' hace referencia al 'id' que se le coloco al formulario en la vista 
	var formData = new FormData($("#formulario")[0]);
	
	//alert($("#formulario")[0][0]);

	$.ajax({
		url:"../ajax/venta.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function(datos)
		{
			swal( 
				  'Listo',
				  datos,
				)

			//bootbox.alert(datos);
			mostrarform(false);
			listar();
		}

	});
	limpiar();	

}

function mostrar(vidventa)
{
	$.post("../ajax/venta.php?op=mostrar",{idventa : vidventa},function(data,status)
	{
		data = JSON.parse(data);
		mostrarform(true);

		$("#idventa").val(data.idventa);
		$("#idcliente").val(data.idcliente);
		$("#idcliente").selectpicker('refresh');
		$("#tipo_comprobante").val(data.tipo_comprobante);
		$("#tipo_comprobante").selectpicker('refresh');
		$("#num_comprobante").val(data.num_comprobante);
		$("#fecha_hora").val(data.fecha);
		$("#impuesto").val(data.impuesto);

		//Ocultar y mostrar los botones
		$("#btnGuardar").hide();
		$("#btnCancelar").show();
		$("#btnAgregarArt").hide();
		
	});

	$.post("../ajax/venta.php?op=listarDetalle&id="+vidventa,function(r){
		$("#detalles").html(r);
	})
}

function anular(vidventa)
{
	swal({
		  title: 'Anular Venta',
		  text: "Está seguro de que desea anular la venta?",
		  type: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Si, anular',
		  cancelButtonText: 'Cancelar'
		}).then(function () {
			$.post("../ajax/venta.php?op=anular",{idventa : vidventa},function(e)
			{	
				swal('Listo',e,'success');
			  	tabla.api().ajax.reload();
			})
		}).catch(swal.noop)
}

//Declaracion de las variables necesarias para las ventas y sus detalles
var impuesto=18;
var cont=0;
var detalles=0;

//$("#guardar").hide();
$("#btnGuardar").hide();
$("#tipo_comprobante").change(marcarImpuesto);

function marcarImpuesto()
{
	var tipo_comprobante=$("#tipo_comprobante option:selected").text();

	if(tipo_comprobante=='Factura')
	{
		$("#impuesto").val(impuesto);
	}
	else
	{
		$("#impuesto").val("0");
	}
}

function agregarDetalle(vidarticulo,varticulo,vprecio_venta)
{
	var cantidad=1;
	var descuento=0;
	
	if(vidarticulo!=="")
	{
		var subtotal = (cantidad * vprecio_venta) - descuento;
		var fila = '<tr class="filas" id="fila'+cont+'">' +
		'<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle('+cont+')"><i class="fa fa-close"></i></button></td>' +
		'<td><input type="hidden" name="idarticulo[]" value="'+vidarticulo+'">'+varticulo+'</td>' +
		'<td><input type="number" name="cantidad[]" id="cantidad[]" value="'+cantidad+'"></td>' +
		'<td><input type="number" name="precio_venta[]" id="precio_venta[]" value="'+vprecio_venta+'"></td>' +
		'<td><input type="number" name="descuento[]" value="'+descuento+'"></td>' +
		'<td><span name="subtotal" id="subtotal'+cont+'">'+subtotal+'</span></td>' +
		'<td><button type="button" onclick="modificarSubtotales()" class="btn btn-info"><i class="fa fa-refresh"></i></button></td>'+
		'</tr>';

		cont++;
		detalles++;
		$('#detalles').append(fila);
		modificarSubtotales();

	}
	else
	{
		alert("Error al ingresar el detalle, revisar los datos del artículo");
	}

}

function modificarSubtotales()
{
	var cant = document.getElementsByName("cantidad[]");
    var prec = document.getElementsByName("precio_venta[]");
    var desc = document.getElementsByName("descuento[]");
    var sub = document.getElementsByName("subtotal");

    for(var i=0; i<cant.length; i++)
    {
    	var vcantidad=cant[i];
    	var vprecio=prec[i];
    	var vdescuento=desc[i];
    	var vsubtotal=sub[i];

    	vsubtotal.value = (vcantidad.value * vprecio.value)-vdescuento.value;

    	document.getElementsByName("subtotal")[i].innerHTML = vsubtotal.value;
    }

    calcularTotales();
}

function calcularTotales()
{
	var sub = document.getElementsByName("subtotal");
	var total = 0.0;

	for(var i=0; i<sub.length; i++)
	{
		total += document.getElementsByName("subtotal")[i].value;
	}

	$("#total").html("$ "+total);
	$("#total_venta").val(total);

	evaluar();

}


function evaluar()
{
	if (detalles>0)
	{
		$("#btnGuardar").show();
	}
	else
	{
		$("#btnGuardar").hide();
		cont=0;
	}
}

function eliminarDetalle(vindice)
{
	$("#fila" + vindice).remove();
	detalles--;
	calcularTotales();
}
init();