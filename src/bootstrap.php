<?php

//-- Importa el framework silex
// require_once (BASE_DIR . '/vendor/silex.phar');
require_once __DIR__.'/../vendor/autoload.php';

//-- Crea una nueva aplicación silex
$app = new Silex\Application();

//-- Configuramos la base de datos
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options'        => array(
        'driver'        => DB_DRIVER,
        'host'          => DB_HOST,
        'dbname'        => DB_NAME,
        'user'          => DB_USER,
        'password'      => DB_PASS,
    )));
// $app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    // 'db.options' => array(
        // 'driver'   => 'pdo_sqlite',
        // 'path'     => __DIR__.'/app.db',
    // ),
// ));