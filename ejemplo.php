<?php 
require 'EuroVal.php'; 
$euroval =  new EUROVAL(); // Instanciando la libreria
isset($_POST['nombre']) ? $_POST['nombre'] : exit; // Validando que la data exista

define('DS', DIRECTORY_SEPARATOR); // Separador de direcctorios
define('ROOT', realpath(dirname(__FILE__)) . DS); // Ruta del sistema
define('IMAGES', ROOT . 'imagenes' . DS); // Ruta de la carpeta donde se guardarán los archivos


try { // Usar try - catch para agarrar las posibles excepciones emitidas por la libreria
	$nombre = $euroval->run(
		'Nombre', // Nombre del campo    						 			 
		$_POST['nombre'], // Dato a validar
		array('required','alphabetic','min_len,2'), // Validaciones
		array() // Filtros
		);
	var_dump($nombre);

	$fecha = $euroval->run(
		'Fecha',
		$_POST['fecha'],
		array('required','date,Y-m-d'),
		array()
		);
	var_dump($fecha);

	$archivo = $euroval->run(
		'Archivo',
		$_FILES['archivo'],
		array('file_exists','file_validate,1024,application/msword|application/pdf|application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
		array()
		);
	var_dump($archivo);

}catch (Exception $e) {
	echo $e->getMessage(); // Regresa los mensajes de las excepciones 
}
 ?>
