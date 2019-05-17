<?php
declare(strict_types=1);

namespace Asynchrony;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use React\EventLoop\Factory as EventLoopFactory;
use React\EventLoop\LoopInterface;
use React\Http\Server;
use React\Promise\Promise;
use React\Socket\Server as SocketServer;

class App implements AppInterface
{
    /**
     * @var array<string, callable>
     */
    private $handlers = [];

    /** @var LoopInterface */
    private $loop;

    /**
     * App constructor.
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop = null)
    {
        $this->loop = $loop;
    }

    public function get(string $route, callable $handler): void
    {
        $this->handlers[$route] = $handler;
    }

    public function listen(int $port, callable $callback): void
    {
        $this->loop = $this->loop ?? EventLoopFactory::create();

        $server = new Server(function (ServerRequestInterface $request) {
            return new Promise(function ($resolve, $reject) use ($request) {
                $this->getHandler($request->getUri())($request, Response::create($resolve));
            });
        });

        $server->listen($socket = new SocketServer($port, $this->loop));

        $callback();

        $this->loop->run();
    }

    private function getHandler(UriInterface $uri): callable
    {
        return $this->handlers[$uri->getPath()];
    }
}