<?php
declare(strict_types=1);

use Asynchrony\App;
use React\MySQL\QueryResult;


require dirname(__DIR__).'/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$connection = (new React\MySQL\Factory($loop))
    ->createLazyConnection('devel:devel@localhost/');

$app = new App($loop);

$app->get('/hello-world', function ($req, $res) use ($connection) {
    $connection->query('SELECT * FROM book')->then(
        function (QueryResult $command) use ($res) {
            $res->send("Hello World!");
        }
    );
});

$app->get('/ciao-mondo', function ($req, $res) {
    $res->send("Ciao Mondo!");
});

$app->listen(3000, function () {
    echo 'Example app listening on port 3000!';
});