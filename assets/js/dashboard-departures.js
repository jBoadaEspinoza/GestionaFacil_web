var confirm_reservation;
var confirm_crew;
var confirm_searchresponsible;
var confirm_searchpassenger;
var confirm_travel;
var confirm_travel_info;
var confirm_edit_price;
var confirm_searchcrewmenber;
var confirm_edit_departure_date;
var confirm_edit_origin_in_departure;
var confirm_edit_boat_in_departure;
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
});
function onStateDeparture(bt,event){
	event.preventDefault();
	hide_loadingOverlay(bt);
	var url=$(bt).attr('href');
    $.confirm({
	    title: 'Ingrese un comentario!',
	    content: '' +
	    '<form action="'+url+'" class="formName">' +
	    '<div class="form-group">' +
	    '<textarea class="form-control" rows="5" class="comment"></textarea>' +
	    '</div>' +
	    '</form>',
	    buttons: {
	        formSubmit: {
	            text: 'Aceptar',
	            btnClass: 'btn-blue',
	            action: function () {
	            	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	                var comment = this.$content.find('textarea').val();
	                $.post(url,{"comment":comment}
	                	).done(function(data){
	                		var r=JSON.parse(data);
	                		if(r.success){
	                			if(r.data.condition>=1){
	                				var boarding_data={
		                				"departure":r.data.aboutboarding.departure,
		                				"boat":r.data.aboutboarding.boat,
		                				"business":r.data.aboutboarding.business,
		                				"boarding_time":r.data.aboutboarding.boarding_time,
		                				"sailing_time":r.data.aboutboarding.sailing_time,
		                				"arrival_time":r.data.aboutboarding.arrival_time
		                			}
									var enviroment="production";
									if($(location).attr('hostname')=='localhost'){
										enviroment='development';
									}
									firebase.database().ref(enviroment+'/bd_ballestas_islands/boarding_place/'+r.data.boarding_place_id)
									.set(boarding_data).then(res=>{
										hide_loadingOverlay(bt);
		                				location.reload();
									});
	                			}else{
	                				hide_loadingOverlay(bt);
		                			location.reload();
	                			}
	                		}
	                	});
	            }
	        },
	        cancel: function () {
	            //close
	        },
	    },
	    onContentReady: function () {
	    }
	});
}

