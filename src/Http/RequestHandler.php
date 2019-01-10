<?php declare(strict_types=1);

namespace App\Http;

/**
 * Import classes
 */
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Sunrise\Http\Factory\ResponseFactory;
use Sunrise\Http\Router\Exception\MethodNotAllowedException;
use Sunrise\Http\Router\Exception\RouteNotFoundException;
use Sunrise\Http\Router\RouterInterface;

/**
 * RequestHandler
 */
class RequestHandler implements RequestHandlerInterface
{

	/**
	 * Logger
	 *
	 * @var LoggerInterface
	 *
	 * @Inject
	 */
	protected $logger;

	/**
	 * Router
	 *
	 * @var RouterInterface
	 *
	 * @Inject
	 */
	protected $router;

	/**
	 * Handles the given request
	 *
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request) : ResponseInterface
	{
		try
		{
			$response = $this->router->handle($request);
		}
		catch (RouteNotFoundException $e)
		{
			$response = (new ResponseFactory)->createResponse(404);
		}
		catch (MethodNotAllowedException $e)
		{
			$response = (new ResponseFactory)->createResponse(405)
			->withHeader('allow', implode(', ', $e->getAllowedMethods()));
		}
		catch (\Throwable $e)
		{
			$response = (new ResponseFactory)->createResponse(500);

			$this->logger->error($e->getMessage(), ['exception' => $e]);
		}

		return $response;
	}
}
