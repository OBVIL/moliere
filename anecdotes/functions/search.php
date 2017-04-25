<?php

function search($db) {

    $sql = "SELECT id FROM occurrences";
    $sql.= " WHERE book IN (SELECT id FROM books WHERE date > " . $_POST["after"] . " AND date < " . $_POST["before"] . ")";
    
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
}
?>