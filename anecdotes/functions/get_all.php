<?php
error_reporting(0);

function get_all($db) {
    $sql = "SELECT * FROM books ORDER BY date ASC";
    $books = mselect($sql,$db);
    $sql = "SELECT * FROM anecdotes WHERE occ_n > 0 ORDER BY first_occ ASC";
    $anecdotes = mselect($sql, $db);
    return array(
        $books,
        $anecdotes,
    );
}
//first_occ
//18**
//largeur first author
//vérifier les js
//vérifier les liens
?>