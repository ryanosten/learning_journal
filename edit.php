
<?php

include 'inc/functions.php';

//vars for storing fields so they dont clear if error on submit
$title = $time_spent = $learned = $date = $resources = $tags = '';

//if request is GET, were going to call getEntryDetails with id and get the entry details. We store the details in $entry_details.
if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(getEntryDetails($id)) {
        $entry_details = getEntryDetails($id);
    } else {
        $error_msg = 'Unable to get details for that item. Check your item id to make sure its valid.';
    }
}

//if request method is POST, that means user wants to submit new edits to a record.
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    //filter all keys on POST
    $id = trim(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
    $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING));
    $date = trim(filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING));
    $time_spent= trim(filter_input(INPUT_POST, 'time_spent', FILTER_SANITIZE_STRING));
    $learned = trim(filter_input(INPUT_POST, 'learned', FILTER_SANITIZE_STRING));
    $resources = trim(filter_input(INPUT_POST, 'resources', FILTER_SANITIZE_STRING));
    $tags = trim(filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_STRING));

    //explode diff tags into an array to handle adding multiple tags on an entry
    if (!empty($tags)) {
        $tags = explode(',', $tags);

        //remove all whitespace from array values
        $tags = array_map('trim', $tags);

        //if there is a trailing comma, an empty value will be created. This will remove it.
        if (($key = array_search('', $tags)) !== false) {
            unset($tags[$key]);
        }
    } else {
        $tags = [];
    }

    //explode date into an array so that we can validate date
    $dateMatch = explode('-', $date);

    //check for required fields
    if (empty($title) || empty($time_spent)) {
        echo "Please complete all required fields";
        //validate date by checking string length
    } elseif (count($dateMatch) != 3
        || strlen($dateMatch[0]) != 4
        || strlen($dateMatch[1]) != 2
        || strlen($dateMatch[2]) != 2
        //use checkdate to validate that its a valid date
        || !checkdate($dateMatch[1], $dateMatch[2], $dateMatch[0])) {
        echo "Please enter a valid date";
    } else {
        //if all is good, call editEntry to edit the entry record in database. Then redirect to index and pass a GET param to show toaster
        if (editEntry($id, $title, $date, $time_spent, $learned, $resources, $tags)) {
            header('Location: index.php?success=updated');
        } else {
            $error_msg = 'Could not update entry';
        }
    }
}

?>

<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>MyJournal</title>
        <link href="https://fonts.googleapis.com/css?family=Cousine:400" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Work+Sans:600" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/site.css">
    </head>
    <body>
    <?php include 'header.php' ?>
        <section>
            <div class="container">
                <div class="edit-entry">
                    <?php
                        if(isset($error_msg)) {
                            echo "<h1>$error_msg</h1>";
                        }
                    ?>
                    <h2>Edit Entry</h2>
                    <form method="post" action="edit.php?id=<?= $id ?>">
                        <label for="title">Title*</label>
                        <input id="title" type="text" name="title" value="<?php
                            //these blocks display the entry details with given key. We first check if the request method is POST,
                            //if so show value that was in field on submit, so we don't clear inputs for user.
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                echo $title;
                            } elseif (isset($entry_details)){
                                echo $entry_details['title'];
                            }
                            ?>" required><br>
                        <label for="date">Date*</label>
                        <input id="date" type="date" name="date" value="<?php
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                echo $date;
                            } elseif (isset($entry_details)){
                                echo $entry_details['date'];
                            }?>" required><br>
                        <label for="time-spent">Time Spent*</label>
                        <input id="time-spent" type="text" name="time_spent" value="<?php
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                echo $time_spent;
                            } elseif (isset($entry_details)){
                                echo $entry_details['time_spent'];
                            }?>" required><br>
                        <label for="what-i-learned">What I Learned</label>
                        <textarea id="what-i-learned" rows="5" name="learned"><?php
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                echo $learned;
                            } elseif (isset($entry_details)){
                                echo $entry_details['learned'];
                            }?>
                        </textarea>
                        <label for="resources-to-remember">Resources to Remember</label>
                        <textarea id="resources-to-remember" rows="5" name="resources"><?php
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                echo $resources;
                            } elseif (isset($entry_details)){
                                echo $entry_details['resources'];
                            }?>
                        </textarea>
                        <label for="tags">Tags</label>
                        <input id="tags" type="text" name="tags" value="<?php
                            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                                echo $tags;
                            } elseif (isset($entry_details)) {
                                if (!empty($entry_details['tags'])) {
                                    foreach ($entry_details['tags'] as $key => $value) {
                                        echo $value;
                                        if($key + 1 < count($entry_details['tags'])) {
                                            echo ', ';
                                        }
                                    }
                                }
                            }?>"><br>
                        <input hidden name="id" value="<?= $id ?>">
                        <br><input type="submit" value="Publish Entry" class="button">
                        <a href="#" class="button button-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </section>
        <?php include 'footer.php'; ?>
    </body>
</html>