var activity_boarding_places;
var activity_date;
var activity_pax;
var activity_login;
var activity_menu_lateral;
var activity_signup;
function showLoginForm(bt,event){
	event.preventDefault();
	var url=$(bt).attr('href');
	//alert(url);
	$.activity({
		title:'',
		logo:'',
		content:function(){
			var self=this;
			activity_login=self;
			return $.post(url)
			.done(function(data){
				var r=JSON.parse(data);
				//self.$closeIcon.remove();
				self.setClassToolBar('text-white color-bg-header');
				self.setBackgroundActivityPane('white');
				self.setTitle(r.title);
				self.setContentAppend(r.template);
			});
		}
	});
}
function showSignUpForm(bt,event){
	event.preventDefault();
	var url=$(bt).attr('href');
	//alert(url);
	$.activity({
		title:'',
		logo:'',
		content:function(){
			var self=this;
			activity_signup=self;
			return $.post(url)
			.done(function(data){
				var r=JSON.parse(data);
				//self.$closeIcon.remove();
				self.setClassToolBar('text-white color-bg-header');
				self.setBackgroundActivityPane('#F3E8E8');
				self.setTitle(r.title);
				self.setContentAppend(r.template);
			});
		}
	});
}
function showLateralMenu(bt,event){
	event.preventDefault();
	$(".apps").find('a').each(function(){
		if($(this).attr('data-active')){
			var url=$(bt).attr('href')+$(this).attr('data-app');
			//alert(url);
			$.activity({
				title:'',
				logo:'',
				content:function(){
					var self=this;
					activity_menu_lateral=self;
					return $.post(url)
					.done(function(data){
						
						var r=JSON.parse(data);
						//self.$closeIcon.remove();
						self.setClassToolBar('text-white color-bg-header');
						self.setBackgroundActivityPane('white');
						self.setTitle(r.title);
						self.setContentAppend(r.template);
					});
				}
			});
		}
	});
}
function chooseBoardingPlace(bt,event){
	event.preventDefault();
	var url=$(bt).attr('data-url');
	$.activity({
		title:'',
		logo:'',
		content:function(){
			var self=this;
			activity_boarding_places=self;
			return $.post(url,{"bpi":$(bt).find('input').val()})
			.done(function(data){
				var r=JSON.parse(data);
				//self.$closeIcon.remove();
				self.setClassToolBar('text-white color-bg-header');
				self.setBackgroundActivityPane('white');
				self.setTitle(r.title);
				self.setContentAppend(r.template);
			});
		}
	});
}
function chooseDate(bt,event){
	event.preventDefault();
	var url=$(bt).attr('data-url');
	$.activity({
		title:'',
		logo:'',
		content:function(){
			var self=this;
			activity_date=self;
			return $.post(url,{"d":$(bt).find('input').val()})
			.done(function(data){
				var r=JSON.parse(data);
				//self.$closeIcon.remove();
				self.setClassToolBar('text-white color-bg-header');
				self.setBackgroundActivityPane('white');
				self.setTitle(r.title);
				self.setContentAppend(r.template);
			});
		}
	});
}
function choosePax(bt,event){
	event.preventDefault();
	var url=$(bt).attr('data-url');
	$.activity({
		title:'',
		logo:'',
		content:function(){
			var self=this;
			activity_pax=self;
			return $.post(url,{"q":$(bt).find('input').val()})
			.done(function(data){
				var r=JSON.parse(data);
				//self.$closeIcon.remove();
				self.setClassToolBar('text-white color-bg-header');
				self.setBackgroundActivityPane('white');
				self.setTitle(r.title);
				self.setContentAppend(r.template);
			});
		}
	});
}

function selectBoardingPlace(bt,event){
	event.preventDefault();
	$("#boarding_place").find('.title').html($(bt).attr('data-title'));
	$("#boarding_place").find('.sub-title').html($(bt).attr('data-subtitle'));
	$("#boarding_place").find('input').val($(bt).attr('data-id'));
	activity_boarding_places.close();
}
function selectDate(bt,event){
	event.preventDefault();
	$("#date").find('input').val($(bt).attr('data-date'));
	$.post($(bt).attr('data-redirect'),{"d":$(bt).attr('data-date')}).
	done(function(data){
		var r=JSON.parse(data);
		$("#date").find('.title').html(r.day_number+' '+r.month_short);
		$("#date").find('.sub-title').html(r.day_of_the_week);
		activity_date.close();
	});
	
}
function selectPax(bt,event){
	event.preventDefault();
	$("#pax").find('.title').html($(bt).attr('data-title'));
	$("#pax").find('input').val($(bt).attr('data-quantity'));
	activity_pax.close();
}

function selectDeparture(bt,event){
	event.preventDefault();
	var form=$($(bt).attr('href'));
	$(form).submit();
}

function selectSeat(bt,event){
	event.preventDefault();
	var num_seat=$(bt).html();
	var price=parseFloat($("#btnContinue").attr('data-unitprice'));

	if($(bt).hasClass('btn-success')){
		var selected=0;
		$('table').find('.seat').each(function(){
			if($(this).hasClass('btn-warning')){
				selected++;
			}
		});

		if(selected<parseInt($("#btnContinue").attr('data-quantity'))){
			$(bt).removeClass('btn-success');
			$(bt).addClass('btn-warning');

			$(bt).append('<div role="detail">'+
					 '	<input type="hidden" name="details['+selected+'][seat]" role="seat" value="'+num_seat+'" />'+
					 '	<input type="hidden" name="details['+selected+'][passenger]" role="passenger" value="0" />'+
					 '	<input type="hidden" name="details['+selected+'][price]" role="price" value="'+price.toFixed(2)+'" />'+
					 '</div>');
		}

		selected=0;
		$('table').find('.seat').each(function(){
			if($(this).hasClass('btn-warning')){
				selected++;
			}
			$("#btnContinue").html(selected+' asientos seleccionados - Continuar');
		});
		if($("#btnContinue").attr('data-quantity')==selected){
			$("#btnContinue").removeAttr('disabled');
		}else{
			
				$("#btnContinue").attr('disabled','disabled');
			
		}
		return;
	}
	if($(bt).hasClass('btn-warning')){
		$(bt).removeClass('btn-warning');
		$(bt).addClass('btn-success');
		$(bt).find('div[role="detail"]').remove();
		var selected=0;
		$('table').find('.seat').each(function(){
			if($(this).hasClass('btn-warning')){
				selected++;
			}
			$("#btnContinue").html(selected+' asientos seleccionados - Continuar');
		});	
		if($("#btnContinue").attr('data-quantity')==selected){
			$("#btnContinue").removeAttr('disabled');
		}else{
			$("#btnContinue").attr('disabled','disabled');
		}
		return;
	}
}
function searchPerson(bt,event){
	event.preventDefault();
	var document_type=$("#document_type").val();
	var document_number=$("#document_number").val();
	if($("#document_number").val().length==0){
		$(bt).parent().parent().find('.document-number-error').removeClass('d-none');
		return;
	}else{
		if(document_type==1 && document_number.length!=8){
			$(bt).parent().parent().find('.document-number-error').removeClass('d-none');
			return;
		}
		$(bt).parent().parent().find('.document-number-error').addClass('d-none');
	}
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	$.post($(bt).attr('href'),{"document_type":document_type,"document_number":document_number}
		).done(function(data){
			var r=JSON.parse(data);
			$("#content_personal_information").html(r.template);
			hide_loadingOverlay(bt);
		});
}