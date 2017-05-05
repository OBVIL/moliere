<?php
error_reporting(0);
include ("functions/functions.php");
include ("functions/db.php");
$db = connect();
$sql = "DELETE FROM anecdotes";
insert($sql, $db);
$sql = "DELETE FROM books";
insert($sql, $db);
$sql = "DELETE FROM occurrences";
insert($sql, $db);
$sql = "DELETE FROM keywords";
insert($sql, $db);
$sql = "DELETE FROM keywords_anecdotes";
insert($sql, $db);
$references = new DOMDocument("1.0", "UTF-8");
$references->load("data/references.xml");
$keywords_elt = $references->getElementsByTagName("keywords")->item(0);
$terms = $keywords_elt->getElementsByTagName("term");
$keywords_old = array();
foreach ($terms as $keyword) {
    $data = array();
    $data[] = $keyword_id = $keywords_old[] = $keyword->getAttribute("xml:id");
    $data[] = $keyword_name = $keyword->textContent;
    $sql = "INSERT INTO keywords (
        id,
        name
    ) VALUES (
        ?,
        ?
    )";
    insert($sql, $db, $data);
}
$anecdotes = $references->getElementsByTagName("div"); //il faudrait faire un xpath, vérifier le type, la présence d'un id et d'un contenu

$array = array();
$keywords_new = array();
foreach ($anecdotes as $anecdote) {
    $data = array();
    $data[] = $anecdote_id = $anecdote->getAttribute("xml:id");
    $title = title_transform($anecdote->getElementsByTagName("head")->item(0));
    $data[] = $array[$anecdote_id]["title"] = $title;
    
    if (strlen($title) > 50) {
        $short_title = substr($title, 0, 50);
        $data[] = $short_title = substr($short_title, 0, strrpos($short_title, ' ')) . " [&hellip;]";
    } else {
        $data[] = $short_title = $title;
    }
    $note = $anecdote->getElementsByTagName("note");
    $data[] = $array[$anecdote_id]["note"] = $note = ($note->length > 0) ? title_transform($note->item(0)) : "";
    $data[] = 0;
    $data[] = NULL;

    //anecdotes
    $sql = "INSERT INTO anecdotes (
        id,
        title,
        short_title,
        note,
        occ_n,
        first_occ
    ) VALUES (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?
    )";
    insert($sql, $db, $data);

    //tags
    $keywords = $anecdote->getAttribute("ana");
    
    if (!$keywords) {
        continue;
    }
    $keywords = explode(" ", $keywords);
    foreach ($keywords as $keyword) {

        //echo "<pre>"; print_r($keyword);print_r($anecdote_id);
        $data = array();
        $data[] = $keyword_id = substr($keyword, 1);
        $keywords_new[$keyword_id] = "";
        $data[] = $anecdote_id;
        $sql = "INSERT INTO keywords_anecdotes (
            keyword,
            anecdote
        ) VALUES (
            ?,
            ?
        )";
        insert($sql, $db, $data);
    }
}

//insérer les keywords manquants
foreach ($keywords_new as $keyword_id => $value) {
    
    if (in_array($keyword_id, $keywords_old)) {
        continue;
    }
    $term = $references->createElement("term");
    $term->setAttribute("xml:id", $keyword_id);
    $keywords_elt->appendChild($term);
}
$xml = $references->saveXML();
file_put_contents("data/references.xml", $xml);
$anecdotes = $array;
ob_start();
include ("tpl/schema.tpl.php");
$schema = ob_get_clean();
file_put_contents("anecdotes.rng", $schema);
$books = explode("\n", file_get_contents("data/corpus.txt"));
$array = array();
$dates = array();

