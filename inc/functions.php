<?php


//function to create new entries in database
function addEntry($title, $date, $time_spent, $learned, $resources, $tags){

    include 'connection.php';

    try{
        $sql = "INSERT INTO entries (title, date, time_spent, learned, resources, tags) VALUES (?, ?, ?, ?, ?, ?)";
        $results = $db->prepare($sql);
        $results->bindValue(1, $title, PDO::PARAM_STR);
        $results->bindValue(2, $date, PDO::PARAM_STR);
        $results->bindValue(3, $time_spent, PDO::PARAM_INT);
        $results->bindValue(4, $learned, PDO::PARAM_STR);
        $results->bindValue(5, $resources, PDO::PARAM_STR);
        $results->bindValue(6, $tags, PDO::PARAM_STR);
        $results->execute();
    } catch (Exception $e) {
        echo "error:" . $e->getMessage();
        return false;
    }
    return true;
}

//function to get all entries from the db
function getEntries() {
    include 'connection.php';

    try {
        $sql = "SELECT id, title, date FROM entries ORDER BY date DESC";
        $results = $db->query($sql);
    } catch (Exception $e) {
        echo 'error: ' . $e->getMessage();
    }

    return $results->fetchAll(PDO::FETCH_ASSOC);
}

//function to get entry details for a single entry
function getEntryDetails($id) {
    include 'connection.php';

    try {
        $sql = "SELECT title, date, time_spent, learned, resources, tags FROM entries WHERE id = ?";
        $results = $db->prepare($sql);
        $results->bindValue(1, $id, PDO::PARAM_INT);
        $results->execute();
    } catch (Exception $e) {
        echo 'error: ' . $e->getMessage();
    }

    return $results->fetch(PDO::FETCH_ASSOC);
}

//function to format the date from db yyyy-mm-dd to June 15, 2020 (for example)
function convertDate($date) {
    $timestamp = strtotime($date);
    $formatted_time = date('F j, Y', $timestamp);
    return $formatted_time;
}

//function to edit an entry in db
function editEntry($id, $title, $date, $time_spent, $learned, $resources, $tags) {
    include 'connection.php';

    try {
        $sql = 'UPDATE entries SET title=?, date=?, time_spent=?, learned=?, resources=?, tags=? WHERE id=?';
        $results = $db->prepare($sql);
        $results->bindValue(1, $title, PDO::PARAM_STR);
        $results->bindValue(2, $date, PDO::PARAM_STR);
        $results->bindValue(3, $time_spent, PDO::PARAM_INT);
        $results->bindValue(4, $learned, PDO::PARAM_STR);
        $results->bindValue(5, $resources, PDO::PARAM_STR);
        $results->bindValue(6, $tags, PDO::PARAM_STR);
        $results->bindValue(7, $id, PDO::PARAM_INT);
        $results->execute();
    } catch (Exception $e) {
        echo 'error: ' . $e->getMessage();
    }

    return true;

}

//function to delete an entry in db
function deleteEntry($id) {
    include 'connection.php';

    try {
        $sql = 'DELETE FROM entries WHERE id = ?';
        $results = $db->prepare($sql);
        $results->bindValue(1, $id, PDO::PARAM_INT);
        $results->execute();
    } catch (Exception $e) {
        echo 'error ' . $e->getMessage();
    }

    return true;
}