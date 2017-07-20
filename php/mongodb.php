<?php 
	/* PHP ERRORS */
	ini_set('display_errors','On');
	
	
	/* PHP CONFIG */
	require_once 'include.php';
	

	/* PHPExcel */
	set_include_path(implode(PATH_SEPARATOR, array(realpath(Config::PATH .'/phpexcel/Classes/'),get_include_path(),)));
	$insertados = 0; //Variable global para determinar numero de documentos insertados 
	$ubicadas = 0;
	$totales = 0;

	//Eliminar lso acentos de las palabras claves obtenidas para poder compararlos correctamente con los almacenados en la base de datos(no tienen tilde)
	function quitar_tildes($cadena) {
		$no_permitidas= array ( "á","é","í","ó","ú",
					"à","è","ì","ò","ù",
				      	"Á","É","Í","Ó","Ú","ñ","À",
					"Ã","Ì","Ò","Ù","Ã™","Ã ",
					"Ã¨","Ã¬","Ã²","Ã¹","ç",
					"Ç","Ã¢","ê","Ã®","Ã´","Ã»",
					"Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü",
				       	"Ã¶","Ã–","Ã¯","Ã¤","«",
				       	"Ò","Ã","Ã„","Ã‹");
		
		$permitidas= array("a","e","i","o","u",
				   "a","e","i","o","u",
				   "A","E","I","O","U",
				   "n","N","A","E","I",
				   "O","U","a","e","i",
				   "o","u","c","C","a",
				   "e","i","o","u","A",
				   "E","I","O","U","u",
				   "o","O","i","a","e",
				   "U","I","A","E");

		$texto = str_replace($no_permitidas, $permitidas ,$cadena);
		return $texto;
	}



	//Eliminar caracteres que no son necesarios analizar para buscar la ubicacion de la noticia, quedandonos con las palabras con mas probabilidad de ser la ubicacion
	function delete_caracteres_sobrantes($texto){
		
		/*
			1. //Quedarse con letras(incluidas vocales con/sin acentos), espacios, guiones y puntos
			2. //Quedarse solo con los nombres que empiecen por mayuscula compuestos o no
		*/
		$n_c = preg_replace("/([^A-Za-z0-9[:space:]áéíóúÁÉÍÓÚñ.-])/", " ", $texto);	
		$n_c = quitar_tildes($n_c); 			
		preg_match_all("/([A-Z]{1}[A-Za-z0-9]+)((\s|\-|)((\sde\s)|([0-9])|([A-Z]{1}[A-Za-z0-9]+)))*/", $n_c, $coinciden);
		
		$arr=array();
		foreach($coinciden as $r){ 
			foreach($r as $n){ 
				$n = explode(' ',$n);
				if(end($n) == ''){
					array_pop($n);  //quita "" del final
					array_pop($n);	//quita "de" del final
				}
				$n = implode(" ", $n);
				//echo $n.'<br>';	
				array_push($arr,strtoupper($n));//Meter en un array los resultados obtenidos despues de haber filtrado el texto mediante las expresiones regulares	       
			}
			break;	// Para quedarnos solo con los valores del primer subarray de coincidencias que son aquellos que cumplen completamente con la expresion regular dada
				// el resto de subarrays devueltos solo la cumplen parcialmente
		}
		
		return $arr;
	}



	// Calcular la ubicacion de la noticia e insertar en la base de datos
	function insert_ubicacion(){

	
	    //COMPROBAR SI EL CAMPO UBICACION EXISTE EN LA BBDD SINO CREARLO

	    //OBTENER LAS UBICACIONES Y GUARDARLAS EN ESE CAMPO
		$mongo    = new MongoDB\Driver\Manager(Config::MONGODB);
		$query    = new MongoDB\Driver\Query([]);
		$bulk 	  = new MongoDB\Driver\BulkWrite;
		$articulos = array('EL','LA','LAS','LOS');

		//Obtener barrios
		$rows 	  = $mongo->executeQuery('NoticiasDB.coordenadas', $query); 
		$barrios  = $rows->toArray();
		$barr 	  = array();/*array para guardar los barrios*/

		//Obtener noticias
		$result   = $mongo->executeQuery('NoticiasDB.noticia', $query); 
		$noticias = $result->toArray();


		if ( !empty($barrios) ){
			
			//Guardar en un array unicamente los nombres de los barrios
			foreach($barrios as $r){
				array_push($barr, $r->LUGAR);	
			}

			//Recorrer cada una de las noticas
			if ( !empty($noticias) ){
				foreach($noticias as $n){
					$encontrado = false;
					$ubicacion = "";


					//Eliminar caracteres innecesarios en las comparaciones
					$titulo      = delete_caracteres_sobrantes($n->titular);
					$descripcion = delete_caracteres_sobrantes($n->descripcion);


					//buscar en el titulo alguna coincidencia con los barrios
					if($encontrado == false){
						foreach($titulo as $t){		  //Decir Gregoria Alonso Jiménez no supone nada especial. Ni siquiera en Afur.						 
							if (in_array($t, $barr)) { //Comparacion de palabra exacta (en mayuscula)
								$ubicacion = $t;
								$encontrado = true;
								break;
							}
							if($encontrado == false){ //Comparacion parcial (en mayuscula)
								$t = explode(' ',$t);
								if(in_array($t[0], $articulos)){
									array_shift($t);  //quita la primera palabra
								}
								$t = implode(" ", $t);

								foreach($barr as $p){	
									if ($t == $p) { //Comparacion de palabra exacta (en mayuscula)
										$ubicacion = $t;
										$encontrado = true;
										break;
									}							
								}
							}
							if($encontrado==true)
								break;	
						}
					}

					//buscar en la descripcion alguna coincidencia
					if($encontrado == false){
						foreach($descripcion as $d){	
							if (in_array($d, $barr)) { //Comparacion de palabra exacta (en mayuscula)
								$ubicacion = $d;
								$encontrado = true;
								break;
							}
							if($encontrado == false){ //Comparacion parcial (en mayuscula)
								$d = explode(' ',$d);
								if(in_array($d[0], $articulos)){	
									array_shift($d);  		//quita la primera palabra
								}
								$d = implode(" ", $d);

								foreach($barr as $p){	
									if ($d == $p) { //Comparacion de palabra exacta (en mayuscula)
										$ubicacion = $d;
										$encontrado = true;
										break;
									}							
								}
							}
							/*if($encontrado == false){ //Comparacion parcial (en mayuscula)
								foreach($barr as $p){	
									if(preg_match("/\s".$d."\s/",$p)){
										if($d!='DE' && $d!='EL' && $d!='LA' && $d!='DEL' && $d!='LAS' && $d!='LOS'){
											
									    		$ubicacion = $d;
											$encontrado = true;
											break;	
										}							
									}							
								}
							}*/
							if($encontrado==true)
								break;	
						}
					}					


					//Guardar ubicacion en la bbdd	
					if($encontrado == true){
						$bulk->update(
						    ['_id' => new MongoDB\BSON\ObjectID($n->_id)],
						    ['$set' => ['ubicacion' => $ubicacion]],
						    ['multi' => false, 'upsert' => false]
						);	
						echo "La ubicacion es: ".$ubicacion.'<br>';
						$GLOBALS['ubicadas'] += 1;
					}
					elseif($encontrado == false){
						$bulk->update(
						    ['_id' => new MongoDB\BSON\ObjectID($n->_id)],
						    ['$set' => ['ubicacion' => "No encontrada"]],
						    ['multi' => false, 'upsert' => false]
						);
						//echo "La ubicacion es: No encontrada".'<br>';
					}
					$GLOBALS['totales'] += 1;
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
	//insert_coordenadas_bd("alimentacion.csv");
	//insert_coordenadas_bd("hosteleriayrestauracion.csv"); 
	insert_coordenadas_bd("asociacionesciudadania.csv");
	insert_coordenadas_bd("comercio.csv");
	insert_coordenadas_bd("educacionycultura.csv");
	insert_coordenadas_bd("medicinaysalud.csv");
	//insert_coordenadas_bd("general.csv");*/
	
	
	insert_ubicacion();
	echo "Se han ubicado: ".$ubicadas." de las ".$totales." que se han analizado.Porcentaje de acierto: ".round((($ubicadas/$totales)*100),2)." %";

	// Obtener info de una noticia en concreto	
	function get_noticia($id){
		$mongo = new MongoDB\Driver\Manager(Config::MONGODB);

		$filter['_id']=new MongoDB\BSON\ObjectID($id); 
		$query = new MongoDB\Driver\Query($filter);
		
		$rows = $mongo->executeQuery('NoticiasDB.noticia', $query); // $mongo contains the connection object to MongoDB
		$fila = $rows->toArray();

		if ( !empty($fila) ){
			foreach($fila as $r){
				$arr[] = array(
					'RSS'		=> $r->rss,
					'PERIODICO'	=> $r->periodico,
					'TITULAR'	=> $r->titular,
					'DESCRIPCION'	=> $r->descripcion,
					'LINK'		=> $r->link,
					'FECHA'		=> (new MongoDB\BSON\UTCDateTime((string)$r->fecha))->toDateTime()->format('d-m-Y')
				);
			}
			return $arr;
		}
		else{
			echo "No existe la noticia indicada".'<br>';
		}
	}




	// Obtener info de todas las noticias de una coleccion
	function get_all_noticias($coleccion, $limit=0){
		$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
		$query = new MongoDB\Driver\Query([], ['limit' => $limit]);
		
		$rows = $mongo->executeQuery('NoticiasDB.'.$coleccion, $query); // $mongo contains the connection object to MongoDB
		$fila = $rows->toArray();
		$fecha = date_create();

		if ( !empty($fila) ){
			foreach($fila as $r){

				$arr[] = array(
					'_id'		=> $r->_id,
					'RSS'		=> $r->rss,
					'PERIODICO'	=> $r->periodico,
					'TITULAR'	=> $r->titular,
					'DESCRIPCION'	=> $r->descripcion,
					'LINK'		=> $r->link,
					'FECHA'		=> (new MongoDB\BSON\UTCDateTime((string)$r->fecha))->toDateTime()->format('d-m-Y')
				);	
			}
			return $arr;
		}
		else{
			echo "No existe la noticia indicada".'<br>';
		}
	}




	// Contar todas las noticias de la base de datos en la coleccion noticia
	function count_all_noticias(){
		$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
		$query = new MongoDB\Driver\Query([]);
		
		$rows = $mongo->executeQuery('NoticiasDB.noticia', $query); // $mongo contains the connection object to MongoDB
		$fila = $rows->toArray();
	
		echo "La base de datos 'NoticiasDB' cuenta con: ".count($fila)." documentos en la coleccion 'noticia'";
	}


	

	//Eliminar todos los documentos de una coleccion
	function remove_coleccion($coleccion){
			
		$mongo 	= new MongoDB\Driver\Manager(Config::MONGODB);
		$bulk 	= new MongoDB\Driver\BulkWrite;
		
		$bulk->delete([]);
		$result = $mongo->executeBulkWrite('NoticiasDB.'.$coleccion, $bulk);
		printf("Se han eliminado %d documentos en la base de datos\n", $result->getDeletedCount());
	}




	//Eliminar un documento en concreto de una coleccion /Eliminar una noticia de la tabla noticia
	function remove_noticia($id, $coleccion){
		
		$mongo 	= new MongoDB\Driver\Manager(Config::MONGODB);
		$bulk 	= new MongoDB\Driver\BulkWrite;
		$id 	= new MongoDB\BSON\ObjectID($id);


		$bulk->delete(['_id' => $id]);
		$result = $mongo->executeBulkWrite('NoticiasDB.'.$coleccion, $bulk);
		printf("Se han eliminado %d documentos en la base de datos\n", $result->getDeletedCount());
	}
	
	



	//LEER E INSERTAR LOS LUGARES DE SC DE TENERIFE EN LA BASE DE DATOS CON SUS COORDENADAS
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
			$GLOBALS['insertados'] += $result->getInsertedCount();
			//echo $insertados;
		} 
	}



	//CREAR FICHERO JSON SI AUN NO HA SIDO CREADO CON LA UBICACION y COORDENADAS DE CADA NOTICIA
	function create_json_marcadores(){
		
		$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
		$query = new MongoDB\Driver\Query([]);
		
		$rows = $mongo->executeQuery('NoticiasDB.coordenadas', $query); // $mongo contains the connection object to MongoDB
		$fila = $rows->toArray();
		
		//Leer filas resultantes de la consulta
		if ( !empty($fila) ){
			foreach($fila as $r){

				//Guardar en un array los resultados
				$arr[] = array(
					'_id'		=> $r->id,
					'LUGAR'		=> $r->LUGAR,
					//'DISTRITO'	=> $r->DISTRITO,
					'LATITUD'	=> $r->LATITUD,
					'LONGITUD'	=> $r->LONGITUD
				);	
			}

			
			//Crear json con barrios y coordenadas
			$nombre_archivo = Config::PATH.'/dump/coordenadas.json'; 
 
			if(!file_exists($nombre_archivo)){	
				$fp = fopen($nombre_archivo,"w+");
				fwrite($fp, json_encode($arr));
				fclose($fp);	
			}else{
				$fp = fopen($nombre_archivo,"a+");
				fwrite($fp, json_encode($arr));
				fclose($fp);
			}
		}
		else{
			echo "No existen noticias";
		}

	}


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
*/











	//EXCEL
	// Obtener listado de noticias a partir de un fichero excel
	function get_noticias_excel() {
	
		$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
		
		require_once("PHPExcel/IOFactory.php");
 		$nombreArchivo = Config::PATH.'/dump/tuplasTablaNoticias.xls';

		// Cargar hoja de cálculo
		$objPHPExcel = PHPExcel_IOFactory::load($nombreArchivo);

		//Asignar la hoja de calculo activa
		$objPHPExcel->setActiveSheetIndex(0);

		//Obtener el numero de filas del archivo
		$numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();


		// Recorrer el excel obteniendo los datos de cada columna y cada fila
		for ($i = 1; $i <= $numRows; $i++) {
			$noticias[] = array(
				'RSS' 		=> $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue(),
				'PERIODICO' 	=> $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue(),
				'TITULAR' 	=> $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue(),
				'DESCRIPCION' 	=> $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue(),
				'LINK' 		=> $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue(),
				'FECHA' 	=> $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue()
			);
 		}
		
		return $noticias;		
	}




	// Insertar noticias en la base de datos a partir de un array de noticias
	function insert_noticias_bd($noticias){

		$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
		$bulk = new MongoDB\Driver\BulkWrite;
		
		foreach ($noticias as $registro) {

			$doc = array(
				'id'      	=> new MongoDB\BSON\ObjectID,     #Generate MongoID
				'RSS'		=> $registro['RSS'],
				'PERIODICO'	=> $registro['PERIODICO'],
				'TITULAR'	=> $registro['TITULAR'],
				'DESCRIPCION'	=> $registro['DESCRIPCION'],
				'LINK'		=> $registro['LINK'],
				'FECHA'		=> $registro['FECHA']
			);

			$bulk->insert($doc);
		}

		
		$result = $mongo->executeBulkWrite('NoticiasDB.noticia', $bulk); # 'NoticiasDB' es la base de datos y 'noticia' la collection.  
		printf("Se han insertado %d documentos en la base de datos\n", $result->getInsertedCount());
	}
	
?>











