<?php 
require 'EuroVal.php'; 
$euroval =  new EUROVAL(); // Instanciando la libreria
$res = isset($_POST['nombre']) ? $_POST['nombre'] : exit; // Validando que la data exista

try { // Usar try - catch para agarrar las posibles excepciones emitidas por la libreria
	$nombre = $euroval->run(
		'Nombre',  									  // Nombre del campo    						 			  // Dato a validar
		$_POST['nombre'],							  // Datos
		array('required','alphabetic','min_len,4'),   // Validaciones
		array('filter_string') 				      	  // Filtros
		);
	echo var_dump($nombre); // Array de respuestas

}catch (Exception $e) {
	echo $e->getMessage(); // Regresa los mensajes de las excepciones 
}
 ?>
