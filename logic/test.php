<?php
namespace Logic\Test;

$path = $_SERVER['DOCUMENT_ROOT'];

use App\MySQLConnector;

include_once $path.'/src/app/MySQLConnector.php';

//header('Content-Type: text/plain');

$db = new MySQLConnector();

print_r($db->get_task(1));