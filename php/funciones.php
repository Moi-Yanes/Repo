<?php 	
	/* PHP ERRORS */
	ini_set('display_errors','On');

	/* PHP CONFIG */
	require_once 'include.php';



	// Calcular la ubicacion de la noticia e insertar en la base de datos
	function insert_ubicacion(){

	    //OBTENER LAS UBICACIONES Y GUARDARLAS EN ESE CAMPO
		$mongo    = new MongoDB\Driver\Manager(Config::MONGODB);
		$query    = new MongoDB\Driver\Query([]);
		$bulk 	  = new MongoDB\Driver\BulkWrite;

		//Obtener lugares
		$rows 	  = $mongo->executeQuery('NoticiasDB.coordenadas', $query); 
		$barrios  = $rows->toArray();
		$barr 	  = array();/*array para guardar los barrios*/

		//Obtener noticias
		$result   = $mongo->executeQuery('NoticiasDB.noticia', $query); 
		$noticias = $result->toArray();

		if ( !empty($barrios) ){
			
			//Guardar en un array los nombres de los lugares, su latitud y su longitud
			foreach($barrios as $r){
				array_push($barr, [$r->LUGAR,$r->LATITUD,$r->LONGITUD]);	
			}

			
			//Recorrer cada una de las noticas
			if ( !empty($noticias) ){
				foreach($noticias as $n){
					$encontrado = false;
					$ubicacion  = "";      /* nombre de la ubicacion */
					$latlong    = array(); /* latitud y longitud de la ubicacion */
					$GLOBALS['noticias_totales'] ++;

					//Eliminar caracteres innecesarios en las comparaciones
					$titulo      = delete_caracteres_sobrantes($n->titular);
					$descripcion = delete_caracteres_sobrantes($n->descripcion);


					//Copiar arrays
					$c_titulo      = explode(' ',preg_replace("/([^A-Za-z0-9[:space:]áéíóúÁÉÍÓÚñÑ-])/",'',$n->titular));
					$c_descripcion = explode(' ',preg_replace("/([^A-Za-z0-9[:space:]áéíóúÁÉÍÓÚñÑ-])/",'',$n->descripcion));

	
					//BUSCAR EN EL TITULO alguna coincidencia con los lugares
					if($encontrado == false){
						foreach($titulo as $t){	
							foreach($barr as $p){						 
								if (in_array($t, $p)) { 			//Comparacion de palabra exacta (en mayuscula)
									$ubicacion = $p[0];
									$latlong = [$p[1],$p[2]];		//latlong[0] -- latitud  | latlong[1] -- longitud
									$encontrado = true;
									break;
								}
							}
							if($encontrado==true)
								break;	
						}
						if($encontrado == false){ 
							foreach($barr as $p){
								if(preg_grep("/\b".$p[0]."\b/", $titulo) ){ 	//Comparacion parcial (en mayuscula)
									$ubicacion = $p[0];
									$latlong = [$p[1],$p[2]];
									$encontrado = true;
									break;
								}
							}
						}
					}

					//BUSCAR EN LA DESCRIPCION alguna coincidencia
					if($encontrado == false){
						foreach($descripcion as $d){
							foreach($barr as $p){	
								if (in_array($d, $p)) { 			//Comparacion de palabra exacta (en mayuscula)
									$ubicacion = $p[0];
									$latlong = [$p[1],$p[2]];
									$encontrado = true;
									break;
								}
							}
							if($encontrado==true)
								break;
						}
						if($encontrado == false){ 
							foreach($barr as $p){
								if(preg_grep("/\b".$p[0]."\b/", $descripcion) ){ //Comparacion parcial (en mayuscula)
									$ubicacion = $p[0];
									$latlong = [$p[1],$p[2]];
									$encontrado = true;
									break;
								}
							}
						}
					}


					//Si no se ha encontrado ninguna palabra clave en las comparaciones previas buscamos la palabra capital por si aparece
					if($encontrado == false){	
						if(in_array("capital", $c_titulo) || in_array("capital", $c_descripcion)){
							$ubicacion = "SANTA CRUZ DE TENERIFE";
							$latlong = [28.463938,-16.262598];
							$encontrado = true;
						}	
					}							


					//GUARDAR UBICACION EN LA BBDD CON SUS COORDENADAS	
					if($encontrado == true){
						$GLOBALS['noticias_ubicadas'] ++;
						$bulk->update(
						    ['_id' => new MongoDB\BSON\ObjectID($n->_id)],
						    ['$set' => ['ubicacion' => $ubicacion, 'latitud' => $latlong[0], 'longitud' => $latlong[1] ]],
						    ['multi' => false, 'upsert' => false]
						);	
						//echo "La ubicacion es: ".$ubicacion." Latitud: ".$latlong[0]." longitud: ".$latlong[1].'<br>';
					}
					elseif($encontrado == false){					
						$bulk->update(
						    ['_id' => new MongoDB\BSON\ObjectID($n->_id)],
						    ['$set' => ['ubicacion' => "No encontrada", 'latitud' => 0, 'longitud' => 0 ]],
						    ['multi' => false, 'upsert' => false]
						);
					}
				}
				$mongo->executeBulkWrite('NoticiasDB.noticia', $bulk); //Actualizar el campo ubicacion de la coleccion de noticias de la base de datos
			}else{
				echo "No hay noticias!";			
			}
		}
		else{
			echo "No hay lugares!".'<br>';
		}
	}

	//Ficheros csv a insertar
	/*insert_coordenadas_bd("barrios.csv");
	insert_coordenadas_bd("esculturas.csv");
	insert_coordenadas_bd("farmacias.csv");
	insert_coordenadas_bd("hoteles.csv");
	insert_coordenadas_bd("instculturales.csv");
	insert_coordenadas_bd("instdeportivas.csv");
	insert_coordenadas_bd("instseguridad.csv");
	insert_coordenadas_bd("miradores.csv");
	insert_coordenadas_bd("parquesinfantiles.csv");
	
	insert_coordenadas_bd("administracionyserviciospublicos.csv");
	insert_coordenadas_bd("agricultura.csv");
	insert_coordenadas_bd("alimentacion.csv");
	insert_coordenadas_bd("hosteleriayrestauracion.csv");
	insert_coordenadas_bd("asociacionesciudadania.csv");
	insert_coordenadas_bd("comercio.csv");
	insert_coordenadas_bd("educacionycultura.csv");
	insert_coordenadas_bd("medicinaysalud.csv");
	insert_coordenadas_bd("carreteras.csv");
	//insert_coordenadas_bd("general.csv");
	insert_coordenadas_bd("distritos.csv");
	insert_coordenadas_bd("lugares.csv");
	*/
	
	//insert_ubicacion();



	//LEER E INSERTAR TODOS LOS FICHEROS DEL DIRECTORIO DUMP EN LA BASE DE DATOS (LUGARES DE SC DE TENERIFE)
	function insert_coordenadas_bd($archivo){
		
		$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
		$bulk = new MongoDB\Driver\BulkWrite;
		$registros = array();

		//Obtener en un array los nombres de todos los ficheros y que se ejecute sola la insercion
		/*
			$ficheros = scandir(Config::PATH."/dump/");
			foreach($ficheros as $archivo){
				$extension = explode(".",$archivo)[1];
				if ( $extension == "csv" )
					echo $archivo.'<br>';
			}
			var_dump($ficheros);
		*/


		//OBTENER DATOS DE CSV
		if (($fichero = fopen(Config::PATH."/dump/".$archivo, "r")) !== FALSE) {
			
			// Lee los nombres de los campos
			$nombres_campos = fgetcsv($fichero, 0, ",", "\"", "\"");
			$num_campos = count($nombres_campos);
			
			// Lee los registros
			while (($datos = fgetcsv($fichero, 0, ",", "\"", "\"")) !== FALSE) {
				// Crea un array asociativo con los nombres y valores de los campos
				for ($icampo = 0; $icampo < $num_campos; $icampo++) {
					$registro[$nombres_campos[$icampo]] = $datos[$icampo];
				}
				// Añade el registro leido al array de registros
				$registros[] = $registro;
			}
			fclose($fichero);


			for ($i = 0; $i < count($registros); $i++) {
					
				$lugar	= preg_replace("/[^A-Z0-9Ñ\-.]/", " ", strtoupper(quitar_tildes($registros[$i]['NOMBRE'])) );	//quitar tildes, pasar a mayuscula y extraños
				$lugar	= preg_replace("/\s+/", " ", $lugar);								//Eliminar dobles espacios
				$lugar	= trim($lugar); 										//Elimina espacios al principio y fin 

				$doc = array(
					'id'      	=> new MongoDB\BSON\ObjectID,     #Generate MongoID
					'LUGAR'		=> $lugar,
					//'DISTRITO'	=> $registros[$i]['DISTRITO'],
					'LATITUD'	=> $registros[$i]['GRAD_Y'],
					'LONGITUD'	=> $registros[$i]['GRAD_X']
				);
				$bulk->insert($doc);	
			}
			

			//Guardamos en la base de datos los lugares 		
			$result = $mongo->executeBulkWrite('NoticiasDB.coordenadas', $bulk); # 'NoticiasDB' es la base de datos y 'coordenadas' la collection.  
			printf("Se han insertado %d documentos en la base de datos\n", $result->getInsertedCount());
		} 
	}

	

	//CREAR FICHERO JSON SI AUN NO HA SIDO CREADO CON LA UBICACION y COORDENADAS DE CADA NOTICIA
	function create_json_marcadores(){
		
		$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
		$query = new MongoDB\Driver\Query([]);
		
		$rows = $mongo->executeQuery('NoticiasDB.noticia', $query); // $mongo contains the connection object to MongoDB
		$fila = $rows->toArray();
	
		$porcentajes = array();

		//Leer filas resultantes de la consulta
		if ( !empty($fila) ){
			
			$noticias_agrupadas = split_array($fila);//agrupar noticias por ubicacion
			$index 		    = 0;
			foreach($noticias_agrupadas as $r){
				$p_sucesos = 0;
				$p_sociedad = 0;
				$total_noticias = count($r);

				//calcular porcentajes
				foreach($r as $n){
					if($n['RSS'] == 'Sucesos'){
						$p_sucesos++;  }

					if($n['RSS'] == 'Sociedad'){
						$p_sociedad++; }
				}
			
				//introducir en array
				$p_su  = porcentaje($total_noticias, $p_sucesos, $redondear = 2);
				$p_so  = porcentaje($total_noticias, $p_sociedad, $redondear = 2);
				
				$porcentajes = array(
					'P_SUCESOS'	=> $p_su,
					'P_SOCIEDAD'	=> $p_so,
				);
				
				array_push($noticias_agrupadas[$index],$porcentajes);
				$index++;
			}

			
			//Crear json con barrios y coordenadas
			$nombre_archivo = Config::PATH.'/dump/coordenadas.json'; 
 
			if(!file_exists($nombre_archivo)){	
				$fp = fopen($nombre_archivo,"w+");
				fwrite($fp, json_encode($noticias_agrupadas));
				fclose($fp);	
			}else{
				$fp = fopen($nombre_archivo,"a+");
				fwrite($fp, json_encode($noticias_agrupadas));
				fclose($fp);
			}
		}
		else{
			echo "No existen noticias";
		}

	}
	//create_json_marcadores();



	function limpiar_csv($archivo){
		$mongo 		= new MongoDB\Driver\Manager(Config::MONGODB);
		$bulk 		= new MongoDB\Driver\BulkWrite;
		$doc[0] 	= array('NOMBRE','GRAD_Y','GRAD_X');
		$registros 	= array();

		/* Codigos postales de Santa Cruz de Tenerife */
		$cp_validos = array("38001","38002","38003","38004",
				    "38006","38007","38008","38009",
			            "38010","38107","38108","38110",
				    "38111","38120","38129","38130",
				    "38139","38140","38150","38160",
				    "38170","38180","38291","38294");
	
		//OBTENER DATOS DE CSV
		if (($fichero = fopen(Config::PATH."/dump/".$archivo, "r")) !== FALSE) {
			
			// Lee los nombres de los campos
			$nombres_campos = fgetcsv($fichero, 0, ",", "\"", "\"");
			$num_campos = count($nombres_campos);
			
			// Lee los registros
			while (($datos = fgetcsv($fichero, 0, ",", "\"", "\"")) !== FALSE) {
				for ($icampo = 0; $icampo < $num_campos; $icampo++) {
					$registro[$nombres_campos[$icampo]] = $datos[$icampo];
				}
				$registros[] = $registro;
			}
			fclose($fichero);


			if( in_array('cp', $nombres_campos) ){ //Si tiene campo cp el csv
				for ($i = 0; $i < count($registros); $i++) {
					if ( in_array($registros[$i]['cp'], $cp_validos) ) { //Si ese cp esta en sc añadimos el lugar sino no
						
						$lugar	= preg_replace("/[^A-Z0-9Ñ\-.]/", " ", strtoupper(quitar_tildes($registros[$i]['NOMBRE'])) );	//quitar tildes, pasar a mayuscula y extraños
						$lugar	= preg_replace("/\s+/", " ", $lugar);								//Eliminar dobles espacios
						$lugar	= trim($lugar); 										//Elimina espacios al principio y fin 
						$lugar	= str_replace("AYTO", "AYUNTAMIENTO", $lugar);
						$lugar  = str_replace("PZA.", "PLAZA", $lugar);
						$lugar  = str_replace("P.", "PARQUE", $lugar);
						$lugar  = str_replace("D.", "DON", $lugar);
						$lugar  = str_replace("B.", "BARRIO", $lugar);
						$lugar  = str_replace("B .", "BARRIO", $lugar);
						$lugar  = str_replace("A.A.V.V.", "ASOCIACION DE VECINOS", $lugar);
						$lugar  = str_replace("CTRA.", "CARRETERA", $lugar);
						$lugar  = str_replace("C.C.", "CENTRO COMERCIAL", $lugar);

						$doc[] = array(
							'NOMBRE'	=> $lugar,
							'GRAD_Y'	=> $registros[$i]['GRAD_Y'],
							'GRAD_X'	=> $registros[$i]['GRAD_X']
						);
					}	
				}

				//Crear csv
				if (($fichero = fopen(Config::PATH."/dump/".$archivo, "w")) !== FALSE) { 
					foreach($doc as $val) {
						fputcsv($fichero, $val);
					}
					fclose($fichero);
				}
			}
			else{
				for ($i = 0; $i < count($registros); $i++) {
	
					$lugar	= preg_replace("/[^A-Z0-9Ñ\-.]/", " ", strtoupper(quitar_tildes($registros[$i]['NOMBRE'])) );	//quitar tildes, pasar a mayuscula y extraños
					$lugar	= preg_replace("/\s+/", " ", $lugar);								//Eliminar dobles espacios
					$lugar	= trim($lugar); 										//Elimina espacios al principio y fin 
					$lugar	= str_replace("AYTO", "AYUNTAMIENTO", $lugar);
					$lugar  = str_replace("PZA.", "PLAZA", $lugar);
					$lugar  = str_replace("P.", "PARQUE", $lugar);
					$lugar  = str_replace("D.", "DON", $lugar);
					$lugar  = str_replace("B.", "BARRIO", $lugar);
					$lugar  = str_replace("B .", "BARRIO", $lugar);
					$lugar  = str_replace("A.A.V.V.", "ASOCIACION DE VECINOS", $lugar);
					$lugar  = str_replace("CTRA.", "CARRETERA", $lugar);
					$lugar  = str_replace("C.C.", "CENTRO COMERCIAL", $lugar);

					$doc[] = array(
						'NOMBRE'	=> $lugar,
						'GRAD_Y'	=> $registros[$i]['GRAD_Y'],
						'GRAD_X'	=> $registros[$i]['GRAD_X']
					);
				}

				//Crear csv
				if (($fichero = fopen(Config::PATH."/dump/".$archivo, "w")) !== FALSE) { 
					foreach($doc as $val) {
						fputcsv($fichero, $val);
					}
					fclose($fichero);
				}

			}
		} 		
	}

	/*limpiar_csv("administracionyserviciospublicos.csv");	//
	limpiar_csv("agricultura.csv");				//
	limpiar_csv("alimentacion.csv");			//
	limpiar_csv("asociacionesciudadania.csv");		//contiene iglesias
	limpiar_csv("comercio.csv");				//Contiene comercios
	limpiar_csv("educacionycultura.csv");			//Contiene colegios
	limpiar_csv("medicinaysalud.csv");
	limpiar_csv("hosteleriayrestauracion.csv");*/
	//limpiar_csv("esculturas.csv");
	//limpiar_csv("parquesinfantiles.csv");
	/*
	limpiar_csv("administracionyserviciospublicos.csv");
	limpiar_csv("agricultura.csv");
	limpiar_csv("alimentacion.csv");
	limpiar_csv("asociacionesciudadania.csv");
	limpiar_csv("comercio.csv");
	limpiar_csv("educacionycultura.csv");
	limpiar_csv("medicinaysalud.csv");
	limpiar_csv("hosteleriayrestauracion.csv");
	limpiar_csv("carreteras.csv");
	limpiar_csv("lugares.csv");
	*/


?>
