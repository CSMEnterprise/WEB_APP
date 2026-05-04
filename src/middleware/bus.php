<?php

function checkBusiness() {
    session_start();

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'business') {
        http_response_code(403);
        exit("Accesso negato: solo business");
    }
}