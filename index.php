<?php 
	/* PHP ERRORS */
	ini_set('display_errors','On');

	//Inclusion de ficheros php
	require_once 'php/include.php';

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Map</title>
		<meta name="viewport" content="initial-scale=1.0">
		<meta charset="utf-8">

		<link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link type="text/css" rel="stylesheet" href="<?php echo SERVER_PATH; ?>bootstrap-3.3.7-dist/css/style.css">
		
	</head>
	<body>
		<div class="container-fluid">
			<div id ="menu" class="row">
				<div class="col-md-12">
					hola header
				</div>
			</div>
			<div class="row fill-height">
				<div class="mycontainer col-md-3">
					<div class="column-left row">
						<div id="div_box" class="box">
							<div id="d1b" class="container-fluid">
								<div class="row vertical_center">
									<div id="title_d1b" class="col-md-9">
										<h2>Noticias</h2>
									</div>
									<div id="btn_d1b" class="col-md-3">
										<div class="div_img"> 
											<input onclick="toggleDiv('option_box_div');" 
												class="open_option_div" 
												type="image" 
												src="<?php echo SERVER_PATH; ?>images/puntos.png" 
											/>
										</div>	
									</div>
								</div>
							</div>
							<div id="d2b" class="container">
								<div class="row">
									<div id="content_d2b1" class="col-md-12">
											
									</div>
								</div>
							</div>	
							<div id="d3b" class="container-fluid">
								<div class="row vertical_center horizontal_center footer_box">
									<div  class="col-md-12">
										<p>Santa Cruz de Tenerife y San Cristobal de La Laguna</p>
									</div>
								</div>
							</div>
						</div>

						<div id="div_box2" class="box box2">
							<div id="d1b2" class="container-fluid">
								<div class="row vertical_center">
									<div id="title_d1b2" class="col-md-9">
										<h2 >Noticias</h2>
									</div>
									<div id="btn_d1b2" class="col-md-3">
										<div class="div_img"> 
											<input onclick="toggleDiv('option_box_div');" 
												class="open_option_div" 
												type="image" 
												src="<?php echo SERVER_PATH; ?>images/puntos.png" 
											/>
										</div>
									</div>
								</div>
							</div>
							<div id="d2b2" class="container">
								<div class="row">
									<div id="content_d2b2" class="col-md-12 ">
										<input class="backBtn" type="button" value="Back">
									</div>
								</div>
							</div>
							<div id="d3b2" class="container-fluid">
								<div class="row vertical_center horizontal_center footer_box">
									<div class="col-md-12">
										<p>Santa Cruz de Tenerife y San Cristobal de La Laguna</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>


				<div id="map" class="col-md-9"></div>
			</div>
			<div id="footer" class="row">
				<div class="col-md-12">
					hola footer
				</div>
			</div>
			
		</div>

		<!-- div de opciones de los box da igual donde se ponga(position absolute)-->
		<div id="option_box_div" class=""> 
			<div id="option_box_div_2" class=""> 
				bla
			</div>
		</div>

		
		

		<script src="<?php echo SERVER_PATH; ?>js/jquery_easing/jquery.js"></script>
		<script src="<?php echo SERVER_PATH; ?>js/jquery_easing/easing.js"></script>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

		<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCBK16iT0lz1tODBvJqToq_ret28wpSPTk&callback=initMap" async defer></script>

		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

		<script src="<?php echo SERVER_PATH; ?>js/index.js"></script>
	</body>
</html>
