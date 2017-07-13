

var map; var infowindow;
function initMap() {

	//Crear nuevo mapa
	map = new google.maps.Map(document.getElementById('map'), {
	  center: {lat: 28.463938, lng: -16.262598}, //(Y,X)Sta Cruz de Tenerife
	  zoom: 12
	});


	//Crear infowindow 
	infowindow = new google.maps.InfoWindow();
	

	//Añadimos marcadores desde el json de coordenadas
	addMarkers();
}


function addMarkers(){
	
	//Obtener json
	$.getJSON("http://localhost/TFG/dump/coordenadas.json", function(json) {
	
		// Recorrer el JSON para obtener los datos
		for (var i = 0, length = json.length; i < length; i++) {
			var data = json[i],
				latLng = new google.maps.LatLng(data.LATITUD, data.LONGITUD);


			// Creando marcador e insertandolo en el mapa "map"
			var marker = new google.maps.Marker({
				position: latLng,
				map: map,
				title: data.BARRIO
			});


			// Creating a closure to retain the correct data, notice how I pass the current data in the loop into the closure (marker, data)
			(function(marker, data) {

				// Attaching a click event to the current marker
				google.maps.event.addListener(marker, "click", function(e) {
					infowindow.setContent(data.DISTRITO);
					infowindow.open(map, marker);
				});

			})(marker, data);
		}
	});

}


