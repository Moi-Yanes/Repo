
//INICIALIZAR MAPA CREANDO EL ARRAY DE LOCALIZACIONES
function initMap() {

	var map = new google.maps.Map(document.getElementById('map'), {
	  center: {lat: 28.463938, lng: -16.262598}, //(Y,X)Sta Cruz de Tenerife
	  zoom: 12
	});

	var infoWin = new google.maps.InfoWindow();

	 //Obtenemos json con noticias agrupadas por ubicacion. Tenemos un array de arrays de tal forma que:
	 /*
		En la posicion 0 tenemos todas las noticias(cada una en un array) referentes a la ubicacion X
		En la posicion 1 tenemos todas las noticias(cada una en un array) referentes a la ubicacion Y
		...
	 */
	 $.getJSON("http://localhost/TFG/dump/coordenadas.json", function(json) {
		var locations= [];
		
		//Obtener ubicaciones de cada agrupacion de noticias para posteriormente agruparlas por cercania 
		for (var i = 0; i < json.length; i++) { 					
			var data   = json[i];
			var contentString = '<div id="content">'+
						'<div id="siteNotice"></div>'+
						'<h1 id="firstHeading" class="firstHeading">'+data[0].UBICACION+'</h1>'+
						'<div id="bodyContent">'+
							'<p><b>'+data[0].UBICACION+'</b>, haciendo click en el boton siguiente podras acceder a todas las noticias de este sitio: </p><br>'+
							'<input type="button" value="Ver" onclick="show_noticias('+i+')">'+
						'</div>'+
					    '</div>';

			locations.push({'lat' : parseFloat(data[0].LATITUD), 'lng': parseFloat(data[0].LONGITUD), 'info':  contentString});
		} 
		
		addMarkers(locations, map, infoWin);
	});
}


//AÑADIR MARCADORES AGRUPADOS EN CLUSTERES
function addMarkers(locations, map, infoWin){

	
	// Add some markers to the map.
	// Note: The code uses the JavaScript Array.prototype.map() method to
	// create an array of markers based on a given "locations" array.
	// The map() method here has nothing to do with the Google Maps API.
	var markers = locations.map(function(location, i) {
		var marker = new google.maps.Marker({
			position: location
		});
		google.maps.event.addListener(marker, 'click', function(evt) {
			infoWin.setContent(location.info);
			infoWin.open(map, marker);
		})
		return marker;
	});

	// markerCluster.setMarkers(markers);
	// Add a marker clusterer to manage the markers.
	var markerCluster = new MarkerClusterer(map, markers, {
		imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
	});
}



/*
function addMarkers(){
	var currWindow = false; 
	//Obtener json
	$.getJSON("http://localhost/TFG/dump/coordenadas.json", function(json) {

		// Recorrer el JSON para obtener los datos
		for (var i = 0; i < json.length; i++) { 					//Recorrer array principal
			var data   = json[i];
			var latLng = new google.maps.LatLng(data[0].LATITUD, data[0].LONGITUD);
			
			var contentString = '<div id="content">'+
						'<div id="siteNotice"></div>'+
						'<h1 id="firstHeading" class="firstHeading">'+data[0].UBICACION+'</h1>'+
						'<div id="bodyContent">'+
							'<p><b>'+data[0].UBICACION+'</b>, haciendo click en el boton siguiente podras acceder a todas las noticias de este sitio: </p><br>'+
							'<input type="button" value="Ver" onclick="show_noticias('+i+')">'+
						'</div>'+
					    '</div>';

			var infowindow = new google.maps.InfoWindow({
			    content: contentString
			});


			var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

			var marker = locations.map(function(location, i) {
				return new google.maps.Marker({
					position: latLng,
					label: labels[i % labels.length],
					title: data[0].UBICACION,
			    		infowindow: infowindow
				});
			});


			// Add a marker clusterer to manage the markers.
			var markerCluster = new MarkerClusterer(map, marker,
			    {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
			

			google.maps.event.addListener(marker, 'click', function() {
				if( currWindow )
					currWindow.close();
				
				currWindow = this.infowindow;
				this.infowindow.open(map, this);
			});	
		}
	});
}*/


function show_noticias(index){

	alert(index);

	/*for (var i = 0; i < json.length; i++) { 					//Recorrer array principal
		var data   = json[i],
		    latLng = new google.maps.LatLng(data[0].LATITUD, data[0].LONGITUD);
	
		/*for (var j = 0; j < data.length; j++) {				//Recorrer subarrays(los agrupados por ubicacion)
			//console.log(data[j].UBICACION);
		
		}
	}*/
}


