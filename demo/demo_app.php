<?php
declare(strict_types=1);

use Asynchrony\App;

require dirname(__DIR__).'/vendor/autoload.php';

$app = new App();

$app->get('/hello-world', function ($req, $res) {
    $res->send("Hello World!");
});

$app->get('/ciao-mondo', function ($req, $res) {
    $res->send("Ciao Mondo!");
});

$app->listen(3000, function () {
    echo 'Example app listening on port 3000!';
});