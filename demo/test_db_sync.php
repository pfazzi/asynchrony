<?php
declare(strict_types=1);

use Symfony\Component\HttpFoundation\Response;

require dirname(__DIR__).'/vendor/autoload.php';

try {
    $connection = new PDO("mysql:host=localhost;dbname=test", 'devel', 'devel');
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $content = "";
    foreach ($connection->query('SELECT * FROM book', PDO::FETCH_ASSOC) as $row) {
        $content .= implode(', ', $row) . PHP_EOL;
    }

    Response::create(
        'sync :(' . PHP_EOL . $content,
        200,
        ['Content-Type' => 'text/plain']
    )->send();
} catch(PDOException $e) {
    Response::create(
        $e->getMessage(),
        Response::HTTP_BAD_REQUEST,
        ['Content-Type' => 'text/plain']
    )->send();
} finally {
    $connection = null;
}