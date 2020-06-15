
<?php

include ('inc/functions.php');

//variables to store values to ensure we don't clear values from page inputs in the event that creation of record in db is not successful
$title = $time_spent = $learned = $date = $resources = $tags = '';

//handler for creating a new journal entry
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    //filter and store journal entry keys on the POST array
    $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING));
    $date = trim(filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING));
    $time_spent= trim(filter_input(INPUT_POST, 'time_spent', FILTER_SANITIZE_STRING));
    $learned = trim(filter_input(INPUT_POST, 'learned', FILTER_SANITIZE_STRING));
    $resources = trim(filter_input(INPUT_POST, 'resources', FILTER_SANITIZE_STRING));
    $tags = trim(filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_STRING));

    //explode $date into an array so that we can validate it
    $dateMatch = explode('-', $date);

    //conditional checks for required fields - $title and $time_spent
    if (empty($title) || empty($time_spent)) {
        echo "Please complete all required fields";
        //validation for date
    } elseif (count($dateMatch) != 3
        || strlen($dateMatch[0]) != 4
        || strlen($dateMatch[1]) != 2
        || strlen($dateMatch[2]) != 2
        || !checkdate($dateMatch[1], $dateMatch[2], $dateMatch[0])) {
        echo "Please enter a valid date";
    } else {
        //if all is good, we add the entry and redirect to index.php. We also set get param to success=added to display toaster
        if (addEntry($title, $date, $time_spent, $learned, $resources, $tags)) {
            header('Location: index.php?success=added');
        } else {
            $error_msg = 'Could not add entry';
        }
    }
}
include ('vendor/autoload.php');
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
                <div class="new-entry">
                    <h2>New Entry</h2>
                    <form method="post" action="new.php">
                        <label for="title"> Title*</label>
                        <input id="title" type="text" name="title" value="<?= htmlspecialchars($title)?>" required><br>
                        <label for="date" >Date*</label>
                        <input id="date" type="date" name="date" value="<?= htmlspecialchars($date)?>" required><br>
                        <label for="time-spent">Time Spent*</label>
                        <input id="time-spent" type="text" name="time_spent" value="<?= htmlspecialchars($time_spent)?>" required><br>
                        <label for="what-i-learned">What I Learned</label>
                        <textarea id="what-i-learned" rows="5" name="learned"><?= htmlspecialchars($learned)?></textarea>
                        <label for="resources-to-remember">Resources to Remember</label>
                        <textarea id="resources-to-remember" rows="5" name="resources"><?= htmlspecialchars($resources)?></textarea>
                        <label for="tags">tags</label>
                        <input id="tags" type="text" name="tags" value="<?= htmlspecialchars($tags) ?>"><br>
                        <input type="submit" value="Publish Entry" class="button">
                        <a href="#" class="button button-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </section>
        <?php include 'footer.php'; ?>
    </body>
</html>