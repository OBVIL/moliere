<?php
error_reporting(0);

//prend les fichiers TEI listés dans corpus.txt et regénère books.xml (regex). id : book + book_anecdote

//prend les fichiers TEI listés dans corpus.txt et regénère anecdotes.xml (regex). id : anecdote + book_anecdote


//prend anecdotes.xml et regénère anecdotes.rng (xslt)


//insère les xml:id dans tei : book_anecdote


function combine($array1) {


    //echo "<pre>";print_r($array1);
    
    //echo count($array1[0]);

    $array2 = array();
    $i = 0;
    while ($i < count($array1[0])) {
        $array2[$i]["id"] = $array1[1][$i][0];
        $array2[$i]["text"] = $array1[2][$i][0];
        $array2[$i]["offset"] = $array1[1][$i][1];
        $i++;
    }

    //echo "<pre>";print_r($array2);
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
    ); //reste les cas où le milestone serait enfant de hi...

    $replace = array(
        '',
        '',

        //   '',
        '',
        '',
        '',
        '<lb/>',
        '<lb/>', //les lb, c'est moche

        
    );
    return preg_replace($remove, $replace, $tei);
}

//$references = new DOMDocument;

//$references -> load("references.xml");

$references = file_get_contents("references.xml");
$bookIds = explode("\n", file_get_contents("corpus.txt"));
$array1 = array();
$array2 = array();

//auteur, titre, date
foreach ($bookIds as $bookId) {
    //$bookPath = '../critique/' . trim($bookId).'.xml';
    $bookPath = trim($bookId).'.xml';
    if (!is_file($bookPath)) {
        continue;
    }
    $book = new DOMDocument();
    $book->load($bookPath);
    $bookTitle = $book->getElementsByTagName("title");
    $bookTitle = $bookTitle->length > 0 ? $bookTitle->item(0)->textContent : '';
    $bookAuthor = $book->getElementsByTagName("author");
    $bookAuthor = $bookAuthor->length > 0 ? $bookAuthor->item(0)->getAttribute("key") : 'Anonyme';
    $bookAuthorLastName = strpos($bookAuthor, ",") ? substr($bookAuthor, 0, strpos($bookAuthor, ",")) : substr($bookAuthor, 0, strpos($bookAuthor, "("));
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
    $i = 0;
    foreach ($anecdotes as $anecdote) {
        $commentBefore = "";
        $commentAfter = "";
        $pattern = '|<div type="anecdote" xml:id="' . $anecdote["id"] . '">[\s\S]*?<head>(.+?)</head>[\s\S]*?</div>|';
        preg_match_all($pattern, $references, $anecdoteTitle);
        $search = array(
            '<hi rend="i">',
            '</hi>'
        );
        $replace = array(
            '<i>',
            '</i>'
        );
        $anecdoteTitle = str_replace($search, $replace, $anecdoteTitle[1][0]);
        //echo $anecdoteTitle[1][0];
        //transform("<p>".$anecdoteTitle[1][0]."<p>");
        //$anecdoteTitle = transform($anecdoteTitle[1][0]);

        //$anecdoteTitle = $references->getElementById($anecdote["id"])->textContent;
        
        if (strlen($anecdoteTitle) > 50) {
            $anecdoteShortTitle = substr($anecdoteTitle, 0, 50);
            $anecdoteShortTitle = substr($anecdoteShortTitle, 0, strrpos($anecdoteShortTitle, ' ')) . " [&hellip;]";
        } else {
            $anecdoteShortTitle = $anecdoteTitle;
        }
        $array2[$anecdote["id"]]["title"] = $anecdoteTitle;
        $array2[$anecdote["id"]]["short-title"] = $anecdoteShortTitle;
        foreach ($comments as $comment) {
            
            if (strpos($comment["id"], $anecdote["id"]) && $comment["offset"] < $anecdote["offset"]) {
                $commentBefore = '<p>' . teiClean($comment["text"]) . '</p>';

                //$commentBefore = teiClean($comment["text"]);
                
            }
        }

        //$text = teiClean($anecdote["text"]);
        $text = '<p>' . teiClean($anecdote["text"]) . '</p>';
        foreach ($comments as $comment) {
            
            if (strpos($comment["id"], $anecdote["id"]) && $comment["offset"] > $anecdote["offset"]) {
                $commentAfter = '<p>' . teiClean($comment["text"]) . '</p>';

                //$commentAfter = teiClean($comment["text"]);
                
            }
        }
        $array2[$anecdote["id"]]["books"][$bookId]["content"] = transform($text);
        
        if ($array2[$anecdote["id"]]["books"][$bookId]["content"]) {
            $i++;
        }
        $array2[$anecdote["id"]]["books"][$bookId]["commentBefore"] = transform($commentBefore);
        $array2[$anecdote["id"]]["books"][$bookId]["commentAfter"] = transform($commentAfter);
    }
    $array1[$bookId] = array(
        "title" => $bookTitle,
        "author" => $bookAuthor,
        "authorLastName" => $bookAuthorLastName,
        "date" => $bookDate,
        "anecdotesCount" => $i
    );
}

$books = $array1;
$anecdotes = $array2;
usort($books, 'sort_by_order ');

//classer books par année décroissante
foreach($books as $bookId=>$book){
    foreach($anecdotes as $anecdoteId => $anecdote){
        foreach($anecdote["books"] as $key => $value){
            if($key==$bookId and !isset($anecdotes[$anecdoteId]["first"]["date"])){
                $anecdotes[$anecdoteId]["first"]["date"] = $book["date"];
                $anecdotes[$anecdoteId]["first"]["authorLastName"] = $book["authorLastName"];
                $anecdotes[$anecdoteId]["first"]["author"] = $book["author"];
                $anecdotes[$anecdoteId]["first"]["title"] = $book["title"];
            }
        }
    }
}
//echo "<pre>";print_r($anecdotes);
include ("anecdotes.tpl.php");
function sort_by_order ($a, $b)
{
    return $a['date'] - $b['date'];
}
function transform($xml) {
    //var_dump($xml);
    $dom = new DOMDocument();
    $dom->loadXML($xml);
    //var_dump($dom);
    $xslt = new DOMDocument();
    $xslt->load("anecdotes.xsl");
    $proc = new XSLTProcessor();
    $proc->importStyleSheet($xslt);
    $html = $proc->transformToXML($dom);
//echo $html;
    return $html;
}
