function save_departure_opening(bt,event){
	event.preventDefault();
	var form=$("#departures_opening");
	$.post($(form).attr('action'),$(form).serializeArray()
	).done(function(data){
		var r=JSON.parse(data);
		if(r.success){
			alert(r.msg);
			location.reload();
			hide_loadingOverlay(bt);
			return;
		}	
	});
}