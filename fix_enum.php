<?php
require 'src/config/db.php';
try {
    $pdo->exec("ALTER TABLE annuncio MODIFY COLUMN stato_conservazione ENUM('Nuovo', 'Usato come nuovo', 'Ottimo', 'Buono', 'Discreto', 'Scarso') NOT NULL");
    echo "Success!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
