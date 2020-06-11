<?php



function addEntry($title, $date, $time_spent, $learned, $resources){

    include 'connection.php';

    try{
        $sql = "INSERT INTO entries (title, date, time_spent, learned, resources) VALUES (?, ?, ?, ?, ?)";
        $results = $db->prepare($sql);
        $results->bindValue(1, $title, PDO::PARAM_STR);
        $results->bindValue(2, $date, PDO::PARAM_STR);
        $results->bindValue(3, $time_spent, PDO::PARAM_INT);
        $results->bindValue(4, $learned, PDO::PARAM_STR);
        $results->bindValue(5, $resources, PDO::PARAM_STR);
        $results->execute();
    } catch (Exception $e) {
        echo "error:" . $e->getMessage();
        return false;
    }
    return true;
}

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

function getEntryDetails($id) {
    include 'connection.php';

    try {
        $sql = "SELECT title, date, time_spent, learned, resources FROM entries WHERE id = ?";
        $results = $db->prepare($sql);
        $results->bindValue(1, $id, PDO::PARAM_INT);
        $results->execute();
    } catch (Exception $e) {
        echo 'error: ' . $e->getMessage();
    }

    return $results->fetch(PDO::FETCH_ASSOC);
}

function convertDate($date) {
    $timestamp = strtotime($date);
    $formatted_time = date('F j, Y', $timestamp);
    return $formatted_time;
}

function editEntry($id, $title, $date, $time_spent, $learned, $resources) {
    include 'connection.php';

    try {
        $sql = 'UPDATE entries SET title=?, date=?, time_spent=?, learned=?, resources=? WHERE id=?';
        $results = $db->prepare($sql);
        $results->bindValue(1, $title, PDO::PARAM_STR);
        $results->bindValue(2, $date, PDO::PARAM_STR);
        $results->bindValue(3, $time_spent, PDO::PARAM_INT);
        $results->bindValue(4, $learned, PDO::PARAM_STR);
        $results->bindValue(5, $resources, PDO::PARAM_STR);
        $results->bindValue(6, $id, PDO::PARAM_INT);
        $results->execute();
    } catch (Exception $e) {
        echo 'error: ' . $e->getMessage();
    }

    return true;

}

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