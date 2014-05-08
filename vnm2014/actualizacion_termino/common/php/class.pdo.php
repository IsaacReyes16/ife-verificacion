<?php
/**
*   Conexión a PHP-MySQL usando PDO - Clases POO
*   @author Oscar Maldonado
*   O3M
*/
##Includes
require_once('o3m_functions.php');
date_default_timezone_set("America/Mexico_City");
###Conexión Data###
class db {
	private $host;
	private $user;
	private $pass;
	private $database;
	private $link;

	public function __construct(){
		$this->host='localhost';
		$this->user='root';
		$this->pass='';
		$this->database='ife_vnm2014';
	}

	private function SQLConn(){
	//Crea conexión con base de datos	    
	    try{
	    //Validación de conexión vía PDO
	        //Realiza la conexión con un objeto tipo PDO
	        $this->link = new PDO('mysql:host='.$this->host.';dbname='.$this->database, $this->user, $this->pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); //Driver de conexión
	        $this->link->setAttribute(PDO::ATTR_PERSISTENT, false);
	        $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Manejo de errores a mostrar
	        $this->link->setAttribute(PDO::ERRMODE_WARNING, true); //Warnings a mostrar
	        $this->link->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true); //Usa el buffer del server MysQL        			
	        
	        return true;
	    }catch(PDOException $e){
	        //Se atrapa y se envía error, si este existe.
	        echo 'ERROR: No puede conectarse con la base de datos';
	        echo "ERROR: " . $e->getMessage();
	        $this->link = null;
	        return false;
	        exit();
	    }
	}

	private function resultadoSQL($SQL, $TipoSQL){
	//Ejecuta query SQL
		try{
	    	$this->SQLConn(); //Llama conexión
	    	// $Result = $this->link->query($SQL); //Ejecuta query
	    	$query = $this->link->prepare($SQL); //Prepara query
			$query->execute(); //Ejecuta query
			$Result = ($TipoSQL=='SELECT')? $query->fetchAll() : 1; //Regresa resultados de query en un array
			// $Result = $query->fetchAll(); //Regresa resultados de query en un array
	    	$this->cierraConexion(); //Cierra conexión (destruye objeto)  	
	    	return $Result;
	    }catch(PDOException $e){
	    	echo  "ERROR: La consulta SQL esta vacía o tiene errores: ".$SQL;
	    	echo $e->getMessage();
	    	return false;
	    }
	}

	private function cierraConexion(){
		$this->link = null;
	}

	public function SQLQuery($SQL){
	//Ejecuta consultas de extracción
		$Cmd=array('SELECT');
		$vSql=explode(' ',$SQL);
		$vSQL=strtoupper(trim($vSql[0]));
		if(in_array($vSQL,$Cmd)){
	    	$Result = $this->resultadoSQL($SQL, $vSQL);
	    	return $Result;
		}else{
			echo "ERROR: La consulta es erronea.";
			return false;
		}
	}

	public function SQLDo($SQL){
	//Ejecuta consultas de modificación
		$Cmd=array('INSERT', 'UPDATE', 'DELETE');
		$vSql=explode(' ',$SQL);
		$vSQL=strtoupper(trim($vSql[0]));
		if(in_array($vSQL,$Cmd)){
	    	$Result = $this->resultadoSQL($SQL, $vSQL);  	
	    	return true;
		}else{
			echo "ERROR: La consulta es erronea.";
			return false;
		}
	}
}
/*O3M*/
?>