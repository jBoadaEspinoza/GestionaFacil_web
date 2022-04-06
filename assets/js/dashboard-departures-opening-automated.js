var confirm_add_departures=null;

function open_template_add_new_departure_time(bt,event){
    event.preventDefault();
    confirm_add_departures=null;
	$.dialog({
		closeIcon: true,
		type: 'red',
    	typeAnimated: true,
        container: 'body',
        containerFluid: false,
    	boxWidth: '40%',
    	useBootstrap: false,
		content:function(){
			var self=this;
			confirm_departures=self;
			return $.post($(bt).attr('href'))
			.done(function(data){
				var r=JSON.parse(data);
				self.setTitle(r.title);
				self.setContentAppend(r.template);
			});
		}
	});
}

function save_new_departure_time(bt,event){
    event.preventDefault();
    show_loadingOverlay(bt,[255,255,255,0,0.5]);
    $.post($(bt).attr('href'),$("#form_new_departure_time").serializeArray())
        .done(function(data){
             var r=JSON.parse(data);
             alert(r.msg);
             location.reload();
             hide_loadingOverlay(bt);
             return;   
        });
}

function changeToCustomizeDays(cb,event){
   
    event.preventDefault();
    $.post($(cb).attr('data-url'),{"case":$(cb).val()}).done(function(template){
            let r=JSON.parse(template);
            $("#days").html(r.template.content);
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