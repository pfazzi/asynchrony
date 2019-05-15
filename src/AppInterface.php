<?php
declare(strict_types=1);

namespace Asynchrony;

interface AppInterface
{
    public function get(string $route, callable $handler): void;

    public function listen(int $port, callable $callback): void;
}