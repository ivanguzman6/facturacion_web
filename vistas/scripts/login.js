$("#frmAcceso").on('submit',function(e)
{
	e.preventDefault();
	login_acceso=$("#login_acceso").val();
	clave_acceso=$("#clave_acceso").val();

	$.post("../ajax/usuario.php?op=verificar",{"login_acceso":login_acceso,"clave_acceso":clave_acceso},function(data){
			//Si la funcion retorno datos
			if(data!="null")
			{
				//Redireccionar a este archivo
				$(location).attr("href","escritorio.php");
			}
			else
			{
				swal('usuario y/o contraseña incorrectos')
			}
		});
})