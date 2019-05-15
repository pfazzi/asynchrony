<?php
declare(strict_types=1);

namespace Asynchrony;

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory as EventLoopFactory;
use React\EventLoop\LoopInterface;
use React\Http\Response;
use React\Http\StreamingServer;
use React\Promise\Deferred;
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

    public function get(string $route, callable $handler): void
    {
        $this->handlers[$route] = $handler;
    }

    public function listen(int $port, callable $callback): void
    {
        $this->loop = EventLoopFactory::create();

        $server = new StreamingServer(function (ServerRequestInterface $request) {
            return new Promise(function ($resolve, $reject) use ($request) {
                if (!array_key_exists($request->getUri()->getPath(), $this->handlers)) {
                    $resolve(
                        new Response(
                            404,
                            array(
                                'Content-Type' => 'text/plain'
                            ),
                            'Not found!'
                        )
                    );
                    return;
                }

                $request->getBody()->on('end', function () use ($request, $resolve){
                    $deferred = new Deferred();

                    $deferred->promise()->then(function ($content) use ($resolve) {
                        $response = new Response(
                            200,
                            array(
                                'Content-Type' => 'text/plain'
                            ),
                            $content
                        );

                        $resolve($response);
                    });

                    $sender = new class($deferred) {
                        private $deferred;

                        public function __construct(Deferred $deferred)
                        {
                            $this->deferred = $deferred;
                        }

                        public function send(string $content)
                        {
                            $this->deferred->resolve($content);
                        }
                    };

                    $this->handlers[$request->getUri()->getPath()]($request, $sender);
                });
            });
        });

        $server->listen($socket = new SocketServer($port, $this->loop));

        $callback();

        $this->loop->run();
    }
}