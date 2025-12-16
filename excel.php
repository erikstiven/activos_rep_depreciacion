<?php
require("_Ajax.comun.php");
if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

$sHtml_cab=$_SESSION['ACT_REP_DEPR'];
//$sHtml_det=$_SESSION['sHtml_det'];

header("Pragma: public");
header("Expires: 0");
$filename = "excel.xls";
header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

echo $sHtml_cab;
//unset($_SESSION['sHtml_cab']);
//echo $sHtml_det;
//unset($_SESSION['sHtml_det']);
?>
