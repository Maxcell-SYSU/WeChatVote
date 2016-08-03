$(document).ready(function(){
	refresh(true);
	setTimeout("atTime()",5000);
})

function atTime(){
	refresh(false);
	setTimeout("atTime()",5000);
}

function refresh(first){
	$.post("./getData.php",function(msg){
		var data=eval("("+msg+")");
		if(first)
			$("#content").empty();
		for(var i=0;i<data.num;i++){
			if(first)
				$("#content").append(
					$("<div id=\""+data.data[i].id+"\" />").append(
						$("<div class=\"col-xs-12 col-sm-12 col-md-6 col-md-offset-3 id\" />"),
						$("<div class=\"col-xs-12 col-sm-12 col-md-6 col-md-offset-3\" />").append(
							$("<div class=\"progress\" />").append(
								$("<div class=\"progress-bar progress-bar-warning vote\" role=\"progressbar\" style=\"min-width:1.5%\" />")
								)
							)
						)
					);
			$("#"+data.data[i].id+" .id").html(data.data[i].id+" "+data.data[i].team);
			$("#"+data.data[i].id+" .vote").width(data.data[i].vote*1.5+"%");
			$("#"+data.data[i].id+" .vote").html(data.data[i].vote);
		}
	});
}
