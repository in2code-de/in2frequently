<?php

declare(strict_types=1);

namespace In2code\In2frequently\Middleware;

use In2code\In2frequently\Domain\Service\FrequentlyVisibilityService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Cache\CacheDataCollector;
use TYPO3\CMS\Core\Routing\PageArguments;

class FrequentlyCacheMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly FrequentlyVisibilityService $visibilityService,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $routing = $request->getAttribute('routing');
        $cacheDataCollector = $request->getAttribute('frontend.cache.collector');
        if ($routing instanceof PageArguments && $cacheDataCollector instanceof CacheDataCollector) {
            $this->visibilityService->restrictLifetimeForPage($routing->getPageId(), $cacheDataCollector);
        }
        return $response;
    }
}
