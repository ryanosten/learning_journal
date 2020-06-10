
<?php

include 'inc/functions.php';

$title = $time_spent = $learned = $date = $resources = '';

if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(getEntryDetails($id)) {
        $entry_details = getEntryDetails($id);
    } else {
        $error_msg = 'Unable to get details for that item. Check your item id to make sure its valid.';
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
    $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING));
    $date = trim(filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING));
    $time_spent= trim(filter_input(INPUT_POST, 'time_spent', FILTER_SANITIZE_STRING));
    $learned = trim(filter_input(INPUT_POST, 'learned', FILTER_SANITIZE_STRING));
    $resources = trim(filter_input(INPUT_POST, 'resources', FILTER_SANITIZE_STRING));

    $dateMatch = explode('-', $date);

    if (empty($title) || empty($time_spent) || empty($learned) || empty($resources)) {
        echo "Please complete all fields";
    } elseif (count($dateMatch) != 3
        || strlen($dateMatch[0]) != 4
        || strlen($dateMatch[1]) != 2
        || strlen($dateMatch[2]) != 2
        || !checkdate($dateMatch[1], $dateMatch[2], $dateMatch[0])) {
        echo "Please enter a valid date";
    } else {
        if (editEntry($id, $title, $date, $time_spent, $learned, $resources)) {
            echo 'test';
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
                    <form method="post" action="edit.php">
                        <label for="title"> Title</label>
                        <input id="title" type="text" name="title" value="<?php
                                if(isset($entry_details)){
                                    echo $entry_details['title'];
                            }?>"><br>
                        <label for="date">Date</label>
                        <input id="date" type="date" name="date" value="<?php
                                if(isset($entry_details)){
                                    echo $entry_details['date'];
                            }?>"><br>
                        <label for="time-spent"> Time Spent</label>
                        <input id="time-spent" type="text" name="time_spent" value="<?php
                                if(isset($entry_details)){
                                echo $entry_details['time_spent'];
                            }?>"><br>
                        <label for="what-i-learned">What I Learned</label>
                        <textarea id="what-i-learned" rows="5" name="learned"><?php
                                if(isset($entry_details)){
                                    echo $entry_details['learned'];
                            }?>
                        </textarea>
                        <label for="resources-to-remember">Resources to Remember</label>
                        <textarea id="resources-to-remember" rows="5" name="resources"><?php
                                if(isset($entry_details)){
                                    echo $entry_details['resources'];
                            }?>
                        </textarea>
                        <input hidden name="id" value="<?= $id ?>">
                        <input type="submit" value="Publish Entry" class="button">
                        <a href="#" class="button button-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </section>
        <?php include 'footer.php'; ?>
    </body>
</html>