<?php
require 'controller.php';
$path = '/' . ($_GET['page'] ?? 'articles');
$method = $_SERVER['REQUEST_METHOD'];
Controller::route($path, $method);