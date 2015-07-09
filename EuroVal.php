<?php 
/**
* EuroVal - Libreria de validacion y saneamiento de datos PHP
* @author      Luis Pastén (https://www.facebook.com/luispastenpuntonet)
* @copyright   Copyright (c) 2015 inifiniwebs.com
* @link        https://github.com/Europpa
* @version     1.0
*/

class EUROVAL{
	protected $errors = array();
	protected $validadores = array();
	protected $filtros = array();
	
	/**
	 * Funcion principal para correr los metodos de filtrado y validado
	 * @param  mixed $input        	  Dato del formulario
	 * @param  array  $validaciones   Array de validaciones a realizar
	 * @param  array  $filtros        Array de filtros a realizar		
	 * @return mixed                  Retorna verdadero si se cumplio o array con errores si ha fallado
	 */
	public function run($input, array $validaciones, array $filtros){
		if(!isset($_POST[$input])){
			$this->errors[] = array(
				'error' => 'undefined');
			return $this->getErrors($this->errors);
		}
		$this->validadores = $validaciones;
		$this->filtros = $filtros;
		$campo = $_POST[$input];
		$data = $this->filtrar($campo, $this->filtros);
		if($this->validar($data, $this->validadores) === false){
			return $this->getErrors($this->errors);
		}
		return true;
	}
	
	/**
	 * Funcion principal para el filtrado de datos
	 * @param  mixed $input    Valor del input a filtrar
	 * @param  array  $filtros Filtros elegidos
	 * @return mixed           Regresa el dato con los filtros aplicados
	 * @throws exception       Filtros no existentes 
	 */
	protected function filtrar($input, array $filtros){
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
	 * @param  mixed $input        Dato del formulario
	 * @param  array  $validadores Contiene todos los validadores
	 * @param  array $params       Contiene los parametros en caso de existir
	 * @return mixed               Retorna true si los datos son correctos o un array si existio algun error.
	 */
	protected function validar($input, array $validadores, $params = NULL){

		if (in_array('required', $validadores) && trim(empty($input)) || is_null(trim($input))) { // ¿el campo es requerido?
			$this->errors[] = $this->required($input);
		}else{
			foreach($validadores as $validador){
				$vali = NULL;
				$method = NULL;
				$params = NULL;
				if(strstr($validador,',') !== false){ // tiene parametros
					$vali = explode(',', $validador);
	 				$method = $vali[0];
	 				$params = $vali[1];
	 				if(is_callable(array($this,$method))){
	 					$resp = $this->$method($input,$params);
	 					if(is_array($resp)){
	 						$this->errors[] = $resp;
	 					}
	 				}else{
	 					throw new Exception('No existe el validador');
	 				}
				}else{
					if(is_callable(array($this,$validador))){
						$resp = $this->$validador($input);
						if(is_array($resp)){
							$this->errors[] = $resp;
						}
					}else{
						throw new Exception('No existe el validador');
					}
				}
			}
		}	
		return count($this->errors) > 0 ? false : true;
	}

	/*--------------------------------------------------------------------*/
	/*--------------------------VALIDADORES-------------------------------*/
	/*--------------------------------------------------------------------*/
	
	/**
	 * Funcion que verifica si el campo no es vacio o nulo
	 * @param  Mixed $input Dato del formulario
	 * @return Mixed        Regresa true si no es vacio de lo contrario un array de error
	 */
	protected function required($input){
		if(!is_null(trim($input)) && !trim(empty($input))){
			return true;
		}else{
			return array(
				'error' => 'required');
		}
	}

	/**
	 * Funcion que verifica que el texto es de tipo alphanumerico								
	 * @param  mixed $dato  Dato del formulario
	 * @return mixed        True o array de errores
	 */
	protected function alpha_numeric($dato = null){
		if(preg_match("/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ])+$/i", $dato)){
			return true;	
		}else{
			return array(
				'error' => 'alpha_numeric');			
		}		
	}

	/**
	 * El texto tiene que ser alfanumerico con posibilidad de introducir espacios
	 * @param  mixed $dato Dato del formulario
	 * @return mixed       True o array de errores  
	 */	
	protected function alpha_spaces($dato = null){
		if (preg_match("/^([a-z0-9ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ\s])+$/i", $dato)) {
			return true;
		}else{
			return array(
				'error' => 'alpha_spaces');
		}
	}

	/**
	 * Verifica longitud minima de una cadena
	 * @param  mixed $input  Dato del formulario
	 * @param  int $params   Numero minimo de caracteres
	 * @return mixed         True si cuenta con longitud minima. de lo contrario array de error
	 */
	protected function min_len($input,$params){
		if(mb_strlen($input) >= (int)$params){
			return true;
		}else{
			return array(
				'error' => 'min_len',
				'len' => $params);
		}
	}

	/*--------------------------------------------------------------------*/
	/*------------------------------FILTROS-------------------------------*/
	/*--------------------------------------------------------------------*/

	/**
	 * Fusion que sanea las variables de tipo string quitando caracteres especiales
	 * @param  string $input Dato del formulario 
	 * @return string        Dato saneado
	 */
	protected function sanitize_string($input){
		return filter_var(trim($input),FILTER_SANITIZE_STRING);
	}	

	/*--------------------------------------------------------------------*/
	/*------------------------------ERRORES-------------------------------*/
	/*--------------------------------------------------------------------*/
	
	/**
	 * Funsion que convierte los errores a legibles
	 * @param  array  $errors Array de errores y parametros necesarios
	 * @return Array          Regresa un array con los mensajes de error legibles
	 */
	protected function getErrors(array $errors){
		$resp = array();
		foreach ($errors as $val) {
			switch($val['error']){
				case 'undefined':
					$resp[] = 'El campo no esta definido';
				break;
				case 'required':
					$resp[] = 'El campo es requerido';
				break;
				case 'min_len':
					$resp[] = 'La longitud del campo no puede ser menor a ' .$val['len']. ' caracteres';
				break;
				case 'alpha_numeric':
					$resp[] = 'El campo solo puede contener caracteres alfanumericos';
				break;
				case 'alpha_spaces':
					$resp[] = 'El campo solo puede contener caracteres alfanumericos con espacios';
				break;
			}	
		}
		return $resp;
	}
}

 ?>