<?php

include 'inc/functions.php';

if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if(getEntryDetails($id)) {
        $entry_details = getEntryDetails($id);
    } else {
        $error_msg = 'Unable to get details for that item. Check your item id to make sure its valid.';
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
                            <h3>What I Learned:</h3>
                            <p><?= $entry_details['learned']?></p>
                        </div>
                        <div class="entry">
                            <h3>Resources to Remember:</h3>
                            <ul>
                                <li><?= $entry_details['resources']?></li>
                            </ul>
                        </div>
                    </article>
                </div>
            </div>
            <div class="edit">
                <p><a href="edit.php?id=<?= $id ?>">Edit Entry</a></p>
                <form method="post" action="edit.php">
                    <input type="submit" value="Delete" class="delete"/>
                </form>
            </div>
        </section>
        <?php include 'footer.php'; ?>
    </body>
</html>