function filter(bt,event){
	event.preventDefault();
	var form=$('#form_filter_departures');
	if($("#param_value").val()!=""){
		switch(parseInt($("#filter_by").val())){
			case 1:
				$('#form_filter_departures').append('<input type="hidden" name="departure_id" value="'+$("#param_value").val()+'" />');
				break;
			case 2:
				$('#form_filter_departures').append('<input type="hidden" name="boat_plate" value="'+$("#param_value").val()+'" />');
				break;
			case 3:
				$('#form_filter_departures').append('<input type="hidden" name="boat_name" value="'+$("#param_value").val()+'" />');
				break;
			case 4:
				$('#form_filter_departures').append('<input type="hidden" name="origin_denomination" value="'+$("#param_value").val()+'" />');
				break;

		}
	}
	var action=$(form).attr("action")+"?"+$(form).serialize();
	$(form).attr("action", action).submit();
}
function saveCrewsAccordingDeparture(bt,event){
	event.preventDefault();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	$.post($("#form_departure_crew").attr('action'),$("#form_departure_crew").serializeArray()
		).done(function(data){
			var r=JSON.parse(data);
			alert(r.message);
			hide_loadingOverlay(bt);
		});
}
function setFilterForCrewMenberByLicense(bt,event){
	event.preventDefault();
	$.post($("#form_search_crew_menber").attr('action'),{"license":$("#crew_license").val()}
	).done(function(data){
		var r=JSON.parse(data);
		var content=$("#form_search_crew_menber").parent();
		$(content).find('.card').html(r.template);
		hide_loadingOverlay(bt);
	});
}
function selectCrewMenber(bt,event){
	event.preventDefault();
	if(typeof confirm_crew!=='undefined'){
		confirm_crew.$content.find('#crew_menber_'+$(bt).attr('data-crew-type')+'_full_name').val($(bt).attr('data-document')+'|'+$(bt).attr('data-fullname'));
		confirm_crew.$content.find('input[name=crew'+$(bt).attr('data-crew-type')+']').val($(bt).attr('data-id'));
		confirm_searchcrewmenber.close();
	}
}
function loadTemplateForsearchingCrewMenber(bt,event){
	event.preventDefault();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	$.dialog({
		closeIcon: true,
		type: 'red',
    	typeAnimated: true,
    	boxWidth: '40%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm_searchcrewmenber=self;
			return $.post($(bt).attr('href'))
			.done(function(data){
				
				hide_loadingOverlay(bt);
				var r=JSON.parse(data);
				self.setTitle(r.title);
				self.setContentAppend(r.template);
		 		return;
			});
		},
		/*onClose:function(){
			var row=$(bt).parent().parent().parent().parent().parent().parent();
			show_loadingOverlay(row,[255,255,255,0,0.5]);
			location.reload();
		}*/
	});
}
function showTemplateForEditDepartureDate(bt,event){
	event.preventDefault();
	$.dialog({
		closeIcon: true,
		type: 'red',
    	typeAnimated: true,
    	boxWidth: '30%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm_edit_departure_date=self;
	
			return $.post($(bt).attr('href'))
			.done(function(data){
				hide_loadingOverlay(bt);
				var r=JSON.parse(data);
				self.setTitle(r.title);
			
				self.setContentAppend(r.template);
		 		return;
			});
		},
		onClose:function(){
			/*var row=$(bt).parent().parent().parent().parent().parent().parent();
			show_loadingOverlay(row,[255,255,255,0,0.5]);
			location.reload();*/
		}
	});
}
function update_date_in_departure(bt,event){
	event.preventDefault();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	var form=$("#form_update_date_in_departure");
	$.post($(form).attr('action'),getFormData($(form))
	).done(function(data){
		var r=JSON.parse(data);
		if(r.success){
			location.reload();
		}
		alert(r.msg);
		hide_loadingOverlay(bt);
	});
}
function showTemplateForEditOriginInDeparture(bt,event){
	event.preventDefault();
	$.dialog({
		closeIcon: true,
		type: 'red',
    	typeAnimated: true,
    	boxWidth: '30%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm_edit_origin_in_departure=self;
			return $.post($(bt).attr('href'))
			.done(function(data){
				hide_loadingOverlay(bt);
				var r=JSON.parse(data);
				self.setTitle(r.title);
			
				self.setContentAppend(r.template);
		 		return;
			});
		},
		onClose:function(){
			/*var row=$(bt).parent().parent().parent().parent().parent().parent();
			show_loadingOverlay(row,[255,255,255,0,0.5]);
			location.reload();*/
		}
	});
}
function update_origin_in_departure(bt,event){
	event.preventDefault();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	var form=$("#form_update_origin_in_departure");
	$.post($(form).attr('action'),getFormData($(form))
	).done(function(data){
		var r=JSON.parse(data);
		if(r.success){
			location.reload();
		}
		alert(r.msg);
		hide_loadingOverlay(bt);
	});
}
function showTemplateForEditBoatInDeparture(bt,event){
	event.preventDefault();
	$.dialog({
		closeIcon: true,
		type: 'red',
    	typeAnimated: true,
    	boxWidth: '30%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm_edit_boat_in_departure=self;
			return $.post($(bt).attr('href'))
			.done(function(data){
				hide_loadingOverlay(bt);
				var r=JSON.parse(data);
				self.setTitle(r.title);
				self.setContentAppend(r.template);
		 		return;
			});
		},
		onClose:function(){
			/*var row=$(bt).parent().parent().parent().parent().parent().parent();
			show_loadingOverlay(row,[255,255,255,0,0.5]);
			location.reload();*/
		}
	});
}
function update_boat_in_departure(bt,event){
	event.preventDefault();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	var form=$("#form_update_boat_in_departure");
	$.post($(form).attr('action'),getFormData($(form))
	).done(function(data){
		var r=JSON.parse(data);
		if(r.success){
			location.reload();
		}
		alert(r.msg);
		hide_loadingOverlay(bt);
	});
}
function getReservationFromDepartureList(bt,event){
	event.preventDefault();
	$.dialog({
		closeIcon: true,
		type: 'red',
    	typeAnimated: true,
    	boxWidth: '95%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm_reservation=self;
			return $.post($(bt).attr('href'))
			.done(function(data){
				hide_loadingOverlay(bt);
				var r=JSON.parse(data);
				self.setTitle(r.title);
				self.setContentAppend(r.template);
		 		return;
			});
		},
		onClose:function(){
			var row=$(bt).parent().parent().parent().parent().parent().parent();
			show_loadingOverlay(row,[255,255,255,0,0.5]);
			location.reload();
		}
	});
}
function getCrewFromDepartureList(bt,event){
	event.preventDefault();
	$.dialog({
		closeIcon: true,
		type: 'red',
    	typeAnimated: true,
    	boxWidth: '60%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm_crew=self;
			return $.post($(bt).attr('href'))
			.done(function(data){

				hide_loadingOverlay(bt);
				var r=JSON.parse(data);
				self.setTitle(r.title);
				self.setContentAppend(r.template);
		 		return;
			});
		},
		onClose:function(){
			var row=$(bt).parent().parent().parent().parent().parent().parent();
			show_loadingOverlay(row,[255,255,255,0,0.5]);
			location.reload();
		}
	});
}
function getTravelFromDepartureList(bt,event){
	event.preventDefault();
	$.dialog({
		closeIcon: true,
		type: 'red',
    	typeAnimated: true,
    	boxWidth: '95%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm_travel=self;
			return $.post($(bt).attr('href'))
			.done(function(data){
				var r=JSON.parse(data);
				self.setTitle(r.title);
				self.setContentAppend(r.template);
				hide_loadingOverlay(bt);
		 		return;
			});
		},
		onClose:function(){
			var row=$(bt).parent().parent().parent().parent().parent().parent();
			show_loadingOverlay(row,[255,255,255,0,0.5]);
			location.reload();
		}
	});
}
function selectSeat(bt,event){
	event.preventDefault();
	var num_seat=$(bt).html();
	if($(bt).hasClass('btn-success')){
		$(bt).removeClass('btn-success');
		$(bt).addClass('btn-warning');
		//$(bt).addClass('disabled');
		var num_seat_selected=parseInt($("#p_seats").find('h6').find('span').html());
		var total=parseFloat($("#p_seats").find('h3').find('span').html());
		num_seat_selected++;
		if(num_seat_selected>0){
			$("#continue").removeAttr('disabled');
		}else{
			$("#continue").attr('disabled','disabled');
		}
		var price=parseFloat($("#price_per_seat").val());
		total=total+price;
		$(bt).append('<div role="detail">'+
					 '	<input type="hidden" role="seat" value="'+num_seat+'" />'+
					 '	<input type="hidden" role="passenger" value="" />'+
					 '	<input type="hidden" role="price" value="'+price.toFixed(2)+'" />'+
					 '</div>');

		$("#p_seats").find('h6').find('span').html(num_seat_selected);
		$("#p_seats").find('h3').find('span').html(total.toFixed(2));
		return;
	}
	if($(bt).hasClass('btn-warning')){
		$(bt).removeClass('btn-warning');
		$(bt).addClass('btn-success');
		$(bt).find('div[role="detail"]').remove();
		var num_seat_selected=parseInt($("#p_seats").find('h6').find('span').html());
		var total=parseFloat($("#p_seats").find('h3').find('span').html());
		num_seat_selected--;
		if(num_seat_selected>0){
			$("#continue").removeAttr('disabled');
		}else{
			$("#continue").attr('disabled','disabled');
		}
		var price=parseFloat($("#price_per_seat").val());
		total=total-price;
		$("#p_seats").find('h6').find('span').html(num_seat_selected);
		$("#p_seats").find('h3').find('span').html(total.toFixed(2));
		//$(bt).removeClass('disabled');
		return;
	}
}
function addReservationDetail(bt,event){
	event.preventDefault();
	var array=[];
	confirm_reservation.$content.find("#p_seats").find('div[role=detail]').each(function(index){
		if($(this)!=null){
			$(this).find('input[type=hidden]').each(function(){
				var json={
					'name':"details["+index+"]["+$(this).attr('role')+"]",
					'value':$(this).val()
				};
				array.push(json);
			});
		}
	});
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	$.post(confirm_reservation.$content.find("#p_seats").attr('action'),array
	).done(function(data){
		var r=JSON.parse(data);
		confirm_reservation.$content.html(r.template);
		confirm_reservation.$content.find('#sub_title').html('Paso 2 - Indique el responsable de la reservación');
		hide_loadingOverlay(bt);
	});

}

