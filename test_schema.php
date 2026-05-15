<?php
require 'src/config/db.php';
$stmt = $pdo->query("SHOW COLUMNS FROM annuncio LIKE 'stato_conservazione'");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
