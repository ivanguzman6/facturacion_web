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
	})
}
//Funcion limpiar
function limpiar()
{

	//Al objeto del formulario cuyo nombre es el siguiente, se le asigna un valor en blanco
	$("#idcategoria").val("");
	$("#nombre").val("");
	$("#descripcion").val("");
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
		$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
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
					url: '../ajax/categoria.php?op=listar',
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
	})
}

//Funcion para guardar o editar
function guardaryeditar(e)
{
	e.preventDefault(); //No se activara la accion predeterminada del evento
	$("#btnGuardar").prop("disabled",true);
	//el nombre 'formulario' hace referencia al 'id' que se le coloco al formulario en la vista 
	var formData = new FormData($("#formulario")[0]);
	
	//alert($("#formulario")[0][0]);

	$.ajax({
		url:"../ajax/categoria.php?op=guardaryeditar",
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
			tabla.api().ajax.reload();
		}

	});
	limpiar();	

}

function mostrar(vidcategoria)
{
	$.post("../ajax/categoria.php?op=mostrar",{idcategoria : vidcategoria},function(data,status)
	{
		data = JSON.parse(data);
		mostrarform(true);

		$("#nombre").val(data.nombre);
		$("#descripcion").val(data.descripcion);
		$("#idcategoria").val(data.idcategoria);
	})
}

function desactivar(vidcategoria)
{
	swal({
		  title: 'Desactivar Categoría',
		  text: "Está seguro de que desea desactivar la categoría?",
		  type: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Si, desactivar',
		  cancelButtonText: 'Cancelar'
		}).then(function () {
			$.post("../ajax/categoria.php?op=desactivar",{idcategoria : vidcategoria},function(e)
			{	
				swal('Listo',e,'success');
			  	tabla.api().ajax.reload();
			})
		}).catch(swal.noop)
}

function activar(vidcategoria)
{
	swal({
		  title: 'Activar Categoría',
		  text: "Está seguro de que desea activar la categoría?",
		  type: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Si, activar',
		  cancelButtonText: 'Cancelar'
		}).then(function () {
			$.post("../ajax/categoria.php?op=activar",{idcategoria : vidcategoria},function(e)
			{	
				swal('Listo',e,'success');
			  	tabla.api().ajax.reload();
			})
		}).catch(swal.noop)
}

init();