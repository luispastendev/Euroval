<?php 
/**
* EuroVal - Libreria de validacion y saneamiento de datos PHP
* @author      Luis Pastén (https://www.facebook.com/luispastenpuntonet)
* @copyright   Copyright (c) 2015 inifiniwebs.com
* @link        https://github.com/Europpa
* @version     1.0.1
*/

class EUROVAL{
	protected $errors = array();
	protected $validadores = array();
	protected $filtros = array();
	
	/**
	 * Funcion principal para correr los metodos de filtrado y validado
	 * @param  string $field          Nombre del campo
	 * @param  mixed  $data        	  Dato del formulario
	 * @param  array  $validaciones   Array de validaciones a realizar
	 * @param  array  $filtros        Array de filtros a realizar		
	 * @return mixed                  Retorna true o false
	 */
	public function run($field, $data, array $validaciones, array $filtros = array()){
		unset($this->errors);
		$this->validadores = $validaciones;
		$this->filtros = $filtros;
		$filter_data = $this->filtrar($data ,$this->filtros);
		$validate_data =  $this->validar($filter_data, $field, $this->validadores);
		if($validate_data === false){
			return $this->getErrors($this->errors);
		}
		return $validate_data;
	}
	
	/**
	 * Funcion principal para el filtrado de datos
	 * @param  mixed $input    Valor del input a filtrar
	 * @param  array  $filtros Filtros elegidos
	 * @return mixed           Regresa el dato con los filtros aplicados
	 * @throws exception       Filtros no existentes 
	 */
	protected function filtrar($input, array $filtros = array()){
		foreach ($filtros as $filtro) {
			if(is_callable(array($this,$filtro))){
				$input = $this->$filtro($input);
			}else{
				throw new Exception("No existe el filtro");
			}
		}
		return $input;
	}

	/**
	 * Funcion para aplicar los filtros de validacion requeridos
	 * @param  mixed  $input        Dato del formulario
	 * @param  string $field        Nombre del campo
	 * @param  array  $validadores  Contiene todos los validadores
	 * @param  array  $params       Contiene los parametros en caso de existir
	 * @return mixed                Retorna true si los datos son correctos o un array si existio algun error.
	 */
	public function validar($input, $field, array $validadores){
		$this->errors = array();
		foreach($validadores as $validador){
			$vali = NULL;
			$method = NULL;
			$params = array();
			if(strstr($validador,',') !== false){ // tiene parametros
				$vali = explode(',', $validador);
 				$method = $vali[0];
				for($n = 1; $n <= count($vali) - 1; $n++){
					array_push($params, $vali[$n]); 
				}
				$params = implode(',', $params);
 				if(is_callable(array($this,$method))){
 					$resp = $this->$method($input,$field,$params);
 					if(is_array($resp)){
 						$this->errors[] = $resp;
 	 				}
 				}else{
 					throw new Exception('No existe el validador');
 				}
			}else{ // Cuando no tiene parametros
				if(is_callable(array($this,$validador))){
					$resp = $this->$validador($input,$field);
					if(is_array($resp)){
						$this->errors[] = $resp;
					}
				}else{
					throw new Exception('No existe el validador');
				}
			}
		}	
		return count($this->errors) > 0 ? false : $input;
	}

	/*--------------------------------------------------------------------*/
	/*--------------------------VALIDADORES-------------------------------*/
	/*--------------------------------------------------------------------*/
	
	/**
	 * Funcion que verifica si el campo no es vacio o nulo
	 * @param  Mixed  $input    Dato del formulario
	 * @param  string $field    Nombre del campo
	 * @param  array  $params   Parametros
	 * @return Mixed            Regresa true si no es vacio de lo contrario un array de error
	 */
	protected function required($input, $field, $params = null){
		if(!is_null(trim($input)) && !empty(trim($input))){
			return true;
		}else{
			return array(
				'error' => 'required',
				'field' => $field);
		}
	}

	/**
	 * Funcion que verifica que el texto es de tipo alphanumerico								
	 * @param  Mixed  $input    Dato del formulario
	 * @param  string $field    Nombre del campo
	 * @param  array  $params   Parametros
	 * @return mixed            True o array de errores
	 */
	protected function alpha_numeric($input, $field, $params = null){
		if(empty($input)){
			return true;
		}
		if(preg_match("/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i", $input)){
			return true;	
		}else{
			return array(
				'error' => 'alpha_numeric',
				'field' => $field);			
		}		
	}

