$(document).ready(function () {
	let lastmsg = 1;
	var pseudo;
	$("#contacts").on('click', 'li' , function(obj){;
	$("#imgContact").attr("src", this.attributes['img'].value)
		$("#msgDetails").html("");
		$("#contacts li").removeClass("active");
		$(this).addClass("active");
		pseudo = this.attributes;
		$("#inContactWith").html(pseudo['value'].value);
		$.post("?page=chat&action=getUserMessages",
			{pseudo: pseudo['value'].value})
		.done((data) => {
			var res = JSON.parse(data);
			$.each(res[0], (k, v) => {
				lastmsg = v.id;
				if (v.sender == pseudo['value'].value) {
					$("#msgDetails").append("<li class='replies'><img src='"+this.attributes['img'].value+
						"'/><p>"+v.content+"</p></li>");
				} else {
					$("#msgDetails").append("<li class='sent'><img src='"+$("#profile-img").attr("src")+
						"' /><p>"+v.content+"</p></li>");
				}
			});
		});
		function getLastMessage() {
			$.post("?page=chat&action=getLastMessage",
				{id_msg: parseInt(lastmsg),
				 pseudo: pseudo['value'].value},
				function(data) {
					var obj = JSON.parse(data);
					$.each(obj[1], (key, v) => {
			lastmsg = (v.id > lastmsg) ? v.id : lastmsg;
						if (v.sender == pseudo['value'].value) {
						$("#imgContact").attr("src", v.img)
						$("#msgDetails").append("<li class='replies'><img src='"+pseudo['img'].value+
							"'/><p>"+v.content+"</p></li>");
					} else {
						$("#msgDetails").append("<li class='sent'><img src='"+$("#profile-img").attr("src")+
							"' /><p>"+v.content+"</p></li>");
					}
					});
				});
		}
		setInterval(getLastMessage, 2500);
	});

	function newMessage() {
		message = $(".message-input input").val();
		if($.trim(message) == '') {
			return false;
		}
		$.post("?page=chat&action=send",
			{pseudo: $("#inContactWith").text(),
			content: message})
		.done((data) => {
			var obj = JSON.parse(data);
			$.each(obj, (k, v) => {
				if (k == "ok") {
					$('<li class="sent"><img src="'+$("#profile-img").attr("src")+'" alt="" /><p>' + message + '</p></li>').appendTo($('.messages ul'));
					$('.message-input input').val(null);
					$('.contact.active .preview').html('<span>'+new Date($.now()));
					$(".messages").animate({ scrollTop: $(document).height() }, "fast");
				}
			});
		});
	};

	$('.submit').click(function() {
		newMessage();
	});

	$(window).on('keydown', function(e) {
		if (e.which == 13) {
			newMessage();
			return false;
		}
	});
});