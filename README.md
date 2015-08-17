# Euroval
Librería de validación y saneo de datos en PHP

## Uso de la librería

1. Descargue la libería via .zip
2. Descomprima la librería en su proyecto
3. Incluya el archivo __EuroVal.php__ 

```php
require 'EuroVal.php'; 

// Instanciando la libreria
$euroval =  new EUROVAL(); 

// Se recomienda validar que exista la variable al ser recibida
isset($_POST['campo']) ? $_POST['campo'] : exit; 
```

## Ejemplo
El siguiente ejemplo muestra la validación de un campo de texto mediante la librería como se puede observar
la libreria validara los datos con los filtros y validadores requeridos, en caso de que la validación fallara
la librería devolverá un array con los errores encontrados o en caso de que la validación sea exitosa devolverá los datos validados y filtrados.

```php
require 'EuroVal.php'; 
$euroval =  new EUROVAL(); 
isset($_POST['campo']) ? $_POST['campo'] : exit; 

$nombre = $euroval->run(
  'Nombre', //Nombre del campo    						 
  $_POST['nombre'], //Dato a validar
  array('required','alphabetic','min_len,4'), //Validaciones
  array('filter_string')); //Filtros

if(is_array($nombre)){ // Hubo algún error en la validación?
	foreach ($nombre as $value) { 
		echo $value;
	}
}else{ // Los datos validados no cuentan con errores
	echo "El campo nombre es correcto";
}
```
## Validadores y Filtros disponibles
#### Validadores
+ required `Verifica que el campo sea requerido`
+ alpha_numeric `Verifica que los datos sean alfa numéricos solo permite caracteres A-Z,a-z,0-9`
+ alpha_spaces `Campos alfa numericos A-Z, a-z, 0-9 con espacios en blanco`
+ min_len `Valida que un campo cuente con una longitud mínima`
+ max_len `Valida que un campo cuente con una longitud máxima`
+ alphabetic `Acepta solo caracteres alfa A-Z, a-z con espacios en blanco`
+ integer `Acepta solo números enteros 0-9`
+ float `Acepta números enteros y decimales`
+ email `Valida direcciones de email validas ejemplo@dominio.com`
+ date `Valida fechas validas dado un determinado formato como parametro`
+ file_exists `Verifica que el archivo sea requerido`
+ file_validate `Verifica que el archivo sea valido en extención y tamaño, también verifica que no exista ningun tipo de error al validarlo`

#### Filtros
+ filter_string `Sanea una cadena de texto quitando caracteres especiales y entidades html`
+ filter_htmlencode `Codifica entidades html` 
+ filter_email `Remueve caracteres invalidos para direcciones de email`
+ filter_numbers `Remueve caracteres no numéricos`

#### Parametros en validadores
Existen algunos validadores que requieren parametros extra para su funcionamiento, dichos parametros se pasan agregando una coma ```array('min_len,3')``` a continuación se muestran algunos detalles sobre cada uno de ellos:

+ min_len
```php
// Se permite una longitud mínima de 3 caracteres
// ejemplo: 

array('min_len,3') 
```
+ max_len
```php
// Se permite una longitud máxima de 5 caracteres
// ejemplo: 

array('max_len,5') 
```
+ date
```php
/* Se permite varios tipos de formato favor de consultar los formatos en: 
http://php.net/manual/es/datetime.createfromformat.php 
si no se pasa ningun parametro el formato predeterminado será 'Y-m-d H:i:s' */
// ejemplo: 

array('date,Y/m/d') // Se tendrá que pasar una fecha de tipo '2015/01/01'
```
+ file_validate
```php
/* Valida que el archivo cuente con las especificacines requeridas: 

array('file_validate,size,extencion1|extencion2')

> size: Tamaño máximo del archivo expresado en kilobytes, ejemplo: 1024, 2048 ...
> extenciones: Extenciones permitidas para determinado archivo, ejemplo: application/ms_word
*/
// ejemplo: 
// Valida un archivo de peso máximo de 1mb, con extenciones permitidas de archivo word o pdf
array('file_validate,1024,application/msword|application/pdf')
```

## Métodos disponibles
+ run `Método para validar los datos pasando validadores y filtros`
 
#### Valores de retorno y parametros
Función | Parametros | Retornos
--- | --- | ---
*run*| `Nombre del campo (string)`, `Dato(string)`, `Validadores(Array)`, `Filtros(Array)` | `True` o `False`



  

