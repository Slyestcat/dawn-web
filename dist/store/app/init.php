<?php
    require_once 'constants.php';
    require_once 'core/Controller.php';
    require_once 'Functions.php';
    require_once 'vendor/autoload.php';
    require 'vendor/stripe/init.php';

    use Illuminate\Database\Capsule\Manager as DB;
    
    $dirs = [
        "app/core/",
        "app/controllers/",
        "app/models/",
        "app/plugins/",
    ];

    foreach($dirs as $dir) {
        foreach (glob($dir.'*.php') as $filename) {
            include_once(''.$filename.'');
        }
    }

    $db = new DB;

    $db->addConnection([
        "driver"   => "mysql",
        "host"     => MYSQL_HOST,
        "database" => MYSQL_DATABASE,
        "username" => MYSQL_USERNAME,
        "password" => MYSQL_PASSWORD,
    ]);

    $db->setAsGlobal();
    $db->bootEloquent();