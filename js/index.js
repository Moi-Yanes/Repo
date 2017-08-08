/*variables globales*/
var globaljson; var map; var allMarkers = []; var infoWin;
var ubicacion_actual;

//INICIALIZAR MAPA CREANDO EL ARRAY DE LOCALIZACIONES
function initMap() {

	map = new google.maps.Map(document.getElementById('map'), {
	  center: {lat: 28.48, lng: -16.32}, //(Y,X)la laguna
	  zoom: 11,
	  mapTypeControl: false
	});


	 infoWin = new google.maps.InfoWindow();

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

			locations.push({'lat' : parseFloat(data[0].LATITUD), 'lng': parseFloat(data[0].LONGITUD), 'info':  contentString, 'ubicacion':  data[0].UBICACION});
			globaljson = json;
		} 
		
		addMarkers(locations, map, infoWin);
	});
}



//AÑADIR MARCADORES AGRUPADOS EN CLUSTERES
function addMarkers(locations, map, infoWin){

	
	// Añadir tantos marcadores como posiciones tenga el array locations (funcion map de arrays)
	var markers = locations.map(function(location, i) {
		var marker = new google.maps.Marker({
			position: location,
			ubicacion: location.ubicacion
		});
		google.maps.event.addListener(marker, 'click', function(evt) {
			infoWin.setContent(location.info);
			infoWin.open(map, marker);
		})

		allMarkers.push(marker);
		return marker;
	});

	// markerCluster.setMarkers(markers);
	// Add a marker clusterer to manage the markers.
	var markerCluster = new MarkerClusterer(map, markers, {
		imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'
	});
}




