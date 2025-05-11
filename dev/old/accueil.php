<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_status() === PHP_SESSION_NONE && session_start();
require_once __DIR__ . '/../../twig.php'; // Inclure la configuration Twig
require_once __DIR__ . '/../General/menu.php';
require_once __DIR__ . '/../../index.php';

