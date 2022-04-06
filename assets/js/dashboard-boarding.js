

function setOnboard(ck,event){
	var onboard=0;
	var reservation_detail_id=$(ck).attr('data-reservation-detail-id');
	if($(ck).is(':checked')){
		onboard=1;
	}
	$.post($(ck).attr('data-url'),{"onboard":onboard}
	).done(function(data){
		var r=JSON.parse(data);
		var onboard={"boat_capacity":r.boat_capacity,"booked_seat_number":r.booked_seats_number,"onboard":r.onboard};
		var enviroment="production";
		if($(location).attr('hostname')=='localhost'){
			enviroment='development';
		}
		firebase.database().ref(enviroment+'/bd_ballestas_islands/businesses/'+r.business+'/departures/'+r.departure)
		.set(onboard).then(res=>{
			//hide_loadingOverlay(bt);
		});
	});
}