function show_noticias(index){
	var data   	 = globaljson[index];	//contiene el json en la posicion[index]
	ubicacion_actual = data; 		//para saber que noticias tenemos en ventana en cada momento


	//cerrar todos los infowindown q hayan abiertos, en nuestro caso sera unicamente uno
	infoWin.close();


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
		div_news += '<div class="col-md-12 horizontal_center calc_tam">'+
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


function pad(input, length, padding) { 
	var str = input + "";
	return (length <= str.length) ? str : pad(str+padding, length, padding);
}


//FUNCION PARA ACORTAR LA LONGITUD DE UNA CADENA
function reducirTitulo(str){
	var leng = 37;
	var strnew;
	str = str.trim();
	//var leng = document.getElementsByClassName('calc_tam')[0].offsetWidth;
	//console.log(leng);

	if(str.length < leng){
		strnew = pad(str,(leng-4)," ");
		strnew = strnew + " ...";
	}
	else if(str.length > leng){
		strnew = str.substr(0,(leng-4));
		strnew = strnew + " ...";
	}
	else if(str.length == leng){
		strnew = str;
	}
	console.log(strnew + " Tamaño: " +strnew.length);
	return strnew;
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

	//Mover busqueda box
	document.getElementById('box_busqueda').style.marginLeft = '180px';
	document.getElementById('box_busqueda').style.marginTop = '25px';

	//Ocultar container de boxs y mostrar divleyenda
	$("#leyenda_div").show();
	$("#mycontainer").hide();

	//Redimensionar y centrar mapa
	map.setZoom(12);
	google.maps.event.trigger(map, "resize");
	map.setCenter({lat: 28.48, lng: -16.32}); 
}



function abrirLeyenda(){

	//Poner el div del mapa en su posicion inicial
	$("#map").removeClass("col-xs-12");
	$("#map").addClass("col-xs-9");

	//Mover busqueda box
	document.getElementById('box_busqueda').style.marginLeft = '25%';
	document.getElementById('box_busqueda').style.marginTop = '20px';

	//Ocultar container de boxs y mostrar divleyenda
	$("#leyenda_div").hide();
	$("#mycontainer").show();

	//Redimensionar y centrar mapa
	map.setZoom(11);
	//var center = map.getCenter();
	google.maps.event.trigger(map, "resize");
	map.setCenter({lat: 28.48, lng: -16.32}); 
}



function centrarMapa(ubicacion){

	//ocultar div de resultados	
	$('#busqueda_result').hide('slow');	 

	//buscar posiciones y centrar mapa
	for (var i = 0; i < globaljson.length; i++) { 					
		var data= globaljson[i];
		var str = data[0].UBICACION;
		
		if( str == ubicacion ){
			map.setCenter(new google.maps.LatLng(data[0].LATITUD,data[0].LONGITUD));
			map.setZoom(20);
			/*for (var i = 0; i < allMarkers.length; i++) {
				if( allMarkers[i].ubicacion == data[0].UBICACION  ){
					new google.maps.Circle({
						strokeColor: '#FF0000',
						strokeOpacity: 0.8,
						strokeWeight: 2,
						fillColor: '#FF0000',
						fillOpacity: 0.35,
						map: map,
						center: {lat: allMarkers[i].getPosition().lat(), lng: allMarkers[i].getPosition().lng()},
						radius: Math.sqrt(0.01) * 100
					});
				}
			}*/	
		}
	}  
}


function descargarNews(){

	//ocultar option box div, puesto que si se llama a esta funcion es pq ya se ha pulsado sobre una opcion
	toggleDiv('option_box_div');

	//descargar fichero
	if(ubicacion_actual == undefined){
		alert("Seleccione una ubicación primero en el mapa");
	}
	else{
		$.ajax({
			url     : 'http://localhost/TFG/php/downloadfile.php?file=downloadfile.json&opcion=1',
			method  : 'post',
			data	: {json : JSON.stringify(ubicacion_actual)},			   //pasar el array de objetos de las noticias a php y que este rellene el fichero json
			success:function(data, textStatus, jqXHR){
				console.log('AJAX SUCCESS');
				window.location.href = "http://localhost/TFG/php/downloadfile.php?file=downloadfile.json&opcion=1";//para que se lleve a cabo la descarga
			}, 
			complete : function(data, textStatus, jqXHR){
				console.log('AJAX COMPLETE');
			}
		});
	}
}

function descargarNewsTxt(){

	//ocultar option box div, puesto que si se llama a esta funcion es pq ya se ha pulsado sobre una opcion
	toggleDiv('option_box_div');

	//descargar fichero
	if(ubicacion_actual == undefined){
		alert("Seleccione una ubicación primero en el mapa");
	}
	else{
		$.ajax({
			url     : 'http://localhost/TFG/php/downloadfile.php?file=downloadfile.txt&opcion=2',
			method  : 'post',
			data	: {txt : ubicacion_actual},		//pasar el array de objetos de las noticias a php y que este rellene el fichero txt
			success:function(data, textStatus, jqXHR){
				console.log('AJAX SUCCESS');
				window.location.href = "http://localhost/TFG/php/downloadfile.php?file=downloadfile.txt&opcion=2";//para que se lleve a cabo la descarga
			}, 
			complete : function(data, textStatus, jqXHR){
				console.log('AJAX COMPLETE');
			}
		});
	}
}



function descargarNewsPdf(){

	//ocultar option box div, puesto que si se llama a esta funcion es pq ya se ha pulsado sobre una opcion
	toggleDiv('option_box_div');

	//descargar fichero
	if(ubicacion_actual == undefined){
		alert("Seleccione una ubicación primero en el mapa");
	}
	else{
		$.ajax({
			url     : 'http://localhost/TFG/php/txttopdf.php',
			method  : 'post',
			data	: {pdf : ubicacion_actual},		//pasar el array de objetos de las noticias a php y que este rellene el fichero txt
			success:function(data, textStatus, jqXHR){
				console.log('AJAX SUCCESS');
				window.open("http://localhost/TFG/dump/downloadfile.pdf","_blank");
			}, 
			complete : function(data, textStatus, jqXHR){
				console.log('AJAX COMPLETE');
			}
		});
	}
}



//Listar las ubicaciones posibles a medida que se introducen letras en el campo de busqueda
function buscarUbicacion(){
	var consulta;
                                                                          
	//hacemos focus al campo de búsqueda
	$('#busqueda').click( function (){
		$("#busqueda").focus();
	});	

                                                                                                    
	//comprobamos si se pulsa una tecla
	$("#busqueda").keyup(function(e){
                  
		//limpiar div de resultados	
            	document.getElementById('busqueda_result').innerHTML = "";

		//obtenemos el texto introducido en el campo de búsqueda
		consulta 	= $("#busqueda").val().toUpperCase();
		var re 		= new RegExp(consulta, 'g');
                var array 	= new Array();
                             
                         
              	//hacemos la búsqueda                                                                
	  	for (var i = 0; i < globaljson.length; i++) { 					
			var data= globaljson[i];
			var str = data[0].UBICACION;
		
			if( str.match(re) ){
				array.push(str);	
			}
		}  

		//recorremos el array y creamos un div que contenga todos los resultados
		var div_new = '';
		for (var i = 0; i < array.length; i++) {
			div_new += '<div class="parrafo_result vertical_center"><p onclick="centrarMapa(`'+array[i]+'`);" style="margin-top: 10px!important;">'+array[i]+'</p></div>';
		}
		
		//insertamos div 
		document.getElementById('busqueda_result').innerHTML += div_new;
		$('#busqueda_result').appendTo('#box_busqueda').show('slow');	 
	}); 

}




/*Llamadas a funciones*/

//Cuando se haga clic en cualquier parte del documento 
$(document).click(function(e) {

	//Se llama a la funcion toogle para cerrar el option box si esta abierto
	if (!$(e.target).closest('#'+openDiv).length) {
		toggleDiv(openDiv);
	}

	//si el div de resultados de busqueda esta abierto, cerrarlo
	if(!$(e.target).closest('#busqueda_result').length){
		$('#busqueda_result').hide('slow');
		document.getElementById('busqueda').value = "";
	}
});




$(window).resize(function(){

	//Recalcular margen en option box div cada vez que se redimencione la pantalla
   	var mycontainerWidth = document.getElementById('mycontainer').offsetWidth;
	document.getElementById('option_box_div').style.marginLeft = (mycontainerWidth-62)+'px';


	//centrar map
	//var center = map.getCenter();
	google.maps.event.trigger(map, "resize");
	map.setCenter({lat: 28.48, lng: -16.32});
})





$(document).ready(function(){
        buscarUbicacion();                        
}); 








