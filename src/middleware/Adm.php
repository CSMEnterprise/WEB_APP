<?php

function checkAdmin() {
    session_start();

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        exit("Accesso negato: area admin");
    }
}