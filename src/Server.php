<?php
namespace TSwiackiewicz\EventsCollector;

use FastRoute\Dispatcher as FastRouteDispatcher;
use FastRoute\Dispatcher\GroupCountBased as FastRouteGroupCountBasedDispatcher;
use React\EventLoop\Factory;
use React\Http\Request as HttpRequest;
use React\Http\Response as HttpResponse;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;
use TSwiackiewicz\EventsCollector\Configuration\Configuration;
use TSwiackiewicz\EventsCollector\Routing\RoutesCollection;

class Server
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param RoutesCollection $routes
     * @param Configuration $configuration
     * @return Server
     */
    public static function create(RoutesCollection $routes, Configuration $configuration)
    {
        return new Server(
            new Dispatcher(
                new FastRouteGroupCountBasedDispatcher(
                    $routes->getRoutes()
                ),
                $configuration
            )
        );
    }

    /**
     * @param int $port
     * @param string $host
     */
    public function listen($port, $host = '127.0.0.1')
    {
        $loop = Factory::create();

        $socket = new SocketServer($loop);
        $http = new HttpServer($socket);

        $http->on('request', [$this, 'onRequest']);

        echo("Server running on {$host}:{$port}\n");

        $socket->listen($port, $host);

        $loop->run();
    }

    public function onRequest(HttpRequest $request, HttpResponse $response)
    {
        $payload = '';
        $request->on('data', function ($data) use ($request, &$payload) {
            $payload .= $data;
            $request->close();
        });

        $dispatcher = $this->dispatcher;
        $request->on('end', function () use ($dispatcher, $request, $response, &$payload) {
            $return = $dispatcher->dispatch(
                $request->getMethod(),
                $request->getPath(),
                $payload
            );

            $response->writeHead(
                $return->getStatusCode(),
                $return->headers->allPreserveCase()
            );
            $response->write(
                $return->getContent()
            );
            $response->end();
        });
    }
}