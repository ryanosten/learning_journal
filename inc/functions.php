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
        $sql = "SELECT id, title, date FROM entries
                ORDER BY date DESC";
        $results = $db->query($sql);
    } catch (Exception $e) {
        echo 'error: ' . $e->getMessage();
    }


    return $results->fetchAll(PDO::FETCH_ASSOC);
}

//function to get tags
function getTags($entry_id) {
    include 'connection.php';

    try {
        $sql = 'SELECT tags.tag_name, entry_tags_link.entry_id FROM tags
                JOIN entry_tags_link ON tags.tag_id = entry_tags_link.tag_id
                WHERE entry_id = ?';
        $results = $db->prepare($sql);
        $results->bindValue(1, $entry_id, PDO::PARAM_INT);
        $results->execute();
    } catch (Exception $e) {
        echo "error: " . $e->getMessage();
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

    addTags($id, $tags);

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

//function to add new tags
function addTags($entry_id, $tags) {
    include 'connection.php';

    try {
        $sql = 'DELETE FROM entry_tags_link
                WHERE entry_id = ?';
        $results = $db->prepare($sql);
        $results->bindValue(1, $entry_id, PDO::PARAM_INT);
        $results->execute();
    } catch (Exception $e) {
        echo "error: " . $e->getMessage();
        return false;
    }


    $tags_added = [];

    foreach ($tags as $tag) {
            if (!in_array($tag, $tags_added)){
                //check if tag already exists in tags table
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
                    //get the tag id from the newly created tag
                    $sql = "SELECT tag_id, tag_name FROM tags WHERE tag_name = ?";
                    $results = $db->prepare($sql);
                    $results->bindValue(1, $tag, PDO::PARAM_STR);
                    $results->execute();
                } catch (Exception $e) {
                    echo "error: " . $e->getMessage();
                    return false;
                }

                $row = $results->fetch(PDO::FETCH_ASSOC);
                $tag_id = $row['tag_id'];
                $tag_name = $row['tag_name'];

                //now that we have the id of the tag from tags table, lets add that ID to the entry_tags_link table
                try {
                    $sql = "INSERT INTO entry_tags_link(tag_id, entry_id) VALUES ($tag_id, $entry_id)";
                    $db->query($sql);
                } catch (Exception $e) {
                    echo "error: " . $e->getMessage();
                    return false;
                }
            }

        //now simply store the tag we added in an array so that we can do a check to prevent duplicates from being added
        $tags_added[] = $tag_name;
    }
}

function convertTagsToArray($tags) {
    if (!empty($tags)) {
        //explode diff tags into an array to handle adding multiple tags on an entry
        $tags = explode(',', $tags);

        //remove all whitespace from array values
        $tags = array_map('trim', $tags);

        //if there is a trailing comma in $tags, an empty value will be created. This will remove it.
        if (($key = array_search('', $tags)) !== false) {
            unset($tags[$key]);
        }
    } else {
        $tags = [];
    }

    return $tags;
}


////function to edit tags from the edit entry view
//function editTags($id, $tags) {
//    include 'connection.php';
//
//    //retrieve the tag names from the tags table or the tags that are linked/associated with the entry id being edited
//    try {
//        $sql = 'SELECT tags.tag_name, tags.tag_id FROM tags
//                  JOIN entry_tags_link ON entry_tags_link.tag_id = tags.tag_id
//                  WHERE entry_tags_link.entry_id = ?';
//        $results = $db->prepare($sql);
//        $results->bindValue(1, $id, PDO::PARAM_INT);
//        $results->execute();
//    } catch (Exception $e) {
//        echo "error: " . $e->getMessage();
//        return false;
//    }
//    //loop through the results
//    $linked_items = $results->fetchAll(PDO::FETCH_ASSOC);
//
//    foreach ($linked_items as $row) {
//        $tag_name = $row['tag_name'];
//        $tag_id = $row['tag_id'];
//
//        //check if tag_name from the query results is in $tags that were submitted by user on edit entry form, if not then this means that the user deleted
//        // it from tags input in form, so go ahead and delete the tag from entry_links_table
//        if (!in_array($tag_name, $tags) OR empty($tags)) {
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
//    }
//
////    OK its time to handle adding tags through the edit from. Now we need to check if a tag from $tags is not in the entry_links table which would mean the user added it in the input
////    first lets put the results from the query to entry_links in an single-dimensional array (bc we need to check this array)
//    if (!empty($tags)) {
//            $input_tags = [];
//            foreach ($linked_items as $item) {
//                $input_tags[] = $item['tag_name'];
//            }
////  now we loops through the $tags that were input by user
//        foreach ($tags as $tag) {
//            //check if a given $tag from user is present in the entry_tags_link table for the given entry $id. If its not in the array, that means its new and we need to add it so we
//            //call addTags
//            if (!in_array($tag, $input_tags)) {
//                //put $tag into array bc addTags takes an array as argument
//                $tag_arr = [$tag];
//                addTags($id, $tag_arr);
//            }
//        }
//    }
//}

