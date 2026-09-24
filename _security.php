<?php
    session_start();

    // Giriş durumunu tek bir noktada kontrol edip global değişkenlere aktarıyoruz
    $isLoggedIn = false;
    $loggedInUser = null;

    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === "ok") {
        $isLoggedIn = true;
        $loggedInUser = $_SESSION["employeecode"] ?? null;
    }
?>