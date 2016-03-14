<?php
namespace TSwiackiewicz\EventsCollector;

use FastRoute\Dispatcher as FastRouteDispatcher;
use FastRoute\Dispatcher\GroupCountBased as FastRouteGroupCountBasedDispatcher;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Http\Request as HttpRequest;
use React\Http\Response as HttpResponse;
use React\Http\Server as HttpServer;
use React\Socket\Server as SocketServer;
use TSwiackiewicz\EventsCollector\Routing\RoutesCollection;

/**
 * Class Server
 * @package TSwiackiewicz\EventsCollector
 */
class Server
{
    const DEFAULT_HOST = '127.0.0.1';

    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var SocketServer
     */
    private $socket;

    /**
     * @var HttpServer
     */
    private $http;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @param LoopInterface $loop
     * @param SocketServer $socket
     * @param HttpServer $http
     * @param Dispatcher $dispatcher
     */
    public function __construct(LoopInterface $loop, SocketServer $socket, HttpServer $http, Dispatcher $dispatcher)
    {
        $this->loop = $loop;
        $this->socket = $socket;
        $this->http = $http;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param RoutesCollection $routes
     * @param ControllerFactory $factory
     * @return Server
     */
    public static function create(RoutesCollection $routes, ControllerFactory $factory)
    {
        $loop = Factory::create();
        $socket = new SocketServer($loop);
        $http = new HttpServer($socket);

        return new Server(
            $loop,
            $socket,
            $http,
            new Dispatcher(
                new FastRouteGroupCountBasedDispatcher($routes->getRoutes()),
                $factory
            )
        );
    }

    /**
     * @param int $port
     * @param string $host
     */
    public function listen($port, $host = self::DEFAULT_HOST)
    {
        $this->http->on('request', [$this, 'onRequest']);
        $this->socket->listen($port, $host);

        $this->dumpConfiguration();

        $this->loop->run();
    }

    private function dumpConfiguration()
    {
        $interval = getenv('CONFIGURATION_DUMP_INTERVAL');

        if (is_numeric($interval) && $interval > 0) {
            $dumpFilePath = getenv('CONFIGURATION_DUMP_FILE_PATH');
            /** @noinspection PhpParamsInspection */
            $this->loop->addPeriodicTimer($interval, function () use ($dumpFilePath) {
                $this->dispatcher->dumpSettings($dumpFilePath);
            });
        }
    }

    /**
     * @param HttpRequest $request
     * @param HttpResponse $response
     */
    public function onRequest(HttpRequest $request, HttpResponse $response)
    {
        $payload = '';
        $request->on('data', function ($data) use ($request, &$payload) {
            $payload .= $data;
            $request->close();
        });

        $request->on('end', function () use ($request, $response, &$payload) {
            $return = $this->dispatcher->dispatch(
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