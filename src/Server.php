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
use TSwiackiewicz\EventsCollector\Configuration\Configuration;
use TSwiackiewicz\EventsCollector\Routing\RoutesCollection;

class Server
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var string
     */
    private $configurationDumpFile;

    /**
     * @param Dispatcher $dispatcher
     * @param string $configurationDumpFile
     */
    public function __construct(Dispatcher $dispatcher, $configurationDumpFile)
    {
        $this->dispatcher = $dispatcher;
        $this->configurationDumpFile = $configurationDumpFile;
    }

    /**
     * @param RoutesCollection $routes
     * @param Configuration $configuration
     * @param string $configurationDumpFile
     * @return Server
     */
    public static function create(RoutesCollection $routes, Configuration $configuration, $configurationDumpFile)
    {
        return new Server(
            new Dispatcher(
                new FastRouteGroupCountBasedDispatcher(
                    $routes->getRoutes()
                ),
                $configuration
            ),
            $configurationDumpFile
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

        $socket->listen($port, $host);

        $this->dumpConfiguration($loop);

        $loop->run();
    }

    /**
     * @param LoopInterface $loop
     */
    private function dumpConfiguration(LoopInterface $loop)
    {
        $interval = 10;

        $configuration = $this->dispatcher->getConfiguration();
        $dumpFile = $this->configurationDumpFile;
        $loop->addPeriodicTimer($interval, function () use ($configuration, $dumpFile) {
            $configuration->dump($dumpFile);
        });
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