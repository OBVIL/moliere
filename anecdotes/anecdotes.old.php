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

$teiHeader='<teiHeader>
    <fileDesc>
      <titleStmt>
        <title>Anecdotes de la vie de Molière</title>
        <principal>Élodie Benard</principal>
      </titleStmt>
      <editionStmt>
        <edition>OBVIL</edition>
      </editionStmt>
    </fileDesc>
  </teiHeader>';
echo '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="../../teipub/xsl/tei2html.xsl"?>
<TEI xmlns="http://www.tei-c.org/ns/1.0" xml:lang="fr">
'.$teiHeader.'
<text>
<body>
';

$bookIds = explode("\n", file_get_contents("corpus.txt"));
$bookIds = array_filter($bookIds, 'strlen');
foreach($files as $bookId){
	$bookPath = '../critique/'.$bookId.'.xml';
	//  $bookId = substr($bookPath, 0, -4);
	echo "<div><head>".$bookId."</head>";
	$book = file_get_contents($bookPath);
	$anecdotePattern = '|<milestone type="anecdoteStart" xml:id="([^"]+)"/>(.*?)<milestone type="anecdoteEnd"/>|';
	$commentPattern = '|<milestone type="commentStart" corresp="#([^"]+)"/>(.*?)<milestone type="commentEnd"/>|';
	$anecdotes = array();
	$comments = array();
	preg_match_all($anecdotePattern, $book, $anecdotes, PREG_OFFSET_CAPTURE); //récupérer offset avec PREG_OFFSET_CAPTURE
	preg_match_all($commentPattern, $book, $comments, PREG_OFFSET_CAPTURE); //récupérer offset avec PREG_OFFSET_CAPTURE

	$anecdotes = combine($anecdotes);
	$comments = combine($comments);

	// echo "<pre>"; print_r($comments);

	//boucler sur les fichiers
	//ordonner les commentaires
	//xmlid
	//link to file

	foreach($anecdotes as $anecdote) {
		echo '<div type="anecdote" xml:id="'.$anecdote["id"].'" copyOf="'.$bookId.'#'.$anecdote["id"].'">'."\n";
		echo "<head>".$anecdote["id"]."</head>\n";
		foreach($comments as $comment) {
			if($comment["id"] == $anecdote["id"] && $comment["offset"]<$anecdote["offset"]) echo "\n<p>".teiClean($comment["text"])."</p>\n";
		}
		echo "<p rend='b'>".teiClean($anecdote["text"])."</p>\n";
		foreach($comments as $comment) {
			if($comment["id"] == $anecdote["id"] && $comment["offset"]>$anecdote["offset"]) echo "\n<p>".teiClean($comment["text"])."</p>\n";
		}
		/*
	foreach($comments as $comment) {
		//if($comment[1] == $anecdote[1]) reprendre ce test à cause de PREG_OFFSET_CAPTURE
		if($comment["id"] == $anecdote["id"]) echo '<div type="comment" corresp="'.$anecdote["id"].'">'."\n<head>commentaire</head>\n<p>".teiClean($comment["text"])."</p>\n</div>\n";
	}
*/
		echo "</div>\n";
	}
	echo "</div>\n";
}
echo "</body>\n</text>\n</TEI>";

?>