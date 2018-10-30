$(document).ready(function() {
	$("#submitLogin").click((e) => {
		e.preventDefault();
		$.post("?page=index&action=connect",
			$("#formLogin").serializeArray(),
			function(data) {
				var response = JSON.parse(data);
				if (response["ok"] == "ok") {
					location.href = "./chat/";
				}
			});
	});
});