function openMenuForMobile(bt,event){
	$.activity({
		title:'',
		logo:BASE_LOGO_URL,
		content:function(){
			var self=this;
			//parent_self=this;
			return $.post($(bt).attr('url'))
			.done(function(data){
				var r=JSON.parse(data);
				//self.setIconToolBar('fas fa-arrow-left');
				self.setClassToolBar('text-white color-bg-header');
				self.setBackgroundActivityPane('#d8d8d8');
				self.setTitle(r.title);
				self.setContentAppend(r.template);

			}).fail(function(){
            	//self.setContentAppend('<div>Fail!</div>');
        	}).always(function(){
            	//self.setContentAppend('<div>Always!</div>');
        	});
		},
		contentLoaded: function(data, status, xhr){
	        //self.setContentAppend('<div>Content loaded!</div>');
	    },
		onContentReady: function(){
			var self=this;
    	}
	});
}