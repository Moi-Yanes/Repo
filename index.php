<?php 
	/* PHP ERRORS */
	ini_set('display_errors','On');

	//Inclusion de ficheros php
	require_once 'php/include.php';


?>

<!DOCTYPE html>
<html>
	<head>
		<title>Simple Map</title>
		<meta name="viewport" content="initial-scale=1.0">
		<meta charset="utf-8">
		
		<style>
			/* Always set the map height explicitly to define the size of the div
			* element that contains the map. */
			#map {
				height: 500px;
				width: 500px;
			}
			/* Optional: Makes the sample page fill the window. */
			html, body {
				height: 100%;
				margin: 0;
				padding: 0;
			}
		</style>
	</head>
	<body>


		<div class="container">    
			<table class="table">
				<thead>
					<tr>
						<th>RSS</th>
						<th>PERIODICO</th>
						<th>TITULAR</th>
						<th>LINK</th>
						<th>FECHA</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						//count_all_noticias();
						//remove_coleccion('noticia');
						$arr = get_all_noticias('noticia', 10);
		
						if ( !empty($arr) ){
			
							foreach($arr as $r){
								echo '<tr>';
								echo '<td>'.	$r['RSS'].		'</td>';
								echo '<td>'.	$r['PERIODICO'].	'</td>';
								echo '<td>'.	$r['TITULAR'].		'</td>';
								echo '<td>'.	$r['LINK'].		'</td>';
								echo '<td>'.	$r['FECHA'].		'</td>';
								echo '</tr>';						
							}
						}
						else{
							echo "<td> Vacioo</td>";
						}	
					?>
				</tbody>
			</table>
		</div>

	
		<div id="map"></div>
		
		<script>
			var map;
			function initMap() {
				map = new google.maps.Map(document.getElementById('map'), {
				  center: {lat: 28.5534710818/*28.463938*/, lng: -16.141977772/*-16.262598*/}, //(Y,X)
				  zoom: 15
				});
			}
		</script>

		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCBK16iT0lz1tODBvJqToq_ret28wpSPTk&callback=initMap" async defer></script>
	</body>
</html>
