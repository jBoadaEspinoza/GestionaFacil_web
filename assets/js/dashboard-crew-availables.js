function add(bt,event){
	show_loadingOverlay(bt,[255,255,255,0,0.5]);
	event.preventDefault();
	$.dialog({
		closeIcon: true,
		type: 'red',
    	typeAnimated: true,
    	boxWidth: '50%',
    	useBootstrap: false,
		content:function(){
			var self=this;
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