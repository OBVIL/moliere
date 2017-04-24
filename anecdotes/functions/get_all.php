<?php
error_reporting(0);

function get_all($db) {
    $sql = "SELECT * FROM books ORDER BY date ASC";
    $books = mselect($sql,$db);
    $sql = "SELECT anecdotes.*, books.date, books.lastname, books.author, books.title AS first_occ_title FROM anecdotes, books WHERE anecdotes.occ_n > 0 AND books.id = anecdotes.first_occ  ORDER BY books.date ASC";
    $anecdotes = mselect($sql, $db);
    //echo "<pre>";print_r($anecdotes);
    return array(
        $books,
        $anecdotes,
    );
}
//18** 2 cas erronés
//largeur first author
//vérifier les js
//vérifier les liens

?>