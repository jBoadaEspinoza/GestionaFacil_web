function searchReservationToRemove(bt,event){
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	event.preventDefault();
	var url_by_post=$(bt).attr('href');
	var r_id=$(bt).parent().parent().find('input').val();
	$.post(url_by_post,{"r_id":r_id}
	).done(function(data){
		 var r=JSON.parse(data);
		 if(!r.success){
 			var input=$(bt).parent().parent().find('input');
 			$(input).focus();
 			$("#reservation_details").html(r.msg.description);
 			hide_loadingOverlay(bt);
		 	return;
		 }
		 hide_loadingOverlay(bt);
		 var input=$(bt).parent().parent().find('input');
	 	 $(input).attr('disabled','disabled');
		 $(bt).addClass('disabled');
		 $("#reservation_details").html(r.template);
		 return;
	});
}
function confirmRemoveOfReservation(bt,event){
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