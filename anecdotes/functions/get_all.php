<?php
error_reporting(0);

function get_all($db) {
    $sql = "SELECT * FROM books";
    $books = mselect($sql,$db);
    $array1 = array();
    $array2 = array();

    //auteur, titre, date
    foreach ($bookIds as $bookId) {

        foreach ($anecdotes as $anecdote) {
            $array2[$anecdote["id"]]["title"] = $anecdoteTitle;
            $array2[$anecdote["id"]]["short-title"] = $anecdoteShortTitle;
            $array2[$anecdote["id"]]["books"][$bookId]["content"] = transform($text);           
            $array2[$anecdote["id"]]["books"][$bookId]["commentBefore"] = transform($commentBefore);
            $array2[$anecdote["id"]]["books"][$bookId]["commentAfter"] = transform($commentAfter);
        }
        $array1[$bookId] = array(
            "title" => $bookTitle,
            "url" => $bookPath,
            "author" => $bookAuthor,
            "authorLastName" => $bookAuthorLastName,
            "date" => $bookDate,
            "anecdotesCount" => $i
        );
    }
    $books = $array1;
    $anecdotes = $array2;
    return array(
        $books,
        $anecdotes
    );
}

//echo "<pre>";print_r($anecdotes);

?>