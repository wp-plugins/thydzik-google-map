function makeMap(idname,xmlname,awidth,aheight,datafolder,markersfolder,azoom,maptype){if(GBrowserIsCompatible()){var gmarkers=[];var imarkers=0;var m=document.getElementById(idname);m.style.width=awidth;m.style.height=aheight;var icons=[];icons[""]=new GIcon(G_DEFAULT_ICON);function numberedIcon(iconStr){if(((0<=iconStr)&&(iconStr<=99))||(("A"<=iconStr)&&(iconStr<="Z"))){if(!icons[iconStr]){var icon=new GIcon(icons[""]);if(("A"<=iconStr)&&(iconStr<="Z")){icon.image="http://www.google.com/mapfiles/marker"+iconStr+".png";}else{icon.image=markersfolder+"marker"+iconStr+".png";}icons[iconStr]=icon;return icons[iconStr];}}else{return icons[""];}}function createMarker(point,html,icon){var marker=new GMarker(point);if(html){var markerOptions={icon:numberedIcon(icon),clickable:true};}else{var markerOptions={icon:numberedIcon(icon),clickable:false};}marker=new GMarker(point,markerOptions);GEvent.addListener(marker,"click",function(){marker.openInfoWindowHtml(html);});gmarkers[imarkers]=marker;imarkers++;return marker;}var map=new GMap2(document.getElementById(idname));map.addControl(new GSmallMapControl());map.addControl(new GMapTypeControl());map.setCenter(new GLatLng(0,0),0);map.addMapType(G_PHYSICAL_MAP);map.removeMapType(G_HYBRID_MAP);amaptype=maptype.toUpperCase();if(amaptype=="NORMAL"||amaptype=="G_NORMAL_MAP"||amaptype=="N"){map.setMapType(G_NORMAL_MAP);}else if(amaptype=="SATELLITE"||amaptype=="G_SATELLITE_MAP"||amaptype=="S"){map.setMapType(G_SATELLITE_MAP);}else if(amaptype=="HYBRID"||amaptype=="G_HYBRID_MAP"||amaptype=="H"){map.setMapType(G_HYBRID_MAP);}else if(amaptype=="PHYSICAL"||amaptype=="G_PHYSICAL_MAP"||amaptype=="P"||amaptype=="TERRAIN"||amaptype=="T"){map.setMapType(G_PHYSICAL_MAP);}else{map.setMapType(G_NORMAL_MAP);}var bounds=new GLatLngBounds();GDownloadUrl(xmlname,function(doc,responseCode){if(responseCode==200){var xmlDoc=GXml.parse(doc);var markers=xmlDoc.documentElement.getElementsByTagName("marker");for(var i=0;i<markers.length;i++){var lat=markers[i].getAttribute("lat");var lng=markers[i].getAttribute("lng");if((lat)&&(lng)){var point=new GLatLng(parseFloat(lat),parseFloat(lng));var html=markers[i].getAttribute("html");if(html){html=html.replace(html.match(/href='(?!(http|ftp))/ig),"href='".concat(datafolder));html=html.replace(html.match(/href="(?!(http|ftp))/ig),'href="'.concat(datafolder));html=html.replace(html.match(/src='(?!(http|ftp))/ig),"src='".concat(datafolder));html=html.replace(html.match(/src="(?!(http|ftp))/ig),'src="'.concat(datafolder));}var icon=markers[i].getAttribute("icon");if(!icon){icon="";}else{icon=icon.toUpperCase();}var marker=createMarker(point,html,icon);map.addOverlay(marker);bounds.extend(point);}}var inputs=[];inputs=inputs.concat(xmlDoc.documentElement.getElementsByTagName("line"));inputs=inputs.concat(xmlDoc.documentElement.getElementsByTagName("points"));var lines="";for(i=0;i<2;i++){lines=inputs[i];for(var j=0;j<lines.length;j++){var colour=lines[j].getAttribute("colour");if(!colour){colour=lines[j].getAttribute("color");}if(!colour){colour="#0000ff";}var width=lines[j].getAttribute("width");if(!width){width="4";}width=parseFloat(width);var opacity=lines[j].getAttribute("opacity");if(!opacity){opacity="0.75";}opacity=parseFloat(opacity);var points=lines[j].getElementsByTagName("point");var pts=[];for(var k=0;k<points.length;k++){pts[k]=new GLatLng(parseFloat(points[k].getAttribute("lat")),parseFloat(points[k].getAttribute("lng")));bounds.extend(pts[k]);}map.addOverlay(new GPolyline(pts,colour,width,opacity));}}if(azoom==-1){map.setZoom(map.getBoundsZoomLevel(bounds));}else{map.setZoom(azoom);}map.setCenter(bounds.getCenter());}else if(responseCode==-1){}else{}});}}