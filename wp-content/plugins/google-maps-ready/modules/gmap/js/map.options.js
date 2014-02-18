function getInfoWindow(title,content,titleLink){
        if(titleLink.linkEnabled && titleLink.linkEnabled!="false"){
            title= "<a href='"+titleLink.link+"' target='_blank' class='gmpInfoWIndowTitleLink'>"+title+"</a>"
        }
	var text="<div class='gmpMarkerInfoWindow'>";
	text+="<div class='gmpInfoWindowtitle'>"+title;
	text+="</div>";
	text+="<div class='gmpInfoWindowContent'>"+content;
	text+="</div></div>";
	 var infoWindow = new google.maps.InfoWindow({
	  content: text
  });
  return infoWindow;
}

var gmapPreview={
	maps:[],
	mapObjects:[],
	mapItemsContainer:{},
	prepareToDraw:function(mapId){
	   if(typeof(mapId)=='undefined'){
		   return false;
	   }
	   var currentMap = gmapPreview.maps[mapId].mapParams;
	   if(currentMap.params.map_display_mode=='popup'){
			jQuery("#show_map_icon.map_num_"+mapId).click(function(){
				  var map_id=jQuery(this).attr("data_val");
				  jQuery("#map_container_"+mapId+".map_container.display_as_popup").bPopup();
				  gmapPreview.drawMap(currentMap);  
			})
		}else if(currentMap.params.map_display_mode=='map'){
			this.drawMap(currentMap);	
		}
	},
	drawMap:function(mapForPreview){
				if(typeof(mapForPreview)==undefined){
					return false;
				}
				var mapElemId = "ready_google_map_"+mapForPreview.id;
				if(typeof(mapForPreview.params.map_center)!=undefined){
					 var lat = mapForPreview.params.map_center.coord_y;
					 var lng = mapForPreview.params.map_center.coord_x;
				} else if(mapForPreview.markers.length>0){
					  var lat = mapForPreview.markers[0].coord_y;
					  var lng = mapForPreview.markers[0].coord_x;
				}else{
					  var lat = 44.5234475708;
					  var lng = 40.1879714881 ;
				}
			 var mapCenter = new google.maps.LatLng(lat,lng);

			  var mapOptions = {
				   center: mapCenter,
				   zoom: parseInt(mapForPreview.params.zoom),
				   scrollwheel: false,//mouse disable
				   draggable: false,//drag map
				   zoomControl: Boolean(parseInt(mapForPreview.params.enable_zoom)),
				   disableDoubleClickZoom:true,
			 };
			 if(mapForPreview.params.enable_mouse_zoom==1){
				 mapOptions.disableDoubleClickZoom=false;
				 mapOptions.scrollwheel=true;
				 mapOptions.draggable=true;
			 }  
				 mapOptions.mapTypeId= google.maps.MapTypeId[mapForPreview.params.type];
				 var map = new google.maps.Map(document.getElementById(mapElemId),mapOptions);
				 this.maps[mapForPreview.id].mapObject = map;
				 
			 if(mapForPreview.markers.length>0){
				  this.drawMarkers(mapForPreview.markers,mapForPreview.id);	  
			   }	
		 google.maps.event.addListenerOnce(this.maps[mapForPreview.id].mapObject, 'tilesloaded', function(){
				  gmpAddLicenzeBlock(mapElemId);
		  });	
		  delete map;
		 },
		 drawMarkers:function(markerList,mapId){
			  for(var i in markerList){
				 this.createMarker(markerList[i],mapId);
			  }   
		 },
		 createMarker:function(markerItem,mapId){
				var iconUrl =GMP_DATA.imgPath+"markers/marker_1.png"; 
				if(markerItem.icon!=undefined && markerItem.icon.path!=undefined){
					iconUrl = markerItem.icon.path;
				}
				 var markerIcon = {
						url: iconUrl,
						//size: new google.maps.Size(32, 37),
						origin: new google.maps.Point(0,0),
				 };
				var markerLatLng = new google.maps.LatLng(markerItem.coord_y,markerItem.coord_x);
				var animType = 2;
				if(markerItem.animation==1){
					var animType = 1;
				}
			   this.maps[mapId].markerArr[markerItem.id] = new google.maps.Marker({
					position: markerLatLng,
					title:markerItem.title,
					description:markerItem.description,
					icon:markerIcon,
					draggable:false,
					map:gmapPreview.maps[mapId].mapObject,
					animation:animType,
					address:markerItem.address,
					id     : markerItem.id
			   })
                        if(typeof(markerItem.titleLink)=='undefined'){
                            markerItem.titleLink={
                                linkEnabled:false
                            }
                        }
		var infoWindow=getInfoWindow(markerItem.title,markerItem.description,markerItem.titleLink);
				google.maps.event.addListener(this.maps[mapId].markerArr[markerItem.id],'click',function(){
					 for(var i in gmapPreview.maps[mapId].infoWindows){
						 gmapPreview.maps[mapId].infoWindows[i].close();
					 }
				  infoWindow.open(gmapPreview.maps[mapId].mapObject,gmapPreview.maps[mapId].markerArr[markerItem.id]);
				  toggleBounce(gmapPreview.maps[mapId].markerArr[markerItem.id],animType)
			   });
			  this.maps[mapId].infoWindows[markerItem.id] =  infoWindow;
		 }
}

function closePopup(){
	jQuery(".map_container.display_as_popup").bPopup().close();	
}
jQuery(document).ready(function(){
	if(typeof(gmpAllMapsInfo)!="undefined"){
		console.log(gmpAllMapsInfo);
		for(var i in gmpAllMapsInfo){
			var map_id = gmpAllMapsInfo[i].id;
		
				gmapPreview.maps[map_id]={
						mapObject:{},
						markerArr:{},
						infoWindows:{},
						mapParams:gmpAllMapsInfo[i]
				  };
			gmapPreview.prepareToDraw(map_id);
		}         
	}
})