//auteur, titre, date
foreach ($books as $book) {
    $data = array();
    $data[] = $path = trim($book) . '.xml';
    
    if (!is_file($path)) {
        continue;
    }
    $data[] = $book_id = trim(substr($book, strpos($book, "critique/") + 9));
    $book = new DOMDocument();
    $book->load($path);
    $title = $book->getElementsByTagName("title");
    $data[] = $title = $title->length > 0 ? $title->item(0)->textContent : '';
    $author = $book->getElementsByTagName("author");
    $data[] = $author = $author->length > 0 ? $author->item(0)->getAttribute("key") : 'Anonyme';
    $lastname = strpos($author, ",") ? substr($author, 0, strpos($author, ",")) : $author;
    $data[] = $lastname = strpos($lastname, "(") ? substr($lastname, 0, strpos($lastname, "(")) : $lastname;
    $date = $book->getElementsByTagName("creation");
    $date = $date->length > 0 ? $date->item(0)->getElementsByTagName("date") : false;
    $data[] = $date = $date->length > 0 ? $date->item(0)->getAttribute("when") : '';
    $data[] = 0;
    $sql = "INSERT INTO books (
        path,
        id,
        title,
        author,
        lastname,
        date,
        occ_n
    ) VALUES (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?
    )";
    insert($sql, $db, $data);
    $book = file_get_contents($path);
    $anecdotePattern = '|<milestone type="[Aa]necdoteStart" xml:id="([^"]+)" ?/>([\s\S]*?)<milestone type="[Aa]necdoteEnd" ?/>|';
    $commentPattern = '|<milestone type="[Cc]ommentStart" corresp="#([^"]+)" ?/>([\s\S]*?)<milestone type="[Cc]ommentEnd" ?/>|';
    $anecdotes = array();
    $comments = array();
    
    if (!preg_match_all($anecdotePattern, $book, $anecdotes, PREG_OFFSET_CAPTURE)) {
        continue;
    }
    preg_match_all($commentPattern, $book, $comments, PREG_OFFSET_CAPTURE);
    $anecdotes = combine($anecdotes);
    $comments = combine($comments);
    foreach ($anecdotes as $anecdote) {
        $data = array();
        $data[] = $book_id . "_" . $anecdote["id"];
        $data[] = $anecdote["id"];
        $data[] = $book_id;
        
        if (!isset($dates[$anecdote["id"]])) {
            $dates[$anecdote["id"]]["book"] = $book_id;
            $dates[$anecdote["id"]]["date"] = $date;
        } else {
            
            if ($dates[$anecdote["id"]]["date"] > $date) {
                $dates[$anecdote["id"]]["book"] = $book_id;
                $dates[$anecdote["id"]]["date"] = $date;
            }
        }
        $comment_before = "";
        $comment_after = "";
        foreach ($comments as $comment) {
            
            if (($comment["id"] == $anecdote["id"]) && ($comment["offset"] < $anecdote["offset"])) {
                $comment_before = '<p>' . teiClean($comment["text"]) . '</p>';
            }
        }
        $text = '<p>' . teiClean($anecdote["text"]) . '</p>';
        foreach ($comments as $comment) {
            
            if (($comment["id"] == $anecdote["id"]) && ($comment["offset"] > $anecdote["offset"])) {
                $comment_after = '<p>' . teiClean($comment["text"]) . '</p>';
            }
        }
        $data[] = $comment_before = $comment_before ? transform($comment_before) : "";
        $data[] = $content = transform($text);
        $data[] = $comment_after = $comment_after ? transform($comment_after) : "";
        $data[] = html_txt($comment_before);
        $data[] = html_txt($content);
        $data[] = html_txt($comment_after);
        $sql = "INSERT INTO occurrences (
            id,
            anecdote,
            book,
            comment_before,
            content,
            comment_after,
            comment_before_txt,
            content_txt,
            comment_after_txt
            
        ) VALUES (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )";
        insert($sql, $db, $data);
        $sql = "UPDATE anecdotes SET occ_n = occ_n +1 WHERE id = '" . $anecdote["id"] . "'";
        insert($sql, $db);

        //$sql = "UPDATE anecdotes SET first_occ = ".$date .", first_occ_lastname = '".$lastname."', first_occ_title= '".$title."', first_occ_author ='".$author."' WHERE id = '" . $anecdote["id"] . "' AND first_occ > " . $date;
        
        //$sql = "UPDATE anecdotes SET anecdotes.first_occ = '".$book_id."' WHERE anecdotes.id = '".$anecdote["id"]."' AND

        
        //((SELECT books.date FROM books WHERE books.id = anecdotes.first_occ) > ".$date.")";

        $sql = "UPDATE anecdotes SET first_occ = '" . $dates[$anecdote["id"]]["book"] . "' WHERE id = '" . $anecdote["id"] . "'";
        insert($sql, $db);
        $sql = "UPDATE books SET occ_n = occ_n +1 WHERE id = '" . $book_id . "'";
        insert($sql, $db);
    }
}
//$sql = "UPDATE anecdotes SET first_occ = NULL, first_occ_lastname = NULL, first_occ_title = NULL, first_occ_author = NULL WHERE first_occ = 3000";
//insert($sql, $db);
$string = "";
$sql = "SELECT id, lastname, title FROM books ORDER BY lastname ASC";
$options = mselect($sql, $db);
foreach ($options as $option) {
    $string.= '<option value="' . $option["id"] . '">' . $option["lastname"] . ', ' . $option["title"] . '</option>';
}
file_put_contents("tpl/books.html", $string);
$string = "";
$sql = "SELECT id, title FROM anecdotes ORDER BY title ASC";
$options = mselect($sql, $db);
foreach ($options as $option) {
    $string.= '<option value="' . $option["id"] . '">' . $option["title"] . '</option>';
}
file_put_contents("tpl/anecdotes.html", $string);
$string = "";
$sql = "SELECT id, name FROM keywords ORDER BY id ASC";
$options = mselect($sql, $db);
foreach ($options as $option) {
    $name = $option["name"] ? $option["name"] : $option["id"];
    $string.= '<option value="' . $option["id"] . '">' . $name . '</option>';
}
file_put_contents("tpl/keywords.html", $string);
print_r($dates);
//toto
//$options .= '<option value='.$book_id.''.$title.'>';

//form


//book++


//anecdote++


//années


//first occ


//tags++


//contenu tout


//comment


//main


//titres manquants


//tableau


//générer le formulaire options : titre, tags, anecdotes


//rng


//valider les ana ?


?>