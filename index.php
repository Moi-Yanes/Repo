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
			#menu{
				height: 10%;
				width: 100%;
				clear: both;
				/*background-color: red;*/
			}
			#map {
				height: 90%;
				width: 75%;
				float: left;
			}
			#column-left{
				height: 90%;
				width: 25%;
				float: left;
				/*background-color: blue;*/
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

		<!--
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
		-->
		<div id="menu"></div>
		<div id="column-left"></div>
		<div id="map"></div>
		



		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
		<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCBK16iT0lz1tODBvJqToq_ret28wpSPTk&callback=initMap" async defer></script>
		<script src="<?php echo SERVER_PATH; ?>js/index.js"></script>
	</body>
</html>
