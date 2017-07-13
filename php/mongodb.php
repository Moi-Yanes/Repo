<?php 
	/* PHP ERRORS */
	ini_set('display_errors','On');
	
	
	/* PHP CONFIG */
	require_once 'include.php';
	

	/* PHPExcel */
	set_include_path(implode(PATH_SEPARATOR, array(realpath(Config::PATH .'/phpexcel/Classes/'),get_include_path(),)));





	// Calcular la ubicacion de la noticia e insertar en la base de datos
	function insert_ubicacion(){


		//comprobar si el campo ubicacion existe en la bbdd sino crearlo

		//obtener las ubicaciones y guardarlas en ese campo
	}




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
	



	//LEER E INSERTAR LOS BARRIOS DE SC DE TENERIFE EN LA BASE DE DATOS CON SUS COORDENADAS
	function insert_coordenadas_bd(){
		
		$mongo = new MongoDB\Driver\Manager(Config::MONGODB);
		$bulk = new MongoDB\Driver\BulkWrite;
		$registros = array();

		//OBTENER DATOS DE CSV
		if (($fichero = fopen(Config::PATH."/dump/barrios.csv", "r")) !== FALSE) {
			
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


			//GUARDAR DATOS EN LA BASE DE DATOS
			for ($i = 0; $i < count($registros); $i++) {
				//echo "Nombre: " . $registros[$i]["BARRIO"] . "\n";
		
				$doc = array(
					'id'      	=> new MongoDB\BSON\ObjectID,     #Generate MongoID
					'BARRIO'	=> $registros[$i]['BARRIO'],
					'DISTRITO'	=> $registros[$i]['DISTRITO'],
					'LATITUD'	=> $registros[$i]['GRAD_Y'],
					'LONGITUD'	=> $registros[$i]['GRAD_X']
				);
				$bulk->insert($doc);	
			}

			$result = $mongo->executeBulkWrite('NoticiasDB.coordenadas', $bulk); # 'NoticiasDB' es la base de datos y 'coordenadas' la collection.  
			printf("Se han insertado %d documentos en la base de datos\n", $result->getInsertedCount());
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
					'BARRIO'	=> $r->BARRIO,
					'DISTRITO'	=> $r->DISTRITO,
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


	// Pruebas 
	//$arr = get_noticias_excel(); //leer excel de noticas
	//echo count($arr);	
	//insert_noticias_bd($arr);   // guardar noticias en bbdd

	//remove_coleccion('noticia');
	
	//count_all_noticias();
	//echo '<br>';
	//remove_noticia('595b8133bc5cec0a8e7ebbd9', 'noticia');
	//get_noticia('5861514401d0282764853a9e'); //Buscar noticia por id
	//insert_coordenadas_bd();
	
?>











