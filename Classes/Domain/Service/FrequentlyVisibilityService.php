<?php

declare(strict_types=1);

namespace In2code\In2frequently\Domain\Service;

use DateTimeImmutable;
use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Cache\CacheDataCollector;
use TYPO3\CMS\Core\Database\ConnectionPool;

class FrequentlyVisibilityService
{
    protected const TABLE_NAME = 'tt_content';
    protected const FIELD_ACTIVE = 'tx_in2frequently_active';
    protected const FIELD_STARTTIME = 'tx_in2frequently_starttime';
    protected const FIELD_ENDTIME = 'tx_in2frequently_endtime';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly ExpressionResolver $expressionResolver,
    ) {
    }

    public function isVisible(string $startExpression, string $endExpression): bool
    {
        $visible = true;
        $now = new DateTimeImmutable('now');
        if ($startExpression !== '' && $endExpression !== '') {
            try {
                $nextStop = $this->expressionResolver->resolveUpcomingDate($endExpression);
                $lastStart = $this->expressionResolver->resolveLastDate($startExpression);
                $lastStop = $this->expressionResolver->resolveLastDate($endExpression);
                return $lastStart <= $now && $now <= $nextStop && $lastStop < $lastStart;
            } catch (\Throwable) {
                // Invalid expression – no restriction applied
            }
        }
        return $visible;
    }

    /**
     * Can be called from middleware to change frontend caches
     *
     * @param int $pageId
     * @param CacheDataCollector $cacheDataCollector
     * @return void
     */
    public function restrictLifetimeForPage(int $pageId, CacheDataCollector $cacheDataCollector): void
    {
        $records = $this->getAllCronableContentElementsByPageIdentifier($pageId);
        foreach ($records as $record) {
            $cacheInvalidationDate = $this->calculateCacheInvalidationDate(
                $record[self::FIELD_STARTTIME] ?? '',
                $record[self::FIELD_ENDTIME] ?? ''
            );
            if ($cacheInvalidationDate !== null) {
                $remainingSeconds = max(0, $cacheInvalidationDate->getTimestamp() - time());
                // Can be called multiple times and TYPO3 uses smallest seconds for cache invalidation
                $cacheDataCollector->restrictMaximumLifetime($remainingSeconds);
            }
        }
    }

    protected function getAllCronableContentElementsByPageIdentifier(int $pageIdentifier): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
        $queryBuilder->getRestrictions()->removeAll();

        return $queryBuilder
            ->select('uid', self::FIELD_STARTTIME, self::FIELD_ENDTIME)
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($pageIdentifier, ParameterType::INTEGER)
                ),
                $queryBuilder->expr()->eq(
                    self::FIELD_ACTIVE,
                    $queryBuilder->createNamedParameter(1, ParameterType::INTEGER)
                ),
                $queryBuilder->expr()->eq(
                    'deleted',
                    $queryBuilder->createNamedParameter(0, ParameterType::INTEGER)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }

    protected function calculateCacheInvalidationDate(string $startExpression, string $endExpression): ?DateTimeImmutable
    {
        try {
            $visible = $this->isVisible($startExpression, $endExpression);
            if ($visible === true) {
                $cacheInvalidationDate = $this->expressionResolver->resolveUpcomingDate($endExpression);
            } else {
                $cacheInvalidationDate = $this->expressionResolver->resolveUpcomingDate($startExpression);
            }
            return $cacheInvalidationDate;
        } catch (\Throwable $exception) {
            return null;
        }
    }
}
