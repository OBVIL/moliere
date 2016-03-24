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
		'|<anchor([^/]+)?/>|',
		'|</?quote>|',
// 		'|<note.*?</note>|',
		'|</p>|',
		'|</l>|',
		'|<p.*?>|',
		'|<l.*?>|',
		);//reste les cas où le milestone serait enfant de hi...
	$replace = array(
		'',
		'',
// 		'',
		'',
		'',
		'',
		'<lb/>',
		'<lb/>',//les lb, c'est moche
	);
	return preg_replace($remove, $replace, $tei);
}
$bookIds = explode("\n", file_get_contents("corpus.txt"));
include("tei.tpl.php");
?>