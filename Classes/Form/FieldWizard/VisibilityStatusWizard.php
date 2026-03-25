<?php

declare(strict_types=1);

namespace In2code\In2frequently\Form\FieldWizard;

use In2code\In2frequently\Domain\Service\FrequentlyVisibilityService;
use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Domain\Access\RecordAccessVoter;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;

class VisibilityStatusWizard extends AbstractNode
{
    public function __construct(
        private readonly FrequentlyVisibilityService $visibilityService,
        private readonly ViewFactoryInterface $viewFactory,
        private readonly RecordAccessVoter $recordAccessVoter,
    ) {
    }

    public function render(): array
    {
        $result = $this->initializeResultArray();
        $row = $this->data['databaseRow'];
        $isActive = (int)($row['tx_in2frequently_active'] ?? 0) === 1;

        if ($isActive) {
            $result['html'] = $this->buildStatusHtml(
                (string)($row['tx_in2frequently_starttime'] ?? ''),
                (string)($row['tx_in2frequently_endtime'] ?? ''),
                $row,
            );
        }

        return $result;
    }

    protected function buildStatusHtml(string $startExpression, string $endExpression, array $row): string
    {
        $viewFactoryData = new ViewFactoryData(
            templatePathAndFilename: 'EXT:in2frequently/Resources/Private/Templates/Form/VisibilityStatus.html'
        );
        $view = $this->viewFactory->create($viewFactoryData);
        $isFrequentlyVisible = $this->visibilityService->isVisible($startExpression, $endExpression);
        $isNativeVisible = $this->isNativeFieldVisible($row);

        $view->assign('isVisible', $isNativeVisible && $isFrequentlyVisible);

        return $view->render();
    }

    protected function isNativeFieldVisible(array $row): bool
    {
        $context = new Context();
        return $this->recordAccessVoter->accessGranted('tt_content', $row, $context);
    }
}
