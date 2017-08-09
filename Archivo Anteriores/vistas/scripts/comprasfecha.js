//Variable que almacena todos los datos
var tabla;

//FUncion que se ejecuta al inicio
function init()
{
	listar();
	$("#fecha_desde").change(listar);
	$("#fecha_hasta").change(listar);
}


//funcion Listar
function listar()
{

	var vfecha_desde = $("#fecha_desde").val();
	var vfecha_hasta = $("#fecha_hasta").val();

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
					url: '../ajax/consultas.php?op=comprasfecha',
					data:{fecha_desde: vfecha_desde,fecha_hasta: vfecha_hasta},
					type: "get",
					dataType: "json",
					error: function(e)
					{
						console.log(e.responseText);
					} 

				},
		"bDestroy":true,
		"iDisplayLenght": 5, //Paginación
		"aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
		"order": [[0,"desc"]] //ordar por la primera columna, descendente		
	});
}


init();