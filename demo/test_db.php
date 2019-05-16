<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Response;
use React\Http\Server;
use React\MySQL\QueryResult;
use React\Promise\Promise;
use React\Socket\Server as SocketServer;

require dirname(__DIR__).'/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$connection = (new React\MySQL\Factory($loop))
    ->createLazyConnection('devel:devel@localhost/test');

$server = new Server(function (ServerRequestInterface $request) use ($loop, $connection) {
    return new Promise(function ($resolve, $reject) use ($loop, $connection) {
        $connection->query('SELECT * FROM book')->then(
            function (QueryResult $result) use ($resolve) {
                $content = implode(PHP_EOL, array_map(function ($row) {
                    return implode(', ', $row);
                }, $result->resultRows));

                $response = new Response(
                    200,
                    array(
                        'Content-Type' => 'text/plain'
                    ),
                    'ASYNC <3' . PHP_EOL . $content
                );
                $resolve($response);
            },
            function (Exception $error) use ($resolve) {
                $response = new Response(
                    200,
                    array(
                        'Content-Type' => 'text/plain'
                    ),
                    'Error: ' . $error->getMessage() . PHP_EOL
                );
                $resolve($response);
            }
        );
    });
});

$server->listen(new SocketServer(4000, $loop));

$loop->run();