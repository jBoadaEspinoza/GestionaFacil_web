var confirm;
$( document ).ready(function() {
	var boats=[];
	var hours=[];
	$('.horas-acomuladas').find('tbody').find('tr').each(function(){
		var boat_name=$(this).find('td').eq(1).html();
		boats.push(boat_name);
		var boat_hour=parseInt($(this).find('td').eq(2).html());
		hours.push(boat_hour);
	});
	var ctx = document.getElementById('horas_acomuladas').getContext('2d');
	var chart = new Chart(ctx, {
	    type: 'bar',
	    data: {
	        labels: boats,
	        datasets: [{
	            label: 'Horas acomuladas',
	            backgroundColor: 'rgb(40, 55, 71)',
            	borderColor: 'rgb(40, 55, 71)',
	            data: hours
	        }]
	    },
	    options: {}
	});
});
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
			$("#details_reservation").find('tbody').find('tr').each(function(){
				if(parseInt($(this).find('td').first().html())==parseInt(seat)){
					$(this).find('td').eq(1).html(passenger_fullname);
					$('input[name="details['+item+'][passenger]"]').val(passenger_id+"|"+passenger_fullname);
					confirm.close();
				}
				item++;
			});
			hide_loadingOverlay(bt);
		}
		
	});
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
			$('#responsible').val(responsible_fullname);
			$('#btnContinue').removeAttr('disabled');
			$('input[name=responsible_id]').val(responsible_id);
			confirm.close();
			hide_loadingOverlay(bt);
		}
		
	});
}
function addPassengerInSummary(bt,event){
	event.preventDefault();
	confirm=null;
	var seat=$(bt).attr('data-seat');
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	$.dialog({
		closeIcon: true,
		type: 'dark',
    	typeAnimated: true,
    	boxWidth: '50%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm=self;
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
function removePassengerInSummary(bt,event){
	event.preventDefault();
	//confirm=null;
	var seat=$(bt).attr('data-seat');
	var index=0;
	$("#details_reservation").find('tbody').find('tr').each(function(){
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
    					$('#details_reservation').find('div').each(function(){
    						var inputs=[];
    						inputs.push($(this).find('input[type=hidden]').eq(0).val());
    						inputs.push($(this).find('input[type=hidden]').eq(1).val());
    						inputs.push($(this).find('input[type=hidden]').eq(2).val());
    						divs.push(inputs);
    					});
    					//eliminamos todos los inputs ocultos
    					$('#details_reservation').find('div').each(function(){
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
    						$('#details_reservation').append(html);
    					}
    				},
    				close:function(){}
    			}
			});
		}
		index++;
	});
}
function RegisterReservation(bt,event){
	event.preventDefault();
	var form=("#summaryReservation");
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	$.post($(form).attr("action"),$(form).serializeArray()
	).done(function(data){
		var r=JSON.parse(data);
		$("#content2").html(r.template);
		hide_loadingOverlay(bt);
	});
}
function showReservationSummary(bt,event){
	event.preventDefault();
	var form=$("#addResponsible");
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	$.post($(form).attr("action"),$(form).serializeArray()
	).done(function(data){
		var r=JSON.parse(data);
		$("#content2").html(r.template);
		hide_loadingOverlay(bt);
	});
}
function selectResponsible(bt,event){
	event.preventDefault();
	$('#responsible').val($(bt).attr('data-document')+'|'+$(bt).attr('data-fullname'));
	$('#btnContinue').removeAttr('disabled');
	$('input[name=responsible_id]').val($(bt).attr('data-id'));
	confirm.close();
}
function selectPassenger(bt,event){
	event.preventDefault();
	var seat=$(bt).attr('data-seat');
	var passenger_id=$(bt).attr('data-passenger-id');
	var passenger=$(bt).attr('data-document')+'|'+$(bt).attr('data-fullname');
	var item=0;
	$("#details_reservation").find('tbody').find('tr').each(function(){
		if(parseInt($(this).find('td').first().html())==parseInt(seat)){
			$(this).find('td').eq(1).html(passenger);
			$('input[name="details['+item+'][passenger]"]').val(passenger_id+"|"+passenger);
			confirm.close();
		}
		item++;
	});
}
function setFilterForResonsibleByDocumentFromForm(form,event){
	if(event.key=='Enter'){
		if($('#document_number').val()!=""){
			var bt=$(form).find('button');
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
					var content=$(form).parent().parent().parent();
					$(content).find('.card').html(r.template);
					hide_loadingOverlay(bt);
				});
			}
		}
		
	}
	if(event.key=='Escape'){
		confirm.close();
		event.preventDefault();
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
			var content=$(form).parent().parent().parent();
			$(content).find('.card').html(r.template);
			hide_loadingOverlay(bt);
		});
	}
}
function setFilterForPassengerByDocumentFromForm(form,event){
	if(event.key=='Enter'){
		if($('#document_number').val()!=""){
			var bt=$(form).find('button');
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
					var content=$(form).parent().parent().parent();
					$(content).find('.card').html(r.template);
					hide_loadingOverlay(bt);
				});
			} 
		}
	}
	if(event.key=='Escape'){
		confirm.close();
		event.preventDefault();
	}
}
function setFilterForPassengerByDocument(bt,event){
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
			var content=$(form).parent().parent().parent();
			$(content).find('.card').html(r.template);
			hide_loadingOverlay(bt);
		});
	}
}
function searchResponsible(bt,event){
	event.preventDefault();
	confirm=null;
	$.dialog({
		closeIcon: true,
		type: 'dark',
    	typeAnimated: true,
    	boxWidth: '50%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm=self;
			return $.post(BASE_URL+"dashboard/templates/search-responsibles/es")
			.done(function(data){
				var r=JSON.parse(data);
				self.setTitle(r.title);
				self.setContentAppend(r.template);
			});
		}
	});
}
function searchDepartureAvailables(bt,event){
	event.preventDefault();
	var form=$("#form_search_departure_availables");
	$.post($(form).attr("action"),$(form).serializeArray()
	).done(function(data){
		var r=JSON.parse(data);
		$("#content2").html(r.template);
		hide_loadingOverlay(bt);
	});
}
function departureSelected(bt,event){
	event.preventDefault();
	var url=$(bt).attr('data-href');
	var d_id=$(bt).attr('data-departure');
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	$.post(url,{"d_id":d_id}
		).done(function(data){
			var r=JSON.parse(data);
			$("#content2").html(r.template);
			hide_loadingOverlay(bt);
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
function chooseTypeSelection(cbb,event){
	event.preventDefault();
	var price=parseFloat($("#price_per_seat").val());
	if($(cbb).val()==1){
		$("#asientos").find('button').each(function(){
			if($(this).hasClass('seat')){
				if($(this).hasClass('btn-warning')){
					$(this).removeClass('btn-warning');
					$(this).addClass('btn-success');
					$(this).removeClass('disabled');
					var num_seat_selected=parseInt($("#p_seats").find('h6').find('span').html());
					num_seat_selected--;
					var total=parseFloat($("#p_seats").find('h3').find('span').html());
					if(num_seat_selected>0){
						$("#continue").removeAttr('disabled');
					}else{
						$("#continue").attr('disabled','disabled');
					}
					$("#p_seats").find('h6').find('span').html(num_seat_selected);
					total=total-parseFloat($("#price_per_seat").val());
					$("#p_seats").find('h3').find('span').html(total.toFixed(2));
				}
			}
		});
	}else{
		$("#asientos").find('button').each(function(){
			if($(this).hasClass('seat')){
				if($(this).hasClass('btn-success')){
					$(this).removeClass('btn-success');
					$(this).addClass('btn-warning');
					$(this).addClass('disabled');
					var num_seat_selected=parseInt($("#p_seats").find('h6').find('span').html());
					num_seat_selected++;
					var total=parseFloat($("#p_seats").find('h3').find('span').html());
					if(num_seat_selected>0){
						$("#continue").removeAttr('disabled');
					}else{
						$("#continue").attr('disabled','disabled');
					}
					$("#p_seats").find('h6').find('span').html(num_seat_selected);
					$(this).append('<div role="detail">'+
					 '	<input type="hidden" role="seat" value="'+num_seat_selected+'" />'+
					 '	<input type="hidden" role="passenger" value="" />'+
					 '	<input type="hidden" role="price" value="'+price.toFixed(2)+'" />'+
					 '</div>');
					total=total+price;
					$("#p_seats").find('h3').find('span').html(total.toFixed(2));
				}
			}
		});
	}
}
function addReservationDetail(bt,event){
	event.preventDefault();
	var array=[];
	$("#p_seats").find('div[role=detail]').each(function(index){
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
	$.post($("#p_seats").attr('action'),array
	).done(function(data){
		var r=JSON.parse(data);
		$("#content2").html(r.template);
		hide_loadingOverlay(bt);
	});
}