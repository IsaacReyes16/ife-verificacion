<?php
require('common/php/conex.php');
$sql="select * from tbl_personal";
echo SQLQuery($sql,1);
?>