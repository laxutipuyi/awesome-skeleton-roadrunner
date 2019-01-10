<?php declare(strict_types=1);

use App\Http\RequestHandler;
use Spiral\Goridge\StreamRelay;
use Spiral\RoadRunner\PSR7Client;
use Spiral\RoadRunner\Worker;

chdir(__DIR__);
require_once 'vendor/autoload.php';

$relay = new StreamRelay(STDIN, STDOUT);
$client = new PSR7Client(new Worker($relay));

$container = require 'config/container.php';
$handler = $container->get(RequestHandler::class);

while ($request = $client->acceptRequest()) {
	$client->respond($handler->handle($request));
	gc_collect_cycles();
}
