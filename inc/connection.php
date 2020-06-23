<?php

try {
    $db = new PDO ("sqlite:" . __DIR__ . "/journal.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec( 'PRAGMA foreign_keys = ON;');
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}