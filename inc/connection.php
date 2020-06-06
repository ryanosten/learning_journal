<?php

try {
    $db = new PDO ("sqlite:" . __DIR__ . "/journal.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}