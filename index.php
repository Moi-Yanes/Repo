<?php 
	
	include "mongodb.php"


?>


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
			$arr = get_noticia('587536bc01d0282ddcf2d287')/*get_all_noticias('noticia', 10)*/;
			
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

