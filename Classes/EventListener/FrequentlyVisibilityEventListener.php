<?php

declare(strict_types=1);

namespace In2code\In2frequently\EventListener;

use In2code\In2frequently\Domain\Service\FrequentlyVisibilityService;
use TYPO3\CMS\Frontend\ContentObject\Event\ModifyRecordsAfterFetchingContentEvent;

class FrequentlyVisibilityEventListener
{
    protected const TABLE_NAME = 'tt_content';

    public function __construct(
        private readonly FrequentlyVisibilityService $visibilityService,
    ) {
    }

    public function __invoke(ModifyRecordsAfterFetchingContentEvent $event): void
    {
        if (($event->getConfiguration()['table'] ?? '') === self::TABLE_NAME) {
            $filteredRecords = array_filter(
                $event->getRecords(),
                fn (array $record): bool => $this->isRecordVisible($record)
            );
            $event->setRecords(array_values($filteredRecords));
        }
    }

    protected function isRecordVisible(array $record): bool
    {
        if ((int)($record['tx_in2frequently_active'] ?? 0) === 1) {
            return $this->visibilityService->isVisible(
                (string)($record['tx_in2frequently_starttime'] ?? ''),
                (string)($record['tx_in2frequently_endtime'] ?? '')
            );
        }
        return true;
    }
}
