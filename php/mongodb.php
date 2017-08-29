<?php 
	/* PHP ERRORS */
	ini_set('display_errors','On');
	
	
	/* PHP CONFIG */
	require_once 'include.php';
	

	/* PHPExcel */
	set_include_path(implode(PATH_SEPARATOR, array(realpath(Config::PATH .'/phpexcel/Classes/'),get_include_path(),)));


	/*Variables globales*/
	$noticias_totales =0;
	$noticias_ubicadas=0;



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

