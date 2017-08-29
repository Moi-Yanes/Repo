<?php 

	//Eliminar lso acentos de las palabras claves obtenidas para poder compararlos correctamente con los almacenados en la base de datos(no tienen tilde)
	function quitar_tildes($cadena) {
		$no_permitidas= array ( "á","é","í","ó","ú",
					"à","è","ì","ò","ù",
				      	"Á","É","Í","Ó","Ú","À",
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
		$n_c = preg_replace("/([^A-Za-z0-9[:space:]áéíóúÁÉÍÓÚñÑ.-])/", " ", $texto);	
		$n_c = quitar_tildes($n_c); 			
		preg_match_all("/([A-ZÑ]{1}[A-Za-z0-9Ññ]+)((\s|\-|)((\sde\s(la\s|el\s|las\s|los\s){0,1})|(\sdel\s)|([0-9])|([A-ZÑ]{1}[A-Za-z0-9Ññ]+)))*/", $n_c, $coinciden);
	

		$arr=array();
		foreach($coinciden as $r){ 
			foreach($r as $n){ 
				$n = explode(' ',$n);
				if(end($n) == ''){
					array_pop($n);  //quita "" del final
					array_pop($n);	//quita "de" o "del" del final
				}
				$n = implode(" ", $n);
			
				array_push($arr,strtoupper(str_replace('ñ', 'Ñ', $n)));//Meter en un array los resultados obtenidos despues de haber filtrado el texto       
			}
			break;	// Para quedarnos solo con los valores del primer subarray de coincidencias que son aquellos que cumplen completamente con la expresion regular dada
				// el resto de subarrays devueltos solo la cumplen parcialmente
		}
		return $arr;
	}



	//DIVIDIR ARRAY Y AGRUPAR NOTICIAS SEGUN SU UBICACION
	function split_array($array){

		$groupedArray   = array();
		$ubicacionArray = array();

		foreach($array as $key => $r){

			//consigo la ubicacion actual
			$ubicacion = $r->latitud;

			//verifico si la ubicacion existe en mi array donde alojo las noticias y su ubicacion //si no existe, lo agrego
			if(!in_array($ubicacion, $ubicacionArray)){
			$ubicacionArray[] = $ubicacion;
			}

			//busco la ubicacion actual
			$ubicacionIndex = array_search($ubicacion, $ubicacionArray);

			//agrego el registro dentro del array con sus valores
			$titulo 	= quitar_tildes($r->titular);
			$descripcion 	= quitar_tildes($r->descripcion);

			$titulo		= preg_replace("/([^A-Za-z0-9,[:space:]áéíóúÁÉÍÓÚñÑ.-])\(\)/", "", $titulo);
			$descripcion 	= preg_replace("/([^A-Za-z0-9,[:space:]áéíóúÁÉÍÓÚñÑ.-])\(\)/", "", $descripcion);

			$arr = array(
				'_id'		=> $r->_id,
				'PERIODICO'	=> quitar_tildes($r->periodico),
				'TITULO'	=> $titulo,
				'DESCRIPCION'	=> $descripcion,
				'LINK'		=> $r->link,
				'RSS'		=> $r->rss,
				'FECHA'		=> (new MongoDB\BSON\UTCDateTime((string)$r->fecha))->toDateTime()->format('d-m-Y'),
				'UBICACION'	=> $r->ubicacion,
				'LATITUD'	=> $r->latitud,
				'LONGITUD'	=> $r->longitud,
			);

			$groupedArray[$ubicacionIndex][] = $arr;
		}

		return $groupedArray;
	}



	function porcentaje($total, $parte, $redondear = 2) {
    		return round($parte / $total * 100, $redondear);
	}

?>
