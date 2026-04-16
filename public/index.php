<?php
// Main entry point for the application
require_once '../app/config/config.php';
require_once '../app/core/Database.php';
require_once '../app/core/Controller.php';
require_once '../app/core/App.php';

// Initialize the core application class
$app = new App();
