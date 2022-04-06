
var confirm_travel_info;
var confirm_searchresponsible;
var confirm_searchpassenger;
var confirm_edit_price;
function filterDepartures(bt,event){
	event.preventDefault();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	var date_from=$("#panel_filter").find('input[name=date_from]').val();
	var date_to=$("#panel_filter").find('input[name=date_to]').val();
	var offset=$('#panel_filter').find('select[name=rows]').val();
	var pag=1;
	$('#pagination').find('li').each(function(){
		if($(this).hasClass('active')){
			pag=$(this).find('a').html();
		}
	});
	var param_option=$('#panel_filter').find('select[name=param_option]').val();
	var param_value=$('#panel_filter').find('input[name=param_value]').val();
	if(param_value==""){
		var href=BASE_URL+'extranet/administracion/dashboard_reservaciones_disponibles/entre-las-fechas/'+date_from+'/'+date_to;
		var form=$(bt).parent();
		$(form).append('<input type="hidden" name="pag" value="'+pag+'" />');
		$(form).append('<input type="hidden" name="offset" value="'+offset+'" />');
		$(form).attr('method','GET');
		$(form).attr('action',href);
		$(form).submit();
		hide_loadingOverlay(bt);
		return;	
	}
	switch(parseInt(param_option)){
    	case 1:
    		if(param_value.length>=3 && param_value.substring(0,3)=='RES'){
    			param_value=param_value.substring(3,param_value.length);
    		}
    		param_value=parseInt(param_value);
    		if(isNaN(param_value)){
    			hide_loadingOverlay(bt);
    			$(param_value).focus();
    			alert("El ID-reserva ingresado es incorrecto");
    			return;
    		}
    		var href=BASE_URL+'extranet/administracion/dashboard_reservaciones_disponibles/segun/ID-reserva/'+param_value+'/entre-las-fechas/'+date_from+'/'+date_to;
			var form=$(bt).parent();
			$(form).attr('action',href);
    		$(form).attr('method','POST');
    		$(form).submit();
			hide_loadingOverlay(bt);
			break;
		case 2:
			var href=BASE_URL+'extranet/administracion/dashboard_reservaciones_disponibles/segun/apellidos-y-nombres-del-responsable/'+param_value+'/entre-las-fechas/'+date_from+'/'+date_to;
			var form=$(bt).parent();
			$(form).append('<input type="hidden" name="pag" value="'+pag+'" />');
			$(form).append('<input type="hidden" name="offset" value="'+offset+'" />');
			$(form).attr('method','GET');
			$(form).attr('action',href);
			$(form).submit();
			hide_loadingOverlay(bt);
    		break;
    }
}
function selectPage(bt,event){
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	event.preventDefault();
    var offset=$('#panel_filter').find('select[name=rows]').val();
	var pag=$(bt).html();
	var href=$(bt).attr('href');
	var form=$(bt).parent().parent().parent();
	$(form).append('<input type="hidden" name="pag" value="'+pag+'" />');
	$(form).append('<input type="hidden" name="offset" value="'+offset+'" />');
	$(form).attr('method','GET');
	$(form).attr('action',href);
	$(form).submit();
	///hide_loadingOverlay(bt);
	return;	
}
function changeNumberOfPages(cbb,event){
	event.preventDefault();
	var offset=$(cbb).val();
	var pag=1;
	var form=$('input[name=param_value]').parent().find('form');
	$(form).append('<input type="hidden" name="pag" value="'+pag+'" />');
	$(form).append('<input type="hidden" name="offset" value="'+offset+'" />');
	$(form).attr('method','GET');
	$(form).submit();
	///hide_loadingOverlay(bt);
	return;	
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
function searchResponsible(bt,event){
	//show_loadingOverlay(bt,[255,255,255,0,0.5]);
	event.preventDefault();
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
			confirm_travel_info.$content.find('#pays').find('span[role=responsible]').html(responsible_fullname);
			confirm_travel_info.$content.find('#pays').find('input[name=responsible_id]').val(responsible_id);
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
		confirm_searchresponsible.close();
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
		confirm_searchpassenger.close();
		event.preventDefault();
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
								confirm_travel_info.close();
								hide_loadingOverlay(bt);
								return;
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
								confirm_travel_info.close();
	                			location.reload();
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