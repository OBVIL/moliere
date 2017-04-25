<?php

function search($db) {

    $sql = "SELECT
    occurrences.id AS occurrences_id,
    occurrences.content AS content,
    occurrences.comment_before AS comment_before,
    occurrences.comment_after AS comment_after,
    books.id AS book_id,
    books.date AS date,
    books.author AS author,
    books.lastname AS lastname,
    books.title AS book_title,
    anecdotes.id AS anecdote_id,
    anecdotes.title AS anecdote_title,
    anecdotes.short_title AS anecdote_short_title
    FROM occurrences, books, anecdotes";
    $sql .= " WHERE books.id = occurrences.book AND anecdotes.id = occurrences.anecdote";
    $sql .= " AND occurrences.book IN (SELECT books.id FROM books WHERE books.date > " . $_POST["after"] . " AND books.date < " . $_POST["before"] . ")";
    
    if ($_POST["content"]) {
        
        switch ($_POST["content_type"]) {
            case "anecdote":
                $sql.= " AND content LIKE '%" . $_POST["content"] . "%'";
            break;
            case "commentaire":
                $sql.= " AND (comment_before LIKE '%" . $_POST["content"] . "%' OR comment_after LIKE '%" . $_POST["content"] . "%')";
            break;
            case "les-deux":
                $sql.= " AND (comment_before LIKE '%" . $_POST["content"] . "%' OR comment_after LIKE '%" . $_POST["content"] . "%' OR content LIKE '%" . $_POST["content"] . "%')";
            break;
        }
    }
    
    if (isset($_POST["anecdotes"])) {
        $sql.= " AND anecdote IN ('" . implode("','",$_POST["anecdotes"]) . "')";
    }
    
    if (isset($_POST["books"])) {
        $sql.= " AND book IN ('" . implode("','",$_POST["books"]) . "')";
    }
    
    if (isset($_POST["keywords"])) {
        $sql.= " AND anecdote IN (SELECT anecdote FROM keywords_anecdotes WHERE keyword IN ('" . implode("','",$_POST["keywords"]) . "'))";
    }
    $results = mselect($sql, $db);
    return $results;
//totalement vulnérable aux injections !
}
?>