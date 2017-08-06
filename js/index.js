

//INICIALIZAR MAPA CREANDO EL ARRAY DE LOCALIZACIONES
var globaljson; var map;
function initMap() {

	map = new google.maps.Map(document.getElementById('map'), {
	  center: {lat: 28.463938, lng: -16.262598}, //(Y,X)Sta Cruz de Tenerife
	  zoom: 12,
	  mapTypeControl: false
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
							'<input id="btnver" type="button" value="Ver" onclick="show_noticias('+i+')">'+
						'</div>'+
					    '</div>';

			locations.push({'lat' : parseFloat(data[0].LATITUD), 'lng': parseFloat(data[0].LONGITUD), 'info':  contentString});
			globaljson = json;
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




function show_noticias(index){
	var data   = globaljson[index];


	//Si esta visible el box2 desplazarnos a la izq
	if (boxVisible("div_box2") == true){
		goLeft(index);
	}
	

	//Limpiar div y borrar su contenido	
	document.getElementById("content_d2b1").innerHTML ='';


	//Quitar clases vertical y horizontal center al div del contenido
	if ( $("#d2b_divrow").hasClass('vertical_center')){
		$("#d2b_divrow").removeClass("vertical_center");
	}
	if ( $("#d2b_divrow").hasClass('horizontal_center')){
		$("#d2b_divrow").removeClass("horizontal_center");
	}
	

	//Establecer titulo 
	var div_title = '<div class="row padding_row"><div style="margin-top:15px; margin-bottom:15px;" class="col-md-12 horizontal_center">'+
				'<h4>'+data[0].UBICACION+'</h4>'+
			'</div></div>';
	document.getElementById("content_d2b1").innerHTML += div_title;


	//Establecer noticias
	var div_news = '';
	for (var j = 0; j < data.length; j++) {	
		if(j == data.length-1)
			div_news += '<div class="row row_final_news padding_row">';
		else{		
			div_news += '<div class="row padding_row">';
		}
		div_news += '<div class="col-md-12">'+
				'<div class="div_img_izq"><img class="img_izquierda" src="http://localhost/TFG/images/marker_transparent.png"/></div>'+
				'<div class="div_parrafo"><p class="parrafo">'+reducirTitulo(data[j].TITULO)+'</p></div>'+
				'<div title="Consultar noticia" class="div_img_de">'+
					'<img alt="Consultar noticia" onclick="goRight('+index+','+j+');" class="img_derecha" src="http://localhost/TFG/images/view_transparent.png"/>'+
				'</div>'+
			     '</div>'+
		    	'</div>';//cierre div row
		
	}
	document.getElementById("content_d2b1").innerHTML += div_news;
}



//FUNCION PARA MOVERSE DEL BOX1 AL BOX2
function goRight(index, i){
	var data   = globaljson[index][i];
	

	//Limpiar div y borrar su contenido	
	document.getElementById("content_d2b2").innerHTML ='';


	/* Añadir los datos de una noticia en concreto al box2 */
	var div_info_new = '';
	div_info_new += '<div class="row padding_row">'+
		    		'<div class="col-xs-12">'+
					'<h5>'+data.UBICACION+' > '+data.RSS+'</h5>'+
				'</div>'+
		    	'</div>'+
			'<div class="row padding_row">'+
				'<div class="col-xs-12">'+
					'<h5>'+data.FECHA+'</h5>'+
				'</div>'+
		    	'</div>'+
			'<div class="row padding_row">'+
		    		'<div class="col-xs-12">'+
					'<h3>Periódico</h3>'+
				'</div>'+
		    	'</div>'+
			'<div class="row padding_row">'+
		    		'<div class="col-xs-12">'+
					'<p>'+data.PERIODICO+'</p>'+
				'</div>'+
		    	'</div>'+
			'<div class="row padding_row">'+
		    		'<div class="col-xs-12">'+
					'<h3>Título</h3>'+
				'</div>'+
		    	'</div>'+
			'<div class="row padding_row">'+
		    		'<div class="col-xs-12 justify_text">'+
					'<p>'+data.TITULO+'</p>'+
				'</div>'+
		    	'</div>'+
			'<div class="row padding_row">'+
		    		'<div class="col-xs-12">'+
					'<h3>Descripción</h3>'+
				'</div>'+
		    	'</div>'+
			'<div class="row padding_row">'+
		    		'<div class="col-xs-12 justify_text">'+
					'<p>'+data.DESCRIPCION+'</p>'+
				'</div>'+
		    	'</div>'+
			'<div class="row padding_row">'+
		    		'<div class="col-xs-12">'+
					'<h3>Link</h3>'+
				'</div>'+
		    	'</div>'+
			'<div class=" row padding_row">'+
		    		'<div class="col-xs-12 justify_text">'+
					'<a href="'+data.LINK+'">'+data.LINK+'</a>'+
				'</div>'+
		    	'</div>'+	
			'<div style="margin-top:50px; margin-bottom:50px;" class="row padding_row horizontal_center">'+
		    		'<div class="col-xs-12">'+
					'<img onclick="show_noticias('+index+');" class="img_derecha" src="http://localhost/TFG/images/flecha.png"/>'+
				'</div>'+
		    	'</div>';
			
	document.getElementById("content_d2b2").innerHTML += div_info_new;


	/* Desplazarse al box2 */
	var initalLeftMargin = $( ".column-left" ).css('margin-left').replace("px", "")*1;
	var widthDiv = document.getElementById('div_box').offsetWidth;

	var newLeftMargin = (initalLeftMargin - widthDiv); 
	$( ".column-left" ).animate({marginLeft: newLeftMargin}, 500);
}



//FUNCION PARA MOVERSE DEL BOX2 AL BOX1	
function goLeft(index){ 	

	//Desplazarnos al box1
	var initalLeftMargin = $( ".column-left" ).css('margin-left').replace("px", "")*1;
	var widthDiv2 = document.getElementById('div_box2').offsetWidth;

	var newLeftMargin = (initalLeftMargin + widthDiv2); 
	$( ".column-left" ).animate({marginLeft: newLeftMargin}, 500);
}



//FUNCION PARA MOSTRAR EL DIV DE OPCIONES DE LOS BOX
var openDiv;
function toggleDiv(divID) {

	var mycontainerWidth = document.getElementById('mycontainer').offsetWidth;
	document.getElementById('option_box_div').style.marginLeft = (mycontainerWidth-62)+'px';

	//fade div
	$("#" + divID).fadeToggle(400, function() {
		openDiv = $(this).is(':visible') ? divID : null;
	});
}



//FUNCION PARA ACORTAR LA LONGITUD DE UNA CADENA
function reducirTitulo(str){
	var leng = 30;
	
	str = str.slice(0, leng);
	str = str + " ...";
	return str;
}



//FUNCION PARA COMPROBAR SI UN BOX ESTA VISIBLE O SI ESTA OCULTO TRAS EL MAPA
function boxVisible(id) {
	var visible = true;
	var box = $((document.getElementById(id)));
	var map = $((document.getElementById("map")));

	var mapLeft = map.offset().left;
	var mapRight = ($(window).width() - (mapLeft + map.outerWidth())); //calcular margin-right

	var boxLeft = box.offset().left;
	var boxRight = ($(window).width() - (boxLeft + box.outerWidth())); //calcular margin-right
	
	//console.log(boxLeft + " : " + mapLeft);
	//console.log(boxRight + " : " + mapRight);
	
	if (boxLeft >= mapLeft || boxRight <= mapRight) {		   //Si el box esta "dentro/detras" del map
		visible = false;
	}
	return visible;
}




function cerrarLeyenda(){

	//Poner el div del mapa a pantalla completa
	$("#map").removeClass("col-xs-9");
	$("#map").addClass("col-xs-12");
	
	//Ocultar option box div
	toggleDiv('option_box_div');

	//Redimensionar y centrar mapa
	var center = map.getCenter();
	google.maps.event.trigger(map, "resize");
	map.setCenter(center); 


	//Ocultar container de boxs y mostrar divleyenda
	$("#leyenda_div").show();
	$("#mycontainer").hide();
	
}



function abrirLeyenda(){

	//Poner el div del mapa en su posicion inicial
	$("#map").removeClass("col-xs-12");
	$("#map").addClass("col-xs-9");
	
	//Ocultar option box div
	//toggleDiv('option_box_div');

	//Redimensionar y centrar mapa
	var center = map.getCenter();
	google.maps.event.trigger(map, "resize");
	map.setCenter(center); 


	//Ocultar container de boxs y mostrar divleyenda
	$("#leyenda_div").hide();
	$("#mycontainer").show();
	
}












/*Llamadas a funciones*/

//Cuando se haga clic en cualquier parte del documento que no sea el option box se llama a la funcion toogle para cerrar el option box si esta abierto
$(document).click(function(e) {
	if (!$(e.target).closest('#'+openDiv).length) {
		toggleDiv(openDiv);
	}
});



//Recalcular margen en option box div cada vez que se redimencione la pantalla
$(window).resize(function(){

   	var mycontainerWidth = document.getElementById('mycontainer').offsetWidth;
	document.getElementById('option_box_div').style.marginLeft = (mycontainerWidth-62)+'px';
})



