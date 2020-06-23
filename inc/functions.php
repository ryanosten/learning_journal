<?php

//function to create new entries in database
function addEntry($title, $date, $time_spent, $learned, $resources, $tags){

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
        echo "error: " . $e->getMessage();
        return false;
    }

    $last_entry = $db->lastInsertId();

    if(!empty($tags)) {
        addTags($last_entry, $tags);
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
        $sql = "SELECT entries.title, entries.date, entries.time_spent, entries.learned, entries.resources FROM entries WHERE id = ?";
        $results = $db->prepare($sql);
        $results->bindValue(1, $id, PDO::PARAM_INT);
        $results->execute();
    } catch (Exception $e) {
        echo 'error: ' . $e->getMessage();
    }

    $entry_details = $results->fetch(PDO::FETCH_ASSOC);

    try {
        $sql = "SELECT tags.tag_name FROM tags
                JOIN entry_tags_link ON entry_tags_link.tag_id = tags.tag_id
                WHERE entry_tags_link.entry_id = ?";
        $results = $db->prepare($sql);
        $results->bindValue(1, $id, PDO::PARAM_INT);
        $results->execute();
    } catch (Exception $e) {
        echo 'error: ' . $e->getMessage();
    }

    $tags = [];

    foreach($results->fetchAll(PDO::FETCH_ASSOC) as $tag){
        $tag_value = $tag['tag_name'];
        $tags[] = $tag_value;
    }

    $entry_details['tags'] = $tags;
    return $entry_details;
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
        return false;
    }

    if(!empty($tags)) {
        foreach ($tags as $tag) {
            //check if tag already exists
            $tag = ltrim($tag);
            try {
                $sql = "SELECT tag_name FROM tags WHERE tag_name = ?";
                $results = $db->prepare($sql);
                $results->bindValue(1, $tag, PDO::PARAM_STR);
                $results->execute();
            } catch (Exception $e) {
                echo "error: " . $e->getMessage();
                return false;
            }
            //if the $results on fetch are empty, that means there is no duplicate existing in the tags table, so we can go ahead and create a record in tags table
            if (empty($results->fetchAll(PDO::FETCH_ASSOC))) {
                try {
                    $sql = "INSERT INTO tags(tag_name) VALUES (?)";
                    $results = $db->prepare($sql);
                    $results->bindValue(1, $tag, PDO::PARAM_STR);
                    $results->execute();
                } catch (Exception $e) {
                    echo "error: " . $e->getMessage();
                    return false;
                }
            }
        }
        //locate all records in entry_tags_link table that have the relevant entry id, compare them to the $tags,
        //and remove record if tag_id is not in $tags, add record if there is no tag_id entry_id association
    }

    editTags($id, $tags);

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

function editTags($id, $tags) {
    include 'connection.php';

    try {
        $sql= 'SELECT tags.tag_name, tags.tag_id FROM tags
                  JOIN entry_tags_link ON entry_tags_link.tag_id = tags.tag_id
                  WHERE entry_tags_link.entry_id = ?';
        $results = $db->prepare($sql);
        $results->bindValue(1, $id, PDO::PARAM_INT);
        $results->execute();
    } catch (Exception $e) {
        echo "error: " . $e->getMessage();
        return false;
    }


//    while($row = $results->fetch(PDO::FETCH_ASSOC)) {
//        $tag_name = $row['tag_name'];
//        $tag_id = $row['tag_id'];
//
//        //check if tag_name from query is in $tags that were submitted by user on edit entry form, if not then the user deleted
//        // it from tags input in form, so go ahead and delete the tag from entry_links_table
//        if(!in_array($tag_name, $tags) OR empty($tags)) {
//            try {
//                $sql = 'DELETE FROM entry_tags_link
//                          WHERE tag_id = ?';
//                $results = $db->prepare($sql);
//                $results->bindValue(1, $tag_id, PDO::PARAM_INT);
//                $results->execute();
//            } catch (Exception $e) {
//                echo "error: " . $e->getMessage();
//                return false;
//            }
//        }
//
//    }

    $results = $results->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as $row) {
        $tag_name = $row['tag_name'];
        $tag_id = $row['tag_id'];

        //check if tag_name from query is in $tags that were submitted by user on edit entry form, if not then the user deleted
        // it from tags input in form, so go ahead and delete the tag from entry_links_table
        if (!in_array($tag_name, $tags) OR empty($tags)) {
            try {
                $sql = 'DELETE FROM entry_tags_link
                          WHERE tag_id = ?';
                $results = $db->prepare($sql);
                $results->bindValue(1, $tag_id, PDO::PARAM_INT);
                $results->execute();
            } catch (Exception $e) {
                echo "error: " . $e->getMessage();
                return false;
            }
        }
    }


    //OK TIME TO ADD TAGS now we need to check if a tag from $tags is not in the entry_links
//    if(!empty($tags)) {
//        foreach ($tags as $tag) {
//            if(!in_array($tag, $entry_tags)) {
//                addTags($id, $tags);
//            }
//        }
//    }
}

function addTags($entry_id, $tags) {
    include 'connection.php';

    foreach ($tags as $tag) {
        //check if tag already exists
        $tag = ltrim($tag);
        try {
            $sql = "SELECT tag_name FROM tags WHERE tag_name = ?";
            $results = $db->prepare($sql);
            $results->bindValue(1, $tag, PDO::PARAM_STR);
            $results->execute();
        } catch (Exception $e) {
            echo "error: " . $e->getMessage();
            return false;
        }
        //if the $results on fetch are empty, that means there is no duplicate existing in the tags table, so we can go ahead and create a record in tags table
        if (empty($results->fetchAll(PDO::FETCH_ASSOC))) {
            try {
                $sql = "INSERT INTO tags(tag_name) VALUES (?)";
                $results = $db->prepare($sql);
                $results->bindValue(1, $tag, PDO::PARAM_STR);
                $results->execute();
            } catch (Exception $e) {
                echo "error: " . $e->getMessage();
                return false;
            }
        }
        //add to the entry_tags_link table
        try {
            //get the tag id
            $sql = "SELECT tag_id FROM tags WHERE tag_name = ?";
            $results = $db->prepare($sql);
            $results->bindValue(1, $tag, PDO::PARAM_STR);
            $results->execute();
        } catch (Exception $e) {
            echo "error: " . $e->getMessage();
            return false;
        }

        $row = $results->fetch(PDO::FETCH_ASSOC);
        $tag_id = $row['tag_id'];

        //now that we have the id of the tag from tags table, lets add that ID to the entry_tags_link table
        try {
            $sql = "INSERT INTO entry_tags_link(tag_id, entry_id) VALUES ($tag_id, $entry_id)";
            $db->query($sql);
        } catch (Exception $e) {
            echo "error: " . $e->getMessage();
            return false;
        }
    }
}


