<?php
//prend les fichiers TEI listés dans corpus.txt et regénère books.xml (regex). id : book + book_anecdote
//prend les fichiers TEI listés dans corpus.txt et regénère anecdotes.xml (regex). id : anecdote + book_anecdote
//prend anecdotes.xml et regénère anecdotes.rng (xslt)
//insère les xml:id dans tei : book_anecdote
function combine($array1){
	$array2 = array();
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
		//   '|<note.*?</note>|',
		'|</p>|',
		'|</l>|',
		'|<p.*?>|',
		'|<l.*?>|',
	);//reste les cas où le milestone serait enfant de hi...
	$replace = array(
		'',
		'',
		//   '',
		'',
		'',
		'',
		'<lb/>',
		'<lb/>',//les lb, c'est moche
	);
	return preg_replace($remove, $replace, $tei);
}
$bookIds = explode("\n", file_get_contents("corpus.txt"));
$xml = '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="../../teipub/xsl/tei2html.xsl"?>
<TEI xml:lang="fr" xmlns="http://www.tei-c.org/ns/1.0">
  <teiHeader>
    <fileDesc>
      <titleStmt>
        <title>Anecdotes de la vie de Molière</title>
        <principal>Élodie Bénard</principal>
      </titleStmt>
      <editionStmt>
        <edition>OBVIL</edition>
      </editionStmt>
    </fileDesc>
  </teiHeader>
  <text>
    <body>';
//auteur, titre, date
foreach($bookIds as $bookId){
	$bookPath = '../critique/'.$bookId.'.xml';
	if(!is_file($bookPath)){continue;}
	$book = new DOMDocument();
	$book->load($bookPath);
	$bookTitle = $book->getElementsByTagName("title");
	$bookTitle = $bookTitle->length>0 ? $bookTitle->item(0)->textContent : '';

	$bookAuthor = $book->getElementsByTagName("author");
	$bookAuthor = $bookAuthor->length>0 ? $bookAuthor->item(0)->textContent : '';

	$bookDate = $book->getElementsByTagName("creation");
	$bookDate = $bookDate->length>0 ? $bookDate->item(0)->getElementsByTagName("date") : false;
	$bookDate = $bookDate->length>0 ? $bookDate->item(0)->getAttribute("when") : '';

	$book = file_get_contents($bookPath);
	$anecdotePattern = '|<milestone type="[Aa]necdoteStart" xml:id="([^"]+)"/>([\s\S]*?)<milestone type="[Aa]necdoteEnd"/>|';
	$commentPattern = '|<milestone type="[Cc]ommentStart" corresp="([^"]+)"/>([\s\S]*?)<milestone type="[Cc]ommentEnd"/>|';
	$anecdotes = array();
	$comments = array();
	if(!preg_match_all($anecdotePattern, $book, $anecdotes, PREG_OFFSET_CAPTURE)){continue;}
	preg_match_all($commentPattern, $book, $comments, PREG_OFFSET_CAPTURE);

	$anecdotes = combine($anecdotes);
	$comments = combine($comments);
	$xml .='<div type="book" xml:id="'.$bookId.'">
		<head>'.$bookAuthor.', <ref target="../critique/'.$bookId.'">'.$bookTitle.'</ref>,'.$bookDate.'</head>';

	//lien vers le texte de l'anecdote : transformer les <milestones@xml:id/> en <???@id/> dans tei2html
	foreach($anecdotes as $anecdote) {
		$xml.='<div type="anecdote" copyOf="#'.$anecdote["id"].'" xml:id="'.$bookId.'_'.$anecdote["id"].'">
	<head>
		<ref target="../critique/'.$bookId.'#'.$anecdote["id"].'">'.$anecdote["id"].'</ref>
	</head>';
		foreach($comments as $comment) {
			if(strpos($comment["id"],$anecdote["id"]) && $comment["offset"]<$anecdote["offset"]) {
				$xml.='<p>'.teiClean($comment["text"]).'</p>';
			}
		}
		$xml.='<p rend="b">'.teiClean($anecdote["text"]).'</p>';
		foreach($comments as $comment) {
			if(strpos($comment["id"],$anecdote["id"]) && $comment["offset"]>$anecdote["offset"]) {
				$xml.='<p>'.teiClean($comment["text"]).'</p>';
			}
		}
		$xml.='</div>';
	}
	$xml.='</div>';
}
$xml .='</body>
  </text>
</TEI>';
file_put_contents('books.xml', $xml);