	/**
	 * El texto tiene que ser alfanumerico con posibilidad de introducir espacios
	 * @param  Mixed  $input    Dato del formulario
	 * @param  string $field    Nombre del campo
	 * @param  array  $params   Parametros
	 * @return mixed       True o array de errores  
	 */	
	protected function alpha_spaces($input, $field, $params = null){
		if(empty($input)){
			return true;
		}
		if (preg_match("/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/i", $input)){
			return true;
		}else{
			return array(
				'error' => 'alpha_spaces',
				'field' => $field);
		}
	}

	 /**
	  * Campos de solo Letras
	  * @param  Mixed  $input   Dato del formulario
	  * @param  string $field   Nombre del campo
	  * @param  array  $params  Parametros
	  * @return Mixed           True o array de errores
	  */
	protected function alphabetic($input, $field, $params = null){
		if(empty($input)){
			return true;
		}
		if (preg_match("/^([a-zÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/i", $input)){
			return true;
		}else{
			return array(
				'error' => 'alphabetic',
				'field' => $field);
		}
	}

	/**
	 * Verifica longitud minima de una cadena
	 * @param  Mixed  $input    Dato del formulario
	 * @param  string $field    Nombre del campo
	 * @param  array  $params   Parametros
	 * @return mixed            True si cuenta con longitud minima. de lo contrario array de error
	 */
	protected function min_len($input, $field, $params = null){
		if(empty($input)){
			return true;
		}
		if(mb_strlen($input) >= (int)$params){
			return true;
		}else{
			return array(
				'error' => 'min_len',
				'len' => $params,
				'field' => $field);
		}
	}

	/**
	 * Verifica longitud maxima de una cadena
	 * @param  Mixed  $input   Dato del formulario
	 * @param  string $field   Nombre del campo
	 * @param  array  $params  Parametros
	 * @return mixed           True o Array
	 */
	protected function max_len($input, $field, $params = null){
		if(empty($input)){
			return true;
		}
		if(mb_strlen($input) <= (int)$params){
			return true;
		}else{
			return array(
				'error' => 'max_len',
				'len' => $params,
				'field' => $field);
		}	
	}

	/**
	 * Validacion de solo numeros enteros
	 * @param  Mixed   $input   Dato del formulario
	 * @param  String  $field   Nombre del campo
	 * @param  Array   $params  Parametros
	 * @return Mixed         	True o array
	 */
	protected function integer($input, $field, $params = null){
		if(empty(trim($input))){
			return true;
		}
		if(filter_var($input, FILTER_VALIDATE_INT)){
			return true;
		}else{
			return array(
				'error' => 'integer',
				'field'	=> $field
				);
		}
	}

	/**
	 * Validacion de numeros decimales
	 * @param  mixed  $input   Dato del formulario
	 * @param  string $field   Nombre del campo
	 * @param  array  $params  Parametros
	 * @return mixed           True o array
	 */
	protected function float($input, $field, $params = null){
		if(empty($input)){
			return true;
		}
		if(filter_var($input, FILTER_VALIDATE_FLOAT)){
			return true;
		}else{
			return array(
				'error' => 'float',
				'field' => $field
				);
		}

	}

	/**
	 * Verifica direcciones de email validas
	 * @param  mixed   $input   Dato del formulario
	 * @param  string  $field   Nombre del campo
	 * @param  array   $params  Parametros
	 * @return mixed            True o array
	 */
	protected function email($input, $field, $params = null){
		if(empty($input)){
			return true;
		}
		if(filter_var($input, FILTER_VALIDATE_EMAIL)){
			return true;
		}else{
			return array(
				'error' => 'email',
				'field' => $field
				);
		}
	}

	/**
	 * Valida fechas validas y en formato correcto
	 * @param  mixed   $input   Dato de formulario
	 * @param  string  $field   Nombre del campo
	 * @param  string  $params  Formato de fecha
	 * @return mixed            True o array de errores
	 */
	protected function date($input, $field, $params = 'Y-m-d H:i:s'){
		if(empty($input)){
			return true;
		}
    	$date = DateTime::createFromFormat($params, $input);
    	if($date && $date->format($params) == $input){
    		return true;
    	}else{
    		return array(
    			'error' => 'date',
    			'field' => $field
    			);
    	}
	}

	/**
	 * Verifica que se hay enviado el campo de tipo archivo
	 * @param  file   $input  Datos sobre el archivo
	 * @param  string $field  Nombre del campo
	 * @return Mixed          Retorna array o true
	 */
	protected function file_exists($input, $field, $params = null){
		if($input['error'] !== 4){
			return true;
		}else{
			return array(
				'error' => 'file_exists',
				'field' => $field
				);
		}
	}

