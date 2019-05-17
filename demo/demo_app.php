<?php
declare(strict_types=1);

use Asynchrony\App;
use Asynchrony\Response;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/vendor/autoload.php';

$loop = React\EventLoop\Factory::create();


$app = new App($loop);

$app->get('/hello-world', function (Request $req, Response $res) {
    $res->send("Hello World!");
});

$app->get('/ciao-mondo', function (Request $req, Response $res) {
    $res->send("Ciao Mondo!");
});

$app->get('/hello/{userName}', function (Request $req, Response $res) {
    $res->send("Ciao {$req->get('userName')}!");
});

$app->listen(3000, function () {
    echo "Example app listening on port 3000!\n";
});