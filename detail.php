<?php

include 'inc/functions.php';
session_start();

//if GET id key is set, call getEntryDetails to display the entry details
if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(getEntryDetails($id)) {
        $entry_details = getEntryDetails($id);
    } else {
        $error_msg = 'Unable to get details for that item. Check your item id to make sure its valid.';
    }
}

//if POST delete key is set, call deleteEntry to delete the entry record from db
if (isset($_POST['delete'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(deleteEntry($id)) {
        $_SESSION['show_msg'] = 1;
        header('Location: index.php?success=deleted');
    } else {
        $error_msg = 'Unable to delete entry. Please try again. If the issue persists please contact support.';
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
    <?php include 'header.php'?>
        <section>
            <div class="container">
                <div class="entry-list single">
                    <article>
                        <h1><?php
                            if(isset($error_msg)) {
                                echo $error_msg;
                                die();
                            } else {
                              echo $entry_details['title'];
                            }
                            ?></h1>
                        <time datetime="2016-01-31"><?= convertDate($entry_details['date']); ?></time>
                        <div class="entry">
                            <h3>Time Spent: </h3>
                            <p><?= $entry_details['time_spent']?></p>
                        </div>
                        <div class="entry">
                            <h3>Learned: </h3>
                            <?php
                            //if $entry_details is set, show the 'learned' key value. We do this conditional bc learned is not a required field
                            if(!empty($entry_details['learned'])) {
                                echo "<p>{$entry_details['learned']}</p>";
                            } else {
                                echo "<p>None</p>";
                            }
                            ?>
                        </div>
                        <div class="entry">
                            <h3>Resources to Remember:</h3>
                            <?php if(!empty($entry_details['resources'])) {
                                echo "<ul>" .
                                "<li>{$entry_details['resources']}</li>" .
                                "</ul>";
                            } else {
                                echo "<p>None</p>";
                            }
                            ?>

                        </div>
                        <div class="entry">
                            <h3>Tags:</h3>
                            <?php if(!empty($entry_details['tags'])) {
                                echo "<p>";
                                foreach ($entry_details['tags'] as $key => $value) {
                                    echo $value;
                                    if($key + 1 < count($entry_details['tags'])) {
                                        echo ', ';
                                    }
                                }
                                echo "</p>";
                            } else {
                                echo "<p>None</p>";
                            }
                            ?>
                        </div>
                    </article>
                </div>
            </div>
            <div class="edit">
                <p><a href="edit.php?id=<?= $id ?>">Edit Entry</a></p>
                <form method="post" action="detail.php">
                    <input hidden value="<?= $id ?>" name="id">
                    <input type="submit" value="Delete" class="delete" name="delete"/>
                </form>
            </div>
        </section>
        <?php include 'footer.php'; ?>
    </body>
</html>