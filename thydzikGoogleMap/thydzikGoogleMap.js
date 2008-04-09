

    //<![CDATA[
	// this is the Javascript that makes the map.  It should be defined in the head of the document
function makeMap(idname, xmlname, markersfolder, awidth, aheight) {
    if (GBrowserIsCompatible()) {
    
      // arrays to hold copies of the markers and html used by the side_bar
      // because the function closure trick doesnt work there
      var gmarkers = [];
      var i = 0;
		
		
      // resize the map
      var m = document.getElementById(idname);
      m.style.width = awidth;
	  m.style.height = aheight;

	  var map = new GMap(m);
	  
	  // Create our base marker icon 
	var icons = new Array();
	icons[""] = new GIcon(); 
	icons[""].image = "http://www.google.com/mapfiles/marker.png"; 
	icons[""].shadow="http://www.google.com/mapfiles/shadow50.png"; 
	icons[""].iconSize=new GSize(20, 34); 
	icons[""].shadowSize=new GSize(37, 34); 
	icons[""].iconAnchor=new GPoint(9,34); 
	icons[""].infoWindowAnchor=new GPoint(9,2); 
	icons[""].infoShadowAnchor=new GPoint(18,25); 
	icons[""].printImage="http://www.google.com/mapfiles/markerie.gif"; 
	icons[""].mozPrintImage="http://www.google.com/mapfiles/markerff.gif"; 
	icons[""].printShadow="http://www.google.com/mapfiles/dithshadow.gif"; 
	icons[""].transparent="http://www.google.com/mapfiles/markerTransparent.png";
	icons[""].imageMap=[9,0,6,1,4,2,2,4,0,8,0,12,1,14,2,16,5,19,7,23,8,26,9,30,9,34,11,34,11,30,12,26,13,24,14,21,16,18,18,16,20,12,20,8,18,4,16,2,15,1,13,0]; 

	function numberedIcon(iconNumber) {
	   var number;
	   if (isNaN(parseInt(iconNumber))) {
		  number = "";
	   } else if (!isNaN(parseInt(iconNumber)) && ((iconNumber < 0) || (iconNumber > 99))) {
		  number = "";
	   } else if ((typeof(iconNumber)=="undefined") || (iconNumber==null)) { 
		  number = "" 
	   } else { 
		  number = iconNumber; 
	   }
	   if (!icons[iconNumber]) {
		  var icon = new GIcon(icons[""]);
		  icon.image = markersfolder + "marker" + number + ".png";
		  icons[iconNumber]=icon;
	   } 
	   return icons[iconNumber];
	}


		// A function to create the marker and set up the event window
		// function createMarker(point,name,html) {
		function createMarker(point, name, html, iconStr) {
			//html = '<div style="white-space:nowrap;">' + html + '</div>'; // FF 1.5 fix
			var marker = new GMarker(point);
	
			if (iconStr) {
				marker = new GMarker(point, numberedIcon(iconStr));
			}
			
			GEvent.addListener(marker, "click", function() {
				marker.openInfoWindowHtml(html);
			});		
			gmarkers[i] = marker;
			i++;
			return marker;
		}
	  
	   // This function picks up the click and opens the corresponding info window
      function myclick(i) {
        GEvent.trigger(gmarkers[i], "click");
      }


      // create the map
      var map = new GMap(document.getElementById(idname));
      map.addControl(new GSmallMapControl());
      map.addControl(new GMapTypeControl());
      map.setCenter(new GLatLng(0,0),0);

      // ===== Start with an empty GLatLngBounds object =====     
      var bounds = new GLatLngBounds();

      //GDownloadUrl(document.getElementById('map').getAttribute('xmlfilename'), function(doc) {
		GDownloadUrl(xmlname, function(doc, responseCode) {	
			var xmlDoc = GXml.parse(doc);
			// obtain the array of markers and loop through it
			var markers = xmlDoc.documentElement.getElementsByTagName("marker");
			for (var i = 0; i < markers.length; i++) {
				// obtain the attribues of each marker
				var lat = parseFloat(markers[i].getAttribute("lat"));
				var lng = parseFloat(markers[i].getAttribute("lng"));
				var point = new GLatLng(lat,lng);
				var html = markers[i].getAttribute("html");
				var label = markers[i].getAttribute("label");
				var icon = markers[i].getAttribute("icon");
				// create the marker
				var marker = createMarker(point,label,html,icon);
				map.addOverlay(marker);
				// ==== Each time a point is found, extend the bounds ato include it =====
				bounds.extend(point);
				}
		
		  // ========= Now process the polylines ===========
          var lines = xmlDoc.documentElement.getElementsByTagName("line");
          // read each line
          for (var a = 0; a < lines.length; a++) {
				// get any line attributes
				var colour = lines[a].getAttribute("colour");
				var width  = parseFloat(lines[a].getAttribute("width"));
				// read each point on that line
				var points = lines[a].getElementsByTagName("point");
				var pts = [];
					for (var i = 0; i < points.length; i++) {
						pts[i] = new GLatLng(parseFloat(points[i].getAttribute("lat")),
											 parseFloat(points[i].getAttribute("lng")));
						bounds.extend(pts[i]);
						}
				map.addOverlay(new GPolyline(pts,colour,width));
				
				bounds.extend(point);
          		}
          // ================================================  
		
		  // ===== determine the zoom level from the bounds =====
          map.setZoom(map.getBoundsZoomLevel(bounds));


          // ===== determine the centre from the bounds ======
          map.setCenter(bounds.getCenter());

/*		if(azoom==null) {
		  // ===== determine the zoom level from the bounds =====
          map.setZoom(map.getBoundsZoomLevel(bounds));
		} else {
		  map.setZoom(map.getBoundsZoomLevel(bounds) + azoom);;
		}*/

      });
    }
	}

    // This Javascript is based on code provided by the
    // Blackpool Community Church Javascript Team
    // http://www.commchurch.freeserve.co.uk/   
    // http://www.econym.demon.co.uk/googlemaps/

    //]]>
