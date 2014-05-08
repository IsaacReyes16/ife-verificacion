<?php session_start();

if (!isset($_SESSION['id_usu'])) { header("Location: ../../sdir_obsln_ver/index.php"); exit; }

/*
$host = "localhost";
$user = "root";
$pass = "ife";
$db = "obs_ln2014_1428";
*/

$host = "localhost";
$user = "root";
$pass = "";
$db = "ife_dom_irre_20140506_1";

$conn = mysql_pconnect($host, $user, $pass) or die("Error de conexión con servidor ".mysql_error());
mysql_select_db($db, $conn);
mysql_query ("SET NAMES 'utf8'");

setlocale(LC_TIME,"es_ES");
include_once('funciones.php');
?>
