<?php 
require 'EuroVal.php'; 
$euroval =  new EUROVAL(); // Instanciando la libreria

try { // Usar try - catch para agarrar las posibles excepciones emitidas por la libreria
	$respuesta = $euroval->run(
		'nombre', // Nombre del campo como aparece en el formulario
		array('required','alpha_spaces','min_len,4'), // Validaciones
		array('sanitize_string') // Filtros
		);
	echo var_dump($respuesta); // Array de respuestas

}catch (Exception $e) {
	echo $e->getMessage();
}
 ?>