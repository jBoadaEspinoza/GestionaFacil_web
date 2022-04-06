function searchReservationToChangeDepartureDate(bt,event){
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

function showTemplateReservationChangeDepartureDateStep2(bt,event){
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
function showTemplateReservationChangeDepartureDateStep3(bt,event){
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

function searchDepartureAvailablesAccordingReservation(bt,event){
	var departure_date=$("#change_date_of_reservation").find("input[name=d_date]").val();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	event.preventDefault();
	var url_by_post=$(bt).attr('href');
	$.post(url_by_post,{"d_date":departure_date}
		).done(function(data){
			var r=JSON.parse(data);
			$("#details").html(r.template);
			hide_loadingOverlay(bt);
			return;
		});
}

function ChangeDepartureDateConfirm(bt,event){
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
function selectSeat(bt,event){
	event.preventDefault();
	var num_seat=$(bt).html();
	if($(bt).hasClass('btn-success')){
		var nseats=parseInt($("#seats_dashboard").find('h1').find('b').html());
		if(nseats>0){
			$(bt).removeClass('btn-success');
			$(bt).addClass('btn-warning');
			$("#seats_dashboard").find('h1').find('b').html(nseats-1);
			if((nseats-1)==0){
				$("#finalize").removeAttr('disabled');
			}else{
				$("#finalize").attr('disabled','disabled');
			}
			var num_seat_selected=parseInt($("#p_seats").find('h6').find('span').html());
			$(bt).append('<div role="detail">'+
					 '	<input type="hidden" name="seats[]" value="'+num_seat+'" />'+
					 '</div>');
		}
		return;
	}
	if($(bt).hasClass('btn-warning')){
		$(bt).removeClass('btn-warning');
		$(bt).addClass('btn-success');
		var nseats=parseInt($("#seats_dashboard").find('h1').find('b').html());
		$("#seats_dashboard").find('h1').find('b').html(nseats+1);
		if((nseats+1)>0){
			$("#finalize").attr('disabled','disabled');
		}else{
			$("#finalize").removeAttr('disabled');
		}
		$(bt).find('div[role="detail"]').remove();
		return;
	}}

function finalizeChangeDepartureDate(bt,event){
	event.preventDefault();
	var seats_array=$("#seats_dashboard").serializeArray();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	$.post($("#seats_dashboard").attr("action"),seats_array
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