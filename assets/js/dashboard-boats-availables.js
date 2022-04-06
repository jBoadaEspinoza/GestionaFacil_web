function nextModelBoat(bt,event){
   event.preventDefault();
   var current_boat_model_id=$(bt,event).attr('data-id');
   var url=BASE_URL+"dashboard/templates/boat/availables/show_next_boat_model/es";
    $.post(url,{"boat_model_id":current_boat_model_id}
      ).done(function(data){
         var r=JSON.parse(data);
         $("#boatModel_panel").html(r.template);
         hide_loadingOverlay(bt,event);
   });
}
function backModelBoat(bt,event){
   event.preventDefault();
   var current_boat_model_id=$(bt,event).attr('data-id');
   var url=BASE_URL+"dashboard/templates/boat/availables/show_back_boat_model/es";
    $.post(url,{"boat_model_id":current_boat_model_id}
      ).done(function(data){
         var r=JSON.parse(data);
         $("#boatModel_panel").html(r.template);
         hide_loadingOverlay(bt,event);
         return;
   });
}
function newBoat(bt,event){
   event.preventDefault();
	var url=BASE_URL+"dashboard/templates/boat/availables/show_new/es";
   	$.post(url).done(function(data){
   		var r=JSON.parse(data);
   		$("#boats_availables").html(r.template);
   		hide_loadingOverlay(bt,event);
         return;
   	});
}
function saveBoatRecord(bt,event){
   event.preventDefault();
   var boat_plate=$("#boat_plate").val();
   var boat_name=$("#boat_name").val();
   var boat_comment=$("#boat_comment").val();
   var boat_seat_model=$("#model_id").html();
   var url=BASE_URL+"dashboard/templates/boat/availables/save_boat_record/es";
   show_loadingOverlay(bt,[255,255,255,0,0.5]);
   $.post(url,{"plate":boat_plate,"name":boat_name,"comment":boat_comment,"model_id":boat_seat_model}
      ).done(function(data){
         var r=JSON.parse(data);
         if(r.success){
            alert(r.msg);
            location.reload();
            hide_loadingOverlay(bt,event);
            return;
         }
   });
}
function nextBoat(bt,event){
   event.preventDefault();
   var url=BASE_URL+"dashboard/templates/boat/availables/show_next/es";
   var plate=$("#boat_plate").val();
   $.post(url,{"plate":plate}
   	).done(function(data){
   		var r=JSON.parse(data);
   		$("#boats_availables").html(r.template);
   		hide_loadingOverlay(bt,event);
         return;
   	});
}
function backBoat(bt,event){
   event.preventDefault();
   var url=BASE_URL+"dashboard/templates/boat/availables/show_back/es";
   var plate=$("#boat_plate").val();
   $.post(url,{"plate":plate}
   	).done(function(data){
   		var r=JSON.parse(data);
   		$("#boats_availables").html(r.template);
   		hide_loadingOverlay(bt,event);
   	});
}
function firstBoat(bt,event){
   event.preventDefault();
   var url=BASE_URL+"dashboard/templates/boat/availables/show_first/es";
   var plate=$("#boat_plate").val();
   $.post(url,{"plate":plate}
   	).done(function(data){
   		var r=JSON.parse(data);
   		$("#boats_availables").html(r.template);
   		hide_loadingOverlay(bt,event);
         return;
   	});
}
function lastBoat(bt,event){
   event.preventDefault();
   var url=BASE_URL+"dashboard/templates/boat/availables/show_last/es";
   var plate=$("#boat_plate").val();
   $.post(url,{"plate":plate}
   	).done(function(data){
   		var r=JSON.parse(data);
   		$("#boats_availables").html(r.template);
   		hide_loadingOverlay(bt,event);
         return;
   	});
}