function searchResponsible(bt,event){
	event.preventDefault();
	//show_loadingOverlay(bt,[255,255,255,0,0.5]);
	confirm_searchresponsible=null;
	$.dialog({
		closeIcon: true,
		type: 'red',
    	typeAnimated: true,
    	boxWidth: '50%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm_searchresponsible=self;
			return $.post(BASE_URL+"dashboard/templates/search-responsibles/es")
			.done(function(data){
				var r=JSON.parse(data);
				self.setTitle(r.title);
				self.setContentAppend(r.template);
			});
		}
	});
}
function addPassenger(bt,event){
	event.preventDefault();
	confirm_searchpassenger=null;
	var seat=$(bt).attr('data-seat');
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	$.dialog({
		closeIcon: true,
		type: 'red',
    	typeAnimated: true,
    	boxWidth: '50%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm_searchpassenger=self;
			return $.post(BASE_URL+"dashboard/templates/search-passengers/es",{"seat":seat})
			.done(function(data){
				var r=JSON.parse(data);
				self.setTitle(r.title);
				self.setContentAppend(r.template);
				hide_loadingOverlay(bt);
			});
		}
	});
}
function editPriceOfBooking(bt,event){
	event.preventDefault();
	confirm_edit_price=null;
	var seat=$(bt).attr('data-seat');
	var price=$(bt).attr('data-price');
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	$.dialog({
		closeIcon: true,
		type: 'red',
    	typeAnimated: true,
    	boxWidth: '30%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm_edit_price=self;
			return $.post($(bt).attr('href'),{"seat":seat,"price":price})
			.done(function(data){
				var r=JSON.parse(data);
				self.setTitle(r.title);
				self.setContentAppend(r.template);
				hide_loadingOverlay(bt);
			});
		}
	});
}
function addPassengerInSummary(bt,event){
	event.preventDefault();
	confirm_searchpassenger=null;
	var seat=$(bt).attr('data-seat');
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	$.dialog({
		closeIcon: true,
		type: 'red',
    	typeAnimated: true,
    	boxWidth: '50%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm_searchpassenger=self;
			return $.post(BASE_URL+"dashboard/templates/search-passengers/es",{"seat":seat})
			.done(function(data){
				var r=JSON.parse(data);
				self.setTitle(r.title);
				self.setContentAppend(r.template);
				hide_loadingOverlay(bt);
			});
		}
	});
}
function addNewPriceToBooking(bt,event){
	var seat=$(bt).attr('data-seat');
	var price=confirm_edit_price.$content.find('#price').val();
	var item=0;
	if(typeof confirm_edit_price!=='undefined'){
		confirm_travel_info.$content.find('#seats').find('tbody').find('tr').each(function(){
			if(parseInt($(this).find('td').first().html())==parseInt(seat)){
				$(this).find('td').eq(2).html(price);
				confirm_travel_info.$content.find('#seats').find('form').find('input[name="details['+item+'][price]"]').val(price);
				confirm_edit_price.close();
			}
			item++;
		});
		hide_loadingOverlay(bt);
	}
}
function addNewPersonAsResponsible(bt,event){
	event.preventDefault();
	var document_type=$("#document_type").val();
	var document_number=$("#document_number").val();
	var firstname=$("#firstname").val();
	var lastname=$("#lastname").val();
	var birthdate=$("#birth_year").val()+'-'+$("#birth_month").val()+'-'+$("#birth_day").val();
	var birth_country_code=$("#birth_country_code").val();
	var mobile=$("#mobile").val();
	var email=$("#email").val();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	$.post(BASE_URL+"dashboard/templates/add-new-person/es",{"document_type":document_type,"document_number":document_number,"firstname":firstname,"lastname":lastname,"birthdate":birthdate,"birth_country_code":birth_country_code,"mobile":mobile,"email":email})
	.done(function(data){
		var r=JSON.parse(data);
		if(r.success){
			alert(r.msg);
			var responsible_id=r.person.id;
			var responsible_fullname=r.person.document+'|'+r.person.fullname;
			confirm_reservation.$content.find('#responsible').val(responsible_fullname);
			confirm_reservation.$content.find('#btnContinue').removeAttr('disabled');
			confirm_reservation.$content.find('input[name=responsible_id]').val(responsible_id);
			confirm_searchresponsible.close();
			hide_loadingOverlay(bt);
		}
		
	});
}
function addNewPersonAsPassenger(bt,event){
	event.preventDefault();
	var document_type=$("#document_type").val();
	var document_number=$("#document_number").val();
	var firstname=$("#firstname").val();
	var lastname=$("#lastname").val();
	var birthdate=$("#birth_year").val()+'-'+$("#birth_month").val()+'-'+$("#birth_day").val();
	var birth_country_code=$("#birth_country_code").val();
	var mobile=$("#mobile").val();
	var email=$("#email").val();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	$.post(BASE_URL+"dashboard/templates/add-new-person/es",{"document_type":document_type,"document_number":document_number,"firstname":firstname,"lastname":lastname,"birthdate":birthdate,"birth_country_code":birth_country_code,"mobile":mobile,"email":email})
	.done(function(data){
		var r=JSON.parse(data);
		if(r.success){
			alert(r.msg);
			var seat=$(bt).attr('data-seat');
			var passenger_id=r.person.id;
			var passenger_fullname=r.person.document+'|'+r.person.fullname;
			var item=0;
			if(typeof confirm_reservation!=='undefined'){
				confirm_reservation.$content.find("#details_reservation").find('tbody').find('tr').each(function(){
					if(parseInt($(this).find('td').first().html())==parseInt(seat)){
						$(this).find('td').eq(1).html(passenger_fullname);
						confirm_reservation.$content.find('input[name="details['+item+'][passenger]"]').val(passenger_id+"|"+passenger_fullname);
						confirm_searchpassenger.close();
					}
					item++;
				});
				hide_loadingOverlay(bt);
			}
			if(typeof confirm_travel_info!=='undefined'){
				confirm_travel_info.$content.find('#seats').find('tbody').find('tr').each(function(){
					if(parseInt($(this).find('td').first().html())==parseInt(seat)){
						$(this).find('td').eq(1).html(passenger_fullname);
						confirm_travel_info.$content.find('#seats').find('form').find('input[name="details['+item+'][passenger]"]').val(passenger_id);
						confirm_searchpassenger.close();
					}
					item++;
				});
				hide_loadingOverlay(bt);
			}
		}
		
	});
}
function updateAll(bt,event){
	event.preventDefault();
	var content=$(bt).parent().parent().parent();
	$(content).find('.nav-link').each(function(){
		if($(this).hasClass('active')){
			switch($(this).prop("id")){
				case "seats-tab":
					//Procedemos a actualizar los asientos
					show_loadingOverlay(bt,[255,255,255,0,0.5]);
					var array_title=confirm_travel_info.$title.html().split(':');
					var r_id=array_title[1];
					var form=$("#"+$(this).attr('aria-controls')).find('form');
					//llamamos al ajax post
					$.post(BASE_URL+'dashboard/save/all/into/travel-seats/where/reservation/'+r_id+'/es',$(form).serializeArray()
						).done(function(data){
							var r=JSON.parse(data);
							if(r.success){
								alert(r.msg.es);
								hide_loadingOverlay(bt);
							}
						});
				break;
				case "pays-tab":
					//Procedemos a actualizar los asientos
					show_loadingOverlay(bt,[255,255,255,0,0.5]);
					var array_title=confirm_travel_info.$title.html().split(':');
					var r_id=array_title[1];
					var form=$("#"+$(this).attr('aria-controls')).find('form');
					$.post(BASE_URL+'dashboard/save/all/into/travel-pays/where/reservation/'+r_id+'/es',$(form).serializeArray()
						).done(function(data){
							var r=JSON.parse(data);
							if(r.success){
								alert(r.msg.es);
								confirm_travel.$content.html(r.template);
								confirm_travel_info.close();
								hide_loadingOverlay(bt);
								return;
							}
							alert(r.msg.es);
							hide_loadingOverlay(bt);
							return;
						});
				break;
			}
		}
	});
}
function setFilterForResonsibleByDocumentFromForm(form,event){
	
	if(event.key=='Enter'){
		if($('#document_number').val()!=""){
			var bt=confirm_searchresponsible.$content.find(form).find('button');
			event.preventDefault();
			if($('#document_number').val()==""){
				alert("ingrese numero de documento valido");
			}else{
				var seat=$(bt).attr('data-seat');
				var form=$(bt).parent().parent();
				show_loadingOverlay(bt,[255,255,255,0,0.5]);
				$.post($(form).attr("action"),$(form).serializeArray()
				).done(function(data){
					var r=JSON.parse(data);
					var content=confirm_searchresponsible.$content.find(form).parent().parent().parent();
					$(content).find('.card').html(r.template);
					hide_loadingOverlay(bt);
				});
			} 
		}
	}
	if(event.key=='Escape'){
		event.preventDefault();
		confirm_searchresponsible.close();
	}
}	
function setFilterForResonsibleByDocument(bt,event){
	event.preventDefault();
	if($('#document_number').val()==""){
		alert("ingrese numero de documento valido");
	}else{
		var seat=$(bt).attr('data-seat');
		var form=$(bt).parent().parent();
		show_loadingOverlay(bt,[255,255,255,0,0.5]);
		$.post($(form).attr("action"),$(form).serializeArray()
		).done(function(data){
			var r=JSON.parse(data);
			var content=confirm_searchresponsible.$content.find(form).parent().parent().parent();
			$(content).find('.card').html(r.template);
			hide_loadingOverlay(bt);
		});
	}
}
function setFilterForPassengerByDocumentFromForm(form,event){
	if(event.key=='Enter'){
		if($('#document_number').val()!=""){
			var bt=confirm_searchpassenger.$content.find(form).find('button');
			event.preventDefault();
			if($('#document_number').val()==""){
				alert("ingrese numero de documento valido");
			}else{
				var seat=confirm_searchpassenger.$content.find(bt).attr('data-seat');
				var form=confirm_searchpassenger.$content.find(bt).parent().parent();
				show_loadingOverlay(bt,[255,255,255,0,0.5]);
				$.post($(form).attr("action"),$(form).serializeArray()
				).done(function(data){
					var r=JSON.parse(data);
					var content=confirm_searchpassenger.$content.find(form).parent().parent().parent();
					$(content).find('.card').html(r.template);
					hide_loadingOverlay(bt);
				});
			} 
		}
	}
	if(event.key=='Escape'){
		event.preventDefault();
		confirm_searchpassenger.close();
		
	}
}
function setFilterForPassengerByDocument(bt,event){
	event.preventDefault();
	if($('#document_number').val()==""){
		alert("ingrese numero de documento valido");
	}else{
		var seat=confirm_searchpassenger.$content.find(bt).attr('data-seat');
		var form=confirm_searchpassenger.$content.find(bt).parent().parent();
		show_loadingOverlay(bt,[255,255,255,0,0.5]);
		$.post($(form).attr("action"),$(form).serializeArray()
		).done(function(data){
			var r=JSON.parse(data);
			var content=confirm_searchpassenger.$content.find(form).parent().parent().parent();
			$(content).find('.card').html(r.template);
			hide_loadingOverlay(bt);
		});
	}
}
function selectResponsible(bt,event){
	event.preventDefault();
	if(typeof confirm_reservation!=='undefined'){
		confirm_reservation.$content.find('#responsible').val($(bt).attr('data-document')+'|'+$(bt).attr('data-fullname'));
		confirm_reservation.$content.find('#btnContinue').removeAttr('disabled');
		confirm_reservation.$content.find('input[name=responsible_id]').val($(bt).attr('data-id'));
		confirm_searchresponsible.close();
	}
	if(typeof confirm_travel_info!=='undefined'){
		confirm_travel_info.$content.find('#pays').find('span[role=responsible]').html($(bt).attr('data-document')+'|'+$(bt).attr('data-fullname'));
		confirm_travel_info.$content.find('#pays').find('input[name=responsible_id]').val($(bt).attr('data-id'));
		confirm_searchresponsible.close();
	}
}
function selectPassenger(bt,event){
	event.preventDefault();
	var seat=$(bt).attr('data-seat');
	var passenger_id=$(bt).attr('data-passenger-id');
	var passenger=$(bt).attr('data-document')+'|'+$(bt).attr('data-fullname');
	var item=0;
	
	if(typeof confirm_reservation !== "undefined"){
		confirm_reservation.$content.find("#details_reservation").find('tbody').find('tr').each(function(){
			if(parseInt($(this).find('td').first().html())==parseInt(seat)){
				$(this).find('td').eq(1).html(passenger);
				confirm_reservation.$content.find('input[name="details['+item+'][passenger]"]').val(passenger_id+"|"+passenger);
				confirm_searchpassenger.close();
			}
			item++;
		});
	}
	if(typeof confirm_travel_info !=="undefined"){
		confirm_travel_info.$content.find('tbody').find('tr').each(function(){
			if(parseInt($(this).find('td').first().html())==parseInt(seat)){
				$(this).find('td').eq(1).html(passenger);
				confirm_travel_info.$content.find('#seats').find('form').find('input[name="details['+item+'][passenger]"]').val(passenger_id);
				var array_splited=confirm_travel_info.$title.html().split(':');
				confirm_searchpassenger.close();
			}
			item++;
		});
	}
	return;

}
function removePassengerInSummary(bt,event){
	event.preventDefault();
	//confirm=null;
	var seat=$(bt).attr('data-seat');
	var index=0;
	confirm_reservation.$content.find("#details_reservation").find('tbody').find('tr').each(function(){
		if(parseInt($(this).find('td').first().html())==parseInt(seat)){
			var itemDetail=$(this);
			var itemRemoved=index;
			$.confirm({
				title:'Confirme lo siguiente',
				content:'¿Realmente desea liberar el asiento numero: '+seat+'?',
				type: 'dark',
    			typeAnimated: true,
    			buttons:{
    				ok:function(){
    					$("#details_reservation").find('div').eq(itemRemoved).remove();
    					//creamos array
    					var divs=[];
    					confirm_reservation.$content.find('#details_reservation').find('div').each(function(){
    						var inputs=[];
    						inputs.push($(this).find('input[type=hidden]').eq(0).val());
    						inputs.push($(this).find('input[type=hidden]').eq(1).val());
    						inputs.push($(this).find('input[type=hidden]').eq(2).val());
    						divs.push(inputs);
    					});
    					//eliminamos todos los inputs ocultos
    					confirm_reservation.$content.find('#details_reservation').find('div').each(function(){
    						$(this).remove();
    					});
    					//eliminamos la tabla seleccionada de la tabla
    					$(itemDetail).remove();
    					//agregamos los nuevos inputs ocultos
    					for(var i=0;i<divs.length;i++){
    						var html='<div>';
    						html+='<input type="hidden" name="details['+i+'][seat]" value="'+divs[i][0]+'" />';
    						html+='<input type="hidden" name="details['+i+'][passenger]" value="'+divs[i][1]+'" />';
    						html+='<input type="hidden" name="details['+i+'][price]" value="'+divs[i][2]+'" />';
    						html+='</div>';
    						confirm_reservation.$content.find('#details_reservation').append(html);
    					}
    				},
    				close:function(){}
    			}
			});
		}
		index++;
	});
	
}
function showReservationSummary(bt,event){
	event.preventDefault();
	var form=confirm_reservation.$content.find("#addResponsible");
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	$.post($(form).attr("action"),$(form).serializeArray()
	).done(function(data){
		var r=JSON.parse(data);
		confirm_reservation.$content.html(r.template);
		hide_loadingOverlay(bt);
	});
}
function RegisterReservation(bt,event){
	event.preventDefault();
	var form=confirm_reservation.$content.find("#summaryReservation");
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	$.post($(form).attr("action"),$(form).serializeArray()
	).done(function(data){
		var r=JSON.parse(data);
		var onboard={"boat_capacity":r.data.boat_capacity,"booked_seat_number":r.data.booked_seats_number,"onboard":r.data.onboard};
		var enviroment="production";
		if($(location).attr('hostname')=='localhost'){
			enviroment='development';
		}
		firebase.database().ref(enviroment+'/bd_ballestas_islands/businesses/'+r.data.business+'/departures/'+r.data.departure)
		.set(onboard).then(res=>{
			confirm_reservation.$content.html(r.template);
			hide_loadingOverlay(bt);
		});
		
	});
}

function showInfoAboutReservationIntoTravel(bt,event){
	event.preventDefault();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	var url_by_post=$(bt).attr('href');
	$.dialog({
		type: 'red',
    	typeAnimated: true,
    	columnClass: 'col-12',
	    content: function () {
	        var self = this;
	        confirm_travel_info=self;
	        return $.post(url_by_post)
	        	.done(function (response) {
	        		var r=JSON.parse(response);
	            	self.setContent(r.template.content);
	            	self.setTitle(r.template.title);
	            	hide_loadingOverlay(bt);
	        });
	    }
	});	
}

function changeTypeOfPay(ddlb,event){
	event.preventDefault();
	var amount_to_pay=$(ddlb).attr('data-amount');
	var type_of_pay=$(ddlb).val();
	var content=$("#type_of_pay");
	show_loadingOverlay(content,[255,255,255,0,0.5]);
	$.post(BASE_URL+'dashboard/templates/type-of-pay/es',{"type_of_pay":type_of_pay,"amount_to_pay":amount_to_pay}
	).done(function(data){
		var r=JSON.parse(data);
		$("#type_of_pay").html(r.template);
		hide_loadingOverlay(content);
	});
}	

function getFormData($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}