<?php
function combine($array1){
	$array2 = [];
	$i = 0;
	while($i < count($array1[0])){
		$array2[$i]["id"] = $array1[1][$i][0];
		$array2[$i]["text"] = $array1[2][$i][0];
		$array2[$i]["offset"] = $array1[1][$i][1];
		$i++;
	}
	return $array2;
}
function teiClean($tei) {
	$remove = array(
		'|<[pc]b([^/]+)?/>|',
		'|<anchor([^/]+)?/>|');
	return preg_replace($remove, "", $tei);
}
$bookIds = explode("\n", file_get_contents("corpus.txt"));
include("tei.tpl.php");
?>