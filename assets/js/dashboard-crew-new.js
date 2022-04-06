var confirm_searchperson;
function searchPerson(bt,event){
	event.preventDefault();
	confirm_searchperson=null;
	$.dialog({
		closeIcon: true,
		type: 'red',
    	typeAnimated: true,
    	boxWidth: '50%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm_searchperson=self;
			return $.post($(bt).attr('data-url-search-person'))
			.done(function(data){
				var r=JSON.parse(data);
				self.setTitle(r.title);
				self.setContentAppend(r.template);
			});
		}
	});
}
function setFilterForPersonByDocumentFromForm(form,event){
	if(event.key=='Enter'){
		if($('#document_number').val()!=""){
			var bt=confirm_searchperson.$content.find(form).find('button');
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
					var content=confirm_searchperson.$content.find(form).parent().parent().parent();
					$(content).find('.card').html(r.template);
					hide_loadingOverlay(bt);
				});
			} 
		}
	}
	if(event.key=='Escape'){
		confirm_searchperson.close();
		event.preventDefault();
	}
}	
function setFilterForPersonByDocument(bt,event){
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
			var content=confirm_searchperson.$content.find(form).parent().parent().parent();
			$(content).find('.card').html(r.template);
			hide_loadingOverlay(bt);
		});
	}
}
function selectPerson(bt,event){
	event.preventDefault();
	if(typeof confirm_searchperson!=='undefined'){
		var person_info=$(bt).attr('data-document')+'|'+$(bt).attr('data-fullname');
		$("#panel_crew").find('#person').val(person_info);
		$("#panel_crew").find('input[name=person_id]').val($(bt).attr('data-id'));
		confirm_searchperson.close();
	}
}
function save_crew(bt,event){
	event.preventDefault();
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	var form=$("#panel_crew");
	$.post($(form).attr("action"),$(form).serializeArray()
		).done(function(data){
			var r=JSON.parse(data);
			if(r.success){
				alert(r.msg);
				location.href =r.redirect;
			}else{
				alert(r.msg);
			}
			hide_loadingOverlay(bt);
		});
}