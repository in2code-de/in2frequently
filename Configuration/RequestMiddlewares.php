<?php

declare(strict_types=1);

use In2code\In2frequently\Middleware\FrequentlyCacheMiddleware;

return [
    'frontend' => [
        'in2frequently/frequently-cache' => [
            'target' => FrequentlyCacheMiddleware::class,
            'after' => [
                'typo3/cms-frontend/authentication',
                'staticfilecache/generate',
            ],
            'before' => [
                'typo3/cms-frontend/content-length-headers',
            ],
        ],
    ],
];
