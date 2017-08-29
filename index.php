<?php 
	/* PHP ERRORS */
	ini_set('display_errors','On');

	//Inclusion de ficheros php
	require_once 'php/include.php';

?>


<!DOCTYPE html>
<html>
	<head>
		<title>Sistema de Ubicacion Geografica de RSS a traves de PLN</title>
		<meta name="viewport" content="initial-scale=1.0">
		<meta charset="utf-8">

		<link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<link type="text/css" rel="stylesheet" href="<?php echo SERVER_PATH_IAAS; ?>bootstrap-3.3.7-dist/css/style.css">
		<link type="text/css" rel="stylesheet" href='//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>
	</head>
	<body>
		<div id="content_all" class="container-fluid">

	
			<!--<div id ="menu" class="row">
				<div class="col-xs-12">
					hola header
				</div>
			</div>-->

			<!-- div de busqueda -->
			<div id="box_busqueda" class="container-fluid">
				<div id="busqueda_div_input" class="row input-group stylish-input-group">
					<input id="busqueda" type="text" class="form-control" placeholder="Busca una ubicacion concreta aquí"/>
				</div>
				<div id="busqueda_result" class="row">
				</div>
			</div>

			<!-- CONTENIDO DE LA PAGINA -->
			<div class="row fill-height">
				<div id="mycontainer" class="mycontainer col-xs-12 col-sm-6 col-lg-4"><!--col-xs-12 col-sm-6 col-lg-4-->
					<div id="column-left" class="column-left row">
						<div id="div_box" class="box">
							<div id="d1b" class="container-fluid">
								<div class="row vertical_center">
									<div id="title_d1b" class="col-xs-9">
										<h2>Noticias</h2>
									</div>
									<div id="btn_d1b" class="col-xs-3">
										<div class="div_img"> 
											<input onclick="toggleDiv('option_box_div');" 
												class="open_option_div" 
												type="image" 
												src="<?php echo SERVER_PATH_IAAS; ?>images/puntos.png" 
											/>
										</div>	
									</div>
								</div>
							</div>
							<div id="d2b" class="container">
								<div id="d2b_divrow" class="row vertical_center horizontal_center">
									<div id="content_d2b1" class="col-xs-12">
										<p>Selecciona una ubicacion en el mapa</p>
									</div>
								</div>
							</div>	
							<div id="d3b" class="container-fluid">
								<div class="row vertical_center horizontal_center footer_box">
									<div  class="col-xs-12">
										<p>Santa Cruz de Tenerife y San Cristobal de La Laguna</p>
									</div>
								</div>
							</div>
						</div>

						<div id="div_box2" class="box box2">
							<div id="d1b2" class="container-fluid">
								<div class="row vertical_center">
									<div id="title_d1b2" class="col-xs-9">
										<h2 >Noticias</h2>
									</div>
									<div id="btn_d1b2" class="col-xs-3">
										<div class="div_img"> 
											<input onclick="toggleDiv('option_box_div');" 
												class="open_option_div" 
												type="image" 
												src="<?php echo SERVER_PATH_IAAS; ?>images/puntos.png" 
											/>
										</div>
									</div>
								</div>
							</div>
							<div id="d2b2" class="container">
								<div class="row">
									<div id="content_d2b2" class="col-xs-12 ">

									</div>
								</div>
							</div>
							<div id="d3b2" class="container-fluid">
								<div class="row vertical_center horizontal_center footer_box">
									<div class="col-xs-12">
										<p>Santa Cruz de Tenerife y San Cristobal de La Laguna</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>


				<div id="map" class="col-xs-12 col-sm-6 col-lg-8"></div><!--col-xs-12 col-sm-6 col-lg-8 -->
			</div>
			

			<!-- FOOTER -->
			<div class="row">
				<footer class="nb-footer">
					<div class="container">
						<div class="row">
							<div class="col-xs-12">
								<div class="about">
									<img src="images/logo.png" class="img-responsive center-block" alt="">
									<p>
										Trabajo de Fin de Grado de Ingeniería Informática sobre Big Data.<br>
										El trabajo consiste en una pagina web que sirve de clasificador de noticias. Las noticias a clasificar
										corresponden a las zonas de Santa Cruz de Tenerife y San Cristobal de La Laguna.
									</p>

									<div class="social-media">
										<ul class="list-inline">
											<li><a id="logo-facebook" href="#" title=""><i class="fa fa-facebook"></i></a></li>
											<li><a id="logo-github" href="#" title=""><i class="fa fa-github"></i></a></li>
											<li><a id="logo-google-plus" href="#" title=""><i class="fa fa-google-plus"></i></a></li>
											<li><a id="logo-linkedin" href="#" title=""><i class="fa fa-linkedin"></i></a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
					<section class="copyright">
						<div class="container">
							<div class="row">
								<div class="col-xs-12">
									<p>Copyright © 2017. Design by Moisés Yanes</p>
								</div>
								<!--<div class="col-xs-6"></div>-->
							</div>
						</div>
					</section>
				</footer>
			</div>
		</div>

		


		<!-- div de opciones de los box da igual donde se ponga(position absolute)-->
		<div id="option_box_div" class="container"> 
			<div id="option_box_div_2" class="row"> 
				<div style="" class="list-group">
					<a href="#" onclick="cerrarLeyenda();" class="list-group-item active">Ocultar leyenda del mapa</a>
					<a href="#" onclick="descargarNews();" class="list-group-item">Descargar noticias de esta ubicación en json</a>
					<a href="#" onclick="descargarNewsTxt();" class="list-group-item">Descargar noticias de esta ubicación en txt</a>
					<a href="#" onclick="descargarNewsPdf();" class="list-group-item">Descargar noticias de esta ubicación en PdF</a>
					<!--<a href="#" onclick="addFavoritos();" class="list-group-item">Añadir ubicación a favoritos</a>-->
				</div>
			</div>
		</div>


		<!-- div de la leyenda-->
		<div id="leyenda_div" class=""> 
			<div id="leyenda_div2" class=""> 
				<div id="leyenda_div3" class=""> 
					<span onclick="abrirLeyenda();" class="">Leyenda del mapa</span>
				</div>
			</div>
		</div>

		
		

		<script src="<?php echo SERVER_PATH_IAAS; ?>js/jquery_easing/jquery.js"></script>
		<script src="<?php echo SERVER_PATH_IAAS; ?>js/jquery_easing/easing.js"></script>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

		<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCBK16iT0lz1tODBvJqToq_ret28wpSPTk&callback=initMap" async defer></script>
		<!--<script src="<?php echo SERVER_PATH_IAAS; ?>js/maplabel-compiled.js"></script>-->
		
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

		<script src="<?php echo SERVER_PATH_IAAS; ?>js/index.js"></script>
	</body>
</html>
