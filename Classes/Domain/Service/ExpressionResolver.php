<?php

declare(strict_types=1);

namespace In2code\In2frequently\Domain\Service;

use BenTools\NaturalCronExpression\NaturalCronExpressionParser;
use Cron\CronExpression;
use DateTimeImmutable;

class ExpressionResolver
{
    protected const UPCOMING_DATES_COUNT = 3;

    public function resolveUpcomingDate(string $expression): DateTimeImmutable
    {
        $cronString = $this->resolveCronExpression($expression);
        $nextRun = (new CronExpression($cronString))->getNextRunDate(
            (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
            0,
            true
        );
        return DateTimeImmutable::createFromMutable($nextRun);
    }

    public function resolveLastDate(string $expression): DateTimeImmutable
    {
        $cronString = $this->resolveCronExpression($expression);
        $nextRun = (new CronExpression($cronString))->getPreviousRunDate(
            (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
            0,
            true
        );
        return DateTimeImmutable::createFromMutable($nextRun);
    }

    /**
     * @return DateTimeImmutable[]
     */
    public function resolveUpcomingDates(string $expression): array
    {
        $cronString = $this->resolveCronExpression($expression);
        $runs = (new CronExpression($cronString))->getMultipleRunDates(
            self::UPCOMING_DATES_COUNT,
            (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
            false,
            true
        );
        return array_map(
            static fn (\DateTime $date) => DateTimeImmutable::createFromMutable($date),
            $runs
        );
    }

    public function resolveCronExpression(string $expression): string
    {
        if ($this->isRawCronExpression($expression)) {
            return $expression;
        }
        set_error_handler(static fn () => true);
        try {
            $cronString = NaturalCronExpressionParser::fromString($expression);
        } finally {
            restore_error_handler();
        }
        return $cronString;
    }

    private function isRawCronExpression(string $expression): bool
    {
        return preg_match('~' . NaturalCronExpressionParser::VALID_PATTERN . '~', $expression) === 1;
    }
}
