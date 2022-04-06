$( document ).ready(function() {
	$('.onboard').each(function(index){
		var span=$(this);
		var enviroment="production";
		if($(location).attr('hostname')=='localhost'){
			enviroment='development';
		}
		firebase.database().ref(enviroment+'/bd_ballestas_islands/businesses/'+$(span).attr('data-business')+'/departures/'+$(span).attr('data-departure')).on('value', function(snapshot) {
		    var r=snapshot.val();
			$(span).html(r.onboard+'/'+r.booked_seat_number); 
		});
	});
	var enviroment="production";
	if($(location).attr('hostname')=='localhost'){
		enviroment='development';
	}
	firebase.database().ref(enviroment+'/bd_ballestas_islands/boarding_place/'+$('.table').attr('data-boardingplace')).on('value', function(snapshot) {
    	$.post($('table').attr('data-url')
		).done(function(data){
			var r=JSON.parse(data);
        	$('table').find('tbody').html(r.template);
		});
	});
});

