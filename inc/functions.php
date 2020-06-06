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
