$(document).ready(function () {	

	$("#linha_nova_senha1").hide();
	$("#linha_nova_senha2").hide();	

	$("#nova_senha1").val("");
	$("#nova_senha2").val("");

	$("#alterar_senha").click(function () {
		$("#senha_atual").val("");
    	$("#nova_senha1").val("");
    	$("#nova_senha2").val("");

		if ($(this).prop("checked")) {
			$("#linha_nova_senha1").show();
			$("#linha_nova_senha2").show();
		}
		else {
			$("#linha_nova_senha1").hide();
			$("#linha_nova_senha2").hide();
		}
  	});

});


