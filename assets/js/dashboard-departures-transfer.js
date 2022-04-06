function searchDepartureForTransfering(bt,event){
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
function confirm(bt,event){
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	event.preventDefault();
	var url_by_post=$(bt).attr('href');
	$.post(url_by_post
		).done(function(data){
		var r=JSON.parse(data);
		$("#content2").html(r.template);
		hide_loadingOverlay(bt);
		return;
	});
}
function searchDepartureAvailablesAccordingDeparture(bt,event){
	event.preventDefault();
	var departure_date=$('#departure').val();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	var url_by_post=$(bt).attr('href');
	$.post(url_by_post,{"d_date":departure_date}
	).done(function(data){
		var r=JSON.parse(data);
		$("#details").html(r.template);
		hide_loadingOverlay(bt);
		return;
	});
}
function showTemplateDepartureChangeDepartureDateStep3(bt){
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	event.preventDefault();
	var url_by_post=$(bt).attr('href');
	$.post(url_by_post
		).done(function(data){
		var r=JSON.parse(data);
		$("#content2").html(r.template);
		hide_loadingOverlay(bt);
		return;
	});
}
function ChangeDepartureDateConfirm(bt){
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
			if(!r.success){
				if(r.msg.id==2){
					alert(r.msg.description.es);
					$("#content2").html(r.template);
					hide_loadingOverlay(bt);
					return;
				}
			}
			
		});
}