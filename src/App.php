<?php
declare(strict_types=1);

namespace Asynchrony;

use function Lambdish\Phunctional\filter;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use React\EventLoop\Factory as EventLoopFactory;
use React\EventLoop\LoopInterface;
use React\Http\Server;
use React\Promise\Promise;
use React\Socket\Server as SocketServer;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class App implements AppInterface
{
    /**
     * @var RouteCollection
     */
    private $routes;

    /**
     * @var HttpFoundationFactory
     */
    private $httpFoundationFactory;

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var UrlMatcher
     */
    private $urlMatcher;

    /**
     * App constructor.
     * @param LoopInterface $loop
     */
    public function __construct(LoopInterface $loop = null)
    {
        $this->loop = $loop;

        $this->routes = new RouteCollection();
        $this->httpFoundationFactory = new HttpFoundationFactory();
        $this->urlMatcher = new UrlMatcher($this->routes, new RequestContext('/'));
    }

    public function get(string $path, callable $handler): void
    {
        $this->addRoute(self::buildRoute($path, ['GET'], $handler));
    }

    public function post(string $path, callable $handler): void
    {
        $this->addRoute(self::buildRoute($path, ['GET'], $handler));
    }

    private static function buildRoute(string $path, array $methods, callable $handler): Route
    {
        $route = new Route($path, ['handler' => $handler]);
        $route->setMethods($methods);

        return $route;
    }

    private function addRoute(Route $route, string $name = null): void
    {
        if (null === $name) {
            $name = Uuid::uuid4()->toString();
        }

        $this->routes->add($name, $route);
    }

    public function listen(int $port, callable $callback): void
    {
        $this->loop = $this->loop ?? EventLoopFactory::create();

        $server = new Server(function (ServerRequestInterface $request) {
            return new Promise(function ($resolve, $reject) use ($request) {
                try {
                    call_user_func(
                        $this->getHandler($request),
                        $this->toSymfonyRequest($request),
                        Response::create($resolve)
                    );
                } catch (\Exception $exception) {
                    $response = new \React\Http\Response(
                        500,
                        array(
                            'Content-Type' => 'text/plain'
                        ),
                        $exception->getMessage()
                    );
                    $resolve($response);
                } catch (\Throwable $throwable) {
                    $response = new \React\Http\Response(
                        500,
                        array(
                            'Content-Type' => 'text/plain'
                        ),
                        $throwable->getMessage()
                    );
                    $resolve($response);
                }
            });
        });

        $server->listen($socket = new SocketServer($port, $this->loop));

        $callback();

        $this->loop->run();
    }

    private function toSymfonyRequest(ServerRequestInterface $request): Request
    {
        $options = $this->urlMatcher->match($request->getUri()->getPath());

        $route = $this->routes->get($options['_route']);
        $variables = $route->compile()->getVariables();

        $symfonyRequest = $this->httpFoundationFactory->createRequest($request);
        $symfonyRequest->request->add(
            filter(function ($value, $key) use ($variables): bool {
                return in_array($key, $variables);
            }, $options)
        );

        return $symfonyRequest;
    }

    private function getHandler(ServerRequestInterface $request): callable
    {
        return $this->urlMatcher->match($request->getUri()->getPath())['handler'];
    }
}
