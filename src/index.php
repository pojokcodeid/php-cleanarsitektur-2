<?php
// public/index.php
require '../vendor/autoload.php';

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use DI\Container;
use Infrastructure\LoggerConfig;
use Web\Controller\UserController;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Buat container dari PHP-DI
$container = new Container();
AppFactory::setContainer($container);

$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Konfigurasi Logger menggunakan LoggerConfig
$logger = LoggerConfig::createLogger();

// Tambahkan Logger ke dalam container
$container->set('logger', $logger);

// Buat UserController dengan dependency injection
$controller = new UserController($container);

$app->post('/example/users', [$controller, 'createUser']); // Create
$app->get('/example/users', [$controller, 'listUsers']);  // Read (All)
$app->get('/example/users/{id}', [$controller, 'getUser']); // Read (By ID)
$app->put('/example/users/{id}', [$controller, 'updateUser']); // Update
$app->delete('/example/users/{id}', [$controller, 'deleteUser']); // Delete

$app->run();

