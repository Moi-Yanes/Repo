<?php
	 
	/* PHP ERRORS */
	ini_set('display_errors','On');
	require_once 'include.php';	


	//Rellenar fichero json con noticias que se reciben desde el ajaxpost
	if( isset($_GET['opcion']) ){

		if( $_GET['opcion']==1 ){ //json
			if( isset($_POST['json']) ){
				$nombre_archivo = Config::PATH.'/dump/downloadfile.json'; 
				$json = $_POST['json'];

				if(!file_exists($nombre_archivo)){	
					$fp = fopen($nombre_archivo,"w+");
					fwrite($fp, $json);
					fclose($fp);	
				}else{
					$fp = fopen($nombre_archivo,"w");
					fwrite($fp, $json);
					fclose($fp);
				}
			}
		}
		elseif( $_GET['opcion']==2 ){ //txt
		
			if( isset($_POST['txt']) ){
				$nombre_archivo = Config::PATH.'/dump/downloadfile.txt'; 
				$json = $_POST['txt'];

				if(!file_exists($nombre_archivo)){	
					$fp = fopen($nombre_archivo,"w+");
				}else{
					$fp = fopen($nombre_archivo,"w");
				}
			
				fwrite($fp, "LISTADO DE NOTICAS\n\n");
				foreach($json as $r){
						if($r['TITULO'] != null ){
							fwrite($fp, "Periodico: ".$r['PERIODICO']."\tUbicacion: ".$r['UBICACION']."\tFecha: ".$r['FECHA']."\n"."\n");	
							fwrite($fp, "Titulo"."\n".$r['TITULO']."\n"."\n");
							fwrite($fp, "Descripcion"."\n".preg_replace('/<[\/\!]*?[^<>]*?>/si', '', $r['DESCRIPCION'])."\n"."\n");
							fwrite($fp, "Link ".$r['LINK']."\n"."\n");
						}	
				}
				fclose($fp);
			}
		}
		

		//Descarga de fichero
		$root = Config::PATH.'/dump/';
		$file = basename($_GET['file']);
		$path = $root.$file;
		$type = '';

		if (is_file($path)) {
			$size = filesize($path);
			if (function_exists('mime_content_type')) {
				$type = mime_content_type($path);
			} else if (function_exists('finfo_file')) {
				$info = finfo_open(FILEINFO_MIME);
				$type = finfo_file($info, $path);
				finfo_close($info);
			}
			if ($type == '') {
				$type = "application/force-download";
			}

			// Definir headers
			header("Content-Type: $type");
			header("Content-Disposition: attachment; filename=$file");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . $size);

			// Descargar archivo
			readfile($path);
		} else {
		 	echo "El archivo no existe.";
		}

	}
?>