	/**
	 * Valida que el archivo sea correcto en tamaño, formato y guardado
	 * @param  file   $input  Archivo
	 * @param  string $field  Nombre del archivo
	 * @param  string $params Cadena de parametros separados por ","
	 * @return mixed          Retorna true o array 
	 */
	protected function file_validate($input, $field, $params = null){
		if($input['error'] == 4){
			return true;
		}else{
			$params = explode(',', $params);
			$size = $params[0];
			$formatos = explode('|', $params[1]);
			if(!$input['error'] > 0){
               	if(in_array($input['type'], $formatos)){
            		if($input['size'] <= ($size * 1024)){
	                	return true; 
            		}
            		return array(
						'error' => 'file_validate_size',
						'field' => $field
					);	
            	}
            	return array(
				'error' => 'file_validate_format',
				'field' => $field
				);
        	}
        	return array(
				'error' => 'file_validate_problem',
				'field' => $field
			);
		}
	}

	/*--------------------------------------------------------------------*/
	/*------------------------------FILTROS-------------------------------*/
	/*--------------------------------------------------------------------*/

	/**
	 * Funcion que sanea las variables de tipo string quitando caracteres especiales
	 * @param  string $input Dato del formulario 
	 * @return string        Dato saneado
	 */
	protected function filter_string($input){
		return filter_var(trim($input),FILTER_SANITIZE_STRING);
	}	

	/**
	 * Sanea variables que contengan tags html conviertiendolas en entidades de texto
	 * @param  mixed   $input   Dato del formulario
	 * @return string           Dato saneado
	 */
	protected function filter_htmlencode($input){
		return filter_var(trim($input), FILTER_SANITIZE_SPECIAL_CHARS);
	}

	/**
	 * Sanea variables con formato email
	 * @param  mixed  $input Dato del formulario
	 * @return string        Dato saneado
	 */
	protected function filter_email($input){
		return filter_var(trim($input), FILTER_SANITIZE_EMAIL);
	}

	/**
	 * Sanea variables de tipo numero
	 * @param  mixed  $input Dato del formulario
	 * @return string        Dato saneado
	 */
	protected function filter_numbers($input){
		return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
	}

	/*--------------------------------------------------------------------*/
	/*------------------------------ERRORES-------------------------------*/
	/*--------------------------------------------------------------------*/
	
	/**
	 * Funsion que convierte los errores a legibles
	 * @param  array  $errors Array de errores y parametros necesarios
	 * @return Array          Regresa un array con los mensajes de error legibles
	 */
	protected function getErrors(){
		$resp = array();
		foreach ($this->errors as $val) {
			switch($val['error']){
				case 'required':
					$resp[$val['error']] = 'El campo '.$val['field']. ' es requerido';
				break;
				case 'min_len':
					$resp[$val['error']] = 'La longitud del campo '.$val['field'].' no puede ser menor a ' .$val['len']. ' caracteres';
				break;
				case 'max_len':
					$resp[$val['error']] = 'La longitud del campo '.$val['field'].' no puede ser mayor a ' .$val['len']. ' caracteres';					
				break;
				case 'alpha_numeric':
					$resp[$val['error']] = 'El campo '.$val['field'].' solo puede contener caracteres alfanumericos';
				break;
				case 'alpha_spaces':
					$resp[$val['error']] = 'El campo '.$val['field'].' solo puede contener caracteres alfanumericos con espacios';
				break;
				case 'alphabetic':
					$resp[$val['error']] = 'El campo '.$val['field'].' solo puede contener letras';
				break;
				case 'integer':
					$resp[$val['error']] = 'El campo '.$val['field'].' solo puede contener numeros enteros';
				break;
				case 'float':
					$resp[$val['error']] = 'El campo '.$val['field'].' solo puede contener numeros enteros o decimales';
				break;
				case 'email':
					$resp[$val['error']] = 'El campo '.$val['field'].' contiene una direccion de email invalida';
				break;
				case 'date':
					$resp[$val['error']] = 'El campo '.$val['field'].' tiene un formato incorrecto';
				break;
				case 'file_exists':
					$resp[$val['error']] = 'No subio ningun archivo '.$val['field'];
				break;
				case 'file_validate_problem':
					$resp[$val['error']] = 'El archivo '.$val['field'].' ha tenido algun problema';
				break;
				case 'file_validate_format':
					$resp[$val['error']] = 'El archivo '.$val['field'].' no cuenta con una extension valida';
				break;
				case 'file_validate_size':
					$resp[$val['error']] = 'El archivo '.$val['field'].' es muy grande';
				break;
				
			}	
		}
		return $resp;
	}
}
 ?>