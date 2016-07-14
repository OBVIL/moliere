<?php

//prend les fichiers TEI listés dans corpus.txt et regénère books.xml (regex). id : book + book_anecdote

//prend les fichiers TEI listés dans corpus.txt et regénère anecdotes.xml (regex). id : anecdote + book_anecdote


//prend anecdotes.xml et regénère anecdotes.rng (xslt)


//insère les xml:id dans tei : book_anecdote


function combine($array1) {

    $array2 = array();
    $i = 0;
    while ($i < count($array1[0])) {
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
        '|</?label>|',

        //   '|<note.*?</note>|',
        '|</p>|',
        '|</l>|',
        '|<p.*?>|',
        '|<l.*?>|',
    ); //reste les cas où le milestone serait enfant de hi...

    $replace = array(
        '',
        '',

        //   '',
        '',
        '',
        '',
        '',
        '<lb/>',
        '<lb/>', //les lb, c'est moche

        
    );
    return preg_replace($remove, $replace, $tei);
}
$references = new DOMDocument;
$references -> load("references.xml");
$bookIds = explode("\n", file_get_contents("corpus.txt"));
$start = '<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet type="text/xsl" href="../../teipub/xsl/tei2html.xsl"?><TEI xml:lang="fr" xmlns="http://www.tei-c.org/ns/1.0"><teiHeader><fileDesc><titleStmt><title>Anecdotes de la vie de Molière</title><principal>Élodie Bénard</principal></titleStmt><editionStmt><edition>OBVIL</edition></editionStmt></fileDesc></teiHeader><text><body>';
$end = '</body></text></TEI>';
$xml = $start;
$array = array();

//auteur, titre, date
foreach ($bookIds as $bookId) {
    $bookPath = '../critique/' . $bookId . '.xml';
    
    if (!is_file($bookPath)) {
        continue;
    }
    echo $bookId;
    $book = new DOMDocument();
    $book->load($bookPath);
    $bookTitle = $book->getElementsByTagName("title");
    $bookTitle = $bookTitle->length > 0 ? $bookTitle->item(0)->textContent : '';
    $bookAuthor = $book->getElementsByTagName("author");
    $bookAuthor = $bookAuthor->length > 0 ? $bookAuthor->item(0)->textContent : '';
    $bookDate = $book->getElementsByTagName("creation");
    $bookDate = $bookDate->length > 0 ? $bookDate->item(0)->getElementsByTagName("date") : false;
    $bookDate = $bookDate->length > 0 ? $bookDate->item(0)->getAttribute("when") : '';
    $book = file_get_contents($bookPath);
    $anecdotePattern = '|<milestone type="[Aa]necdoteStart" xml:id="([^"]+)"/>([\s\S]*?)<milestone type="[Aa]necdoteEnd"/>|';
    $commentPattern = '|<milestone type="[Cc]ommentStart" corresp="([^"]+)"/>([\s\S]*?)<milestone type="[Cc]ommentEnd"/>|';
    $anecdotes = array();
    $comments = array();
    
    if (!preg_match_all($anecdotePattern, $book, $anecdotes, PREG_OFFSET_CAPTURE)) {
        continue;
    }
    preg_match_all($commentPattern, $book, $comments, PREG_OFFSET_CAPTURE);

    $anecdotes = combine($anecdotes);
    $comments = combine($comments);
    $xml.= '<div type="book" xml:id="' . $bookId . '">';
    $bookHead = '<head>' . $bookAuthor . ', <ref target="../critique/' . $bookId . '">' . $bookTitle . '</ref>,' . $bookDate . '</head>';
    $xml.=$bookHead;
    //lien vers le texte de l'anecdote : transformer les <milestones@xml:id/> en <???@id/> dans tei2html
    foreach ($anecdotes as $anecdote) {
        $commentBefore = $commentAfter = "";
        $anecdoteTitle = $references->getElementById($anecdote["id"])->textContent;
        $xml.= '<div type="anecdote" copyOf="#' . $anecdote["id"] . '" xml:id="' . $bookId . '_' . $anecdote["id"] . '"><head><ref target="../critique/' . $bookId . '#' . $anecdote["id"] . '">' . $anecdoteTitle . '</ref></head>';
        $array[$anecdote["id"]]["title"] = $anecdoteTitle;
        foreach ($comments as $comment) {
            
            if (strpos($comment["id"], $anecdote["id"]) && $comment["offset"] < $anecdote["offset"]) {
                $commentBefore = '<p type="comment">' . teiClean($comment["text"]) . '</p>';
                $xml.= $commentBefore;
            }
        }
        $text = '<p type ="text" rend="b">' . teiClean($anecdote["text"]) . '</p>';
        $xml.= $text;
        foreach ($comments as $comment) {
            
            if (strpos($comment["id"], $anecdote["id"]) && $comment["offset"] > $anecdote["offset"]) {
                $commentAfter = '<p type="comment">' . teiClean($comment["text"]) . '</p>';
                $xml.= $commentAfter;
            }
        }
        $xml.= '</div>';
        $array[$anecdote["id"]]["books"][$bookId]["title"] = $bookHead;
        $array[$anecdote["id"]]["books"][$bookId]["content"] = $commentBefore . $text . $commentAfter;
    }
    $xml.= '</div>';
}
$xml.= $end;
file_put_contents('books.xml', $xml);

$anecdotes = $array;
echo "<pre>"; print_r($anecdotes);
$xml = $start;
foreach ($anecdotes as $anecdoteId => $anecdote){
    $xml.='<div type="anecdote" xml:id="'.$anecdoteId.'"><head>'.$anecdote["title"].'</head>';
    foreach ($anecdote["books"] as $bookId => $book){
        $xml.='<div type="book" xml:id="'.$bookId.'_'.$anecdoteId.'">'.$book["title"].$book["content"].'</div>';
    }
    $xml.='</div>';
}
$xml.= $end;
file_put_contents('anecdotes.xml', $xml);

$xml = '<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="../xrem/rng2html.xsl"?>
<grammar xmlns="http://relaxng.org/ns/structure/1.0" 
  xmlns:a="http://relaxng.org/ns/compatibility/annotations/1.0" 
  xmlns:h="http://www.w3.org/1999/xhtml" 
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  datatypeLibrary="http://www.w3.org/2001/XMLSchema-datatypes" 
  ns="http://www.tei-c.org/ns/1.0" 
  xml:lang="fr">
  
  <!--
    SURCHARGE TEIBOOK
    http://relaxng.org/tutorial-20011203.html#IDAVEZR
  -->
  <include
    href="../../teibook/teibook.rng"
    h:href="../../teibook/teibook.rng" 
    h:title="Schéma Teibook">
    
    <define name="milestone">
      <element name="milestone">
        <a:documentation>Borne anonyme, vide, permet d’étiqueter des points qui n’entrent pas dans la hiérarchie éditoriale, et qui ne sont pas des sauts de page <a:el>pb</a:el>, de colonne <a:el>cb</a:el> ou de ligne <a:el>lb</a:el></a:documentation>
        <empty/>
        <choice>
          <!-- anecdoteStart -->
          <group>
            <attribute name="type">
              <value>anecdoteStart</value>
              <a:documentation>Début d’une anecdote</a:documentation>
            </attribute>
            <attribute name="xml:id">
              <choice>';
$anecdotes = new DOMDocument();
$anecdotes -> load("references.xml");
$anecdotes = $anecdotes->getElementsByTagName("div");
foreach($anecdotes as $anecdote){
    $xml .= '<value>'.$anecdote->getAttribute("xml:id").'</value>';
    $xml .= '<a:documentation>'.$anecdote->getElementsByTagName("head")->item(0)->textContent.'</a:documentation>';
}
$xml .= ' </choice>
            </attribute>
          </group>
          <!-- commentStart -->
          <group>
            <attribute name="type">
              <value>commentStart</value>
            </attribute>
            <attribute name="corresp">
              <choice>';
  foreach($anecdotes as $anecdote){
    $xml .= '<value>#'.$anecdote->getAttribute("xml:id").'</value>';
}
$xml .= '</choice>
            </attribute>
          </group>
          <!-- anecdoteEnd | commentEnd -->
          <attribute name="type">
            <choice>
              <value>anecdoteEnd</value>
              <a:documentation>Fin d’une anecdote</a:documentation>
              <value>commentEnd</value>
              <a:documentation>Fin du commentaire</a:documentation>
            </choice>
          </attribute>
        </choice>
      </element>
    </define>
    
  </include>
  
  <!--
    SPÉCIFIQUE ANECDOTES
  -->
  
  
</grammar>';
file_put_contents('anecdotes.rng', $xml);
