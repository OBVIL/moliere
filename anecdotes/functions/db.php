<?php

function connect() {

    try {
        $db = new PDO('sqlite:data/anecdotes.sqlite');
    }
    catch(Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
    return $db;
}

function insert($sql, $db, $data = false) {

    
    if ($data) {
        $req = $db->prepare($sql);
        $req->execute($data);
    } else {
        $req = $db->exec($sql);
    }
    
    if ($req === false) {
        echo 'ERREUR : ', print_r($db->errorInfo());
    } else {

        //echo 'Table créée';
        
    }
}

function select($sql, $db) {

    $req = $db->query($sql);
    $req = $req->fetch(PDO::FETCH_ASSOC);
    return $req;
}

function mselect($sql, $db) {

    $req = $db->query($sql);
    $req = $req->fetchAll(PDO::FETCH_ASSOC);
    return $req;
}
?>