var confirm;
var confirm_parent;
var confirm_parent_2;
var confirm_print_zarpe;
var confirm_edit_price;
function searchResponsible(bt,event){
	event.preventDefault();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	event.preventDefault();
	confirm_parent_2=null;
	$.dialog({
		closeIcon: true,
		type: 'red',
    	typeAnimated: true,
    	boxWidth: '50%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm_parent_2=self;
			return $.post($(bt).attr('href'))
			.done(function(data){
				var r=JSON.parse(data);
				self.setTitle(r.title);
				self.setContentAppend(r.template);
				hide_loadingOverlay(bt);
		 		return;
			});
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
			confirm_parent.$content.find('#pays').find('span[role=responsible]').html(responsible_fullname);
			confirm_parent.$content.find('#pays').find('input[name=responsible_id]').val(responsible_id);
			confirm_parent_2.close();
			hide_loadingOverlay(bt);
		}
		
	});
}
function selectResponsible(bt,event){
	event.preventDefault();
	confirm_parent.$content.find('#pays').find('span[role=responsible]').html($(bt).attr('data-document')+'|'+$(bt).attr('data-fullname'));
	confirm_parent.$content.find('#pays').find('input[name=responsible_id]').val($(bt).attr('data-id'));
	confirm_parent_2.close();
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
function searchTravels(bt,event){
	event.preventDefault();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	event.preventDefault();
	var url_by_post=$(bt).attr('href');
	var d_id=$(bt).parent().parent().find('input').val();
	$.post(url_by_post,{"d_id":d_id}
	).done(function(data){
		var r=JSON.parse(data);
		if(!r.success){
 			var input=$(bt).parent().parent().find('input');
 			$(input).focus();
 			$("#departures_details").html(r.msg.description.es);
 			hide_loadingOverlay(bt);
		 	return;
		}
		hide_loadingOverlay(bt);
		$("#departures_details").html(r.template);
		return;
	});
}

function showInfoAboutReservationIntoTravel(bt,event){
	event.preventDefault();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	event.preventDefault();
	var url_by_post=$(bt).attr('href');
	$.dialog({
		type: 'red',
    	typeAnimated: true,
    	columnClass: 'col-12',
	    content: function () {
	        var self = this;
	        confirm_parent=self;
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
	if(typeof confirm_parent!=='undefined'){
		confirm_parent.$content.find('#seats').find('tbody').find('tr').each(function(){
			if(parseInt($(this).find('td').first().html())==parseInt(seat)){
				$(this).find('td').eq(2).html(price);
				confirm_parent.$content.find('#seats').find('form').find('input[name="details['+item+'][price]"]').val(price);
				confirm_edit_price.close();
			}
			item++;
		});
		hide_loadingOverlay(bt);
	}
}
function addPassenger(bt,event){
	event.preventDefault();
	confirm=null;
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	event.preventDefault();
	var seat=$(bt).attr('data-seat');
	$.dialog({
		closeIcon: true,
		type: 'red',
    	typeAnimated: true,
    	boxWidth: '50%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm=self;
			return $.post($(bt).attr("href"),{"seat":seat})
			.done(function(data){
				var r=JSON.parse(data);
				self.setTitle(r.title);
				self.setContentAppend(r.template);
				hide_loadingOverlay(bt);
			});
		}
	});
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

function selectPassenger(bt,event){
	event.preventDefault();
	var seat=$(bt).attr('data-seat');
	var passenger_id=$(bt).attr('data-passenger-id');
	var passenger=$(bt).attr('data-document')+'|'+$(bt).attr('data-fullname');
	var item=0;
	confirm_parent.$content.find('tbody').find('tr').each(function(){
		if(parseInt($(this).find('td').first().html())==parseInt(seat)){
			$(this).find('td').eq(1).html(passenger);
			confirm_parent.$content.find('#seats').find('form').find('input[name="details['+item+'][passenger]"]').val(passenger_id);
			var array_splited=confirm_parent.$title.html().split(':');
			confirm.close();
		}
		item++;
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
			confirm_parent.$content.find('#seats').find('tbody').find('tr').each(function(){
				if(parseInt($(this).find('td').first().html())==parseInt(seat)){
					$(this).find('td').eq(1).html(passenger_fullname);
					confirm_parent.$content.find('#seats').find('form').find('input[name="details['+item+'][passenger]"]').val(passenger_id);
					confirm.close();
				}
				item++;
			});
			hide_loadingOverlay(bt);
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
					var array_title=confirm_parent.$title.html().split(':');
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
					var array_title=confirm_parent.$title.html().split(':');
					var r_id=array_title[1];
					var form=$("#"+$(this).attr('aria-controls')).find('form');
					$.post(BASE_URL+'dashboard/save/all/into/travel-pays/where/reservation/'+r_id+'/es',$(form).serializeArray()
						).done(function(data){
							var r=JSON.parse(data);
							if(r.success){
								alert(r.msg.es);
								confirm_parent.close();
								$("#departures_details").html(r.template);
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