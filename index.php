<?php
include 'inc/functions.php';

if(isset($_GET['success'])) {
    $msg_param = trim(filter_input(INPUT_GET, 'success', FILTER_SANITIZE_STRING));

    if ($msg_param == 'updated') {
        $success_msg = 'Item was successfully updated';
    } elseif ($msg_param == 'added') {
        $success_msg = 'Item was successfully added';
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
    <?php include 'header.php'; ?>
        <section>
            <div class="container">
                <div class="entry-list">
                    <?php
                        if(isset($success_msg)) {
                            echo "<p class='animate__animated animate__fadeOutUp success-toast animate__delay-2s '>$success_msg</p>";
                        }
                        $entries = getEntries();
                        foreach($entries as $entry) {
                        $formatted_time = convertDate($entry['date']);
                        echo "<article>";
                        echo "<h2><a href=\"detail.php?id={$entry['id']}\">{$entry['title']}</a></h2>";
                        echo "<time datetime=\"{$entry['date']}\">" . convertDate($entry['date']) . "</time>";
                        echo "</article>";
                        }
                    ?>

                </div>
            </div>
        </section>
        <?php include 'footer.php'; ?>
    </body>
</html>