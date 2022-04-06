function show(bt,event){
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
function save(bt,event){
	event.preventDefault();
	$.post($("#form_documents").attr('action'),$("#form_documents").serializeArray()
		).done(function(data){
			var r=JSON.parse(data);
			$.dialog({
			    title: r.title,
			    content: r.msg,
			});
		});
}