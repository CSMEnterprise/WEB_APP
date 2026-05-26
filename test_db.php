<?php
require 'src/config/db.php';
$stmt = $pdo->query('SELECT id_annuncio, stato FROM annuncio WHERE id_annuncio IN (12, 4124) LIMIT 5');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows);
