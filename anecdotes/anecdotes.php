<?php
$bookPath = 'auger_introduction-oeuvres.xml';
$bookId = substr($bookPath, 0, -4);
$book = file_get_contents($bookPath);
$anecdotePattern = '|<milestone type="anecdoteStart" xml:id="([^"]+)"/>(.*?)<milestone type="anecdoteEnd"/>|';
$commentPattern = '|<milestone type="commentStart" corresp="#([^"]+)"/>(.*?)<milestone type="commentEnd"/>|';
$anecdotes = array();
$comments = array();
preg_match_all($anecdotePattern, $book, $anecdotes, PREG_SET_ORDER); //récupérer offset avec PREG_OFFSET_CAPTURE
preg_match_all($commentPattern, $book, $comments, PREG_SET_ORDER); //récupérer offset avec PREG_OFFSET_CAPTURE

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

function teiClean($tei) {
  $remove = array(
    '|<[pc]b([^/]+)?/>|',
    '|<anchor([^/]+)?/>|');
  return preg_replace($remove, "", $tei);
}

foreach($anecdotes as $anecdote) {
  echo '<div type="anecdote" xml:id="'.$anecdote[1].'" copyOf="'.$bookId.'#'.$anecdote[1].'">'."\n";
  echo "<head>$anecdote[1]</head>\n";
  echo "<p>".teiClean($anecdote[2])."</p>\n";
  foreach($comments as $comment) {
    //if($comment[1] == $anecdote[1]) reprendre ce test à cause de PREG_OFFSET_CAPTURE
    if($comment[1] == $anecdote[1]) echo '<div type="comment" corresp="'.$anecdote[1].'">'."\n<head>commentaire</head>\n<p>".teiClean($comment[2])."</p>\n</div>\n";
  }
  echo "</div>\n";
}
echo "</body>\n</text>\n</TEI>";

?>