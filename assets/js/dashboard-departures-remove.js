function searchDepartureForRemoving(bt,event){
	event.preventDefault();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	var div=$(bt).parent().parent();
	var input=$(div).find('input');
	$.post($(bt).attr('href'),{'d_id':$(input).val()}
		).done(function(data){
		var r=JSON.parse(data);
		if(r.success){
			$("#departures_details").html(r.template);
			hide_loadingOverlay(bt);
        	return;
		}
		$("#departures_details").html(r.msg.description.es);
		hide_loadingOverlay(bt);
        return;
	});	
}
function confirmRemoveOfDeparture(bt,event){
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	event.preventDefault();
	var url_by_post=$(bt).attr('href');
	$.post(url_by_post
		).done(function(data){
		var r=JSON.parse(data);
		if(r.success){
			alert(r.msg.es);
			location.reload();
			hide_loadingOverlay(bt);
			return;
		}
		alert(r.msg.es);
		hide_loadingOverlay(bt);
		return;
	});
}
