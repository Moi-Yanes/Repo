

var map;
function initMap() {
	map = new google.maps.Map(document.getElementById('map'), {
	  center: {lat: 28.463938, lng: -16.262598}, //(Y,X)
	  zoom: 15
	});

	var marcadores = [
		['IGUESTE DE SAN ANDRES', 28.5388712223, -16.164973018],
		['VALLESECO', 28.5016481037, -16.244637817],
		['LOS CAMPITOS', 28.4851829752, -16.2667995069]
	];

	var infowindow = new google.maps.InfoWindow();
	var marker, i;

	for (i = 0; i < marcadores.length; i++) {  
		marker = new google.maps.Marker({
		  position: new google.maps.LatLng(marcadores[i][1], marcadores[i][2]),
		  map: map
		});

		google.maps.event.addListener(marker, 'click', (function(marker, i) {
		  return function() {
		    infowindow.setContent(marcadores[i][0]);
		    infowindow.open(map, marker);
		  }
		})(marker, i));
	}
}

