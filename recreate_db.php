<?php
try {
    $db = new PDO('mysql:host=127.0.0.1', 'root', '');
    $db->exec('DROP DATABASE IF EXISTS laravel_testing; CREATE DATABASE laravel_testing;');
    echo "Database recreated successfully\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
