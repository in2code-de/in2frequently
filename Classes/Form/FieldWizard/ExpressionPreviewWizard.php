<?php

declare(strict_types=1);

namespace In2code\In2frequently\Form\FieldWizard;

use In2code\In2frequently\Domain\Service\ExpressionResolver;
use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;

class ExpressionPreviewWizard extends AbstractNode
{
    public function __construct(
        private readonly ExpressionResolver $expressionResolver,
        private readonly ViewFactoryInterface $viewFactory,
    ) {
    }

    public function render(): array
    {
        $result = $this->initializeResultArray();
        $expression = $this->data['databaseRow'][$this->data['fieldName']] ?? '';
        if ($expression !== '') {
            $result['html'] = $this->buildPreviewHtml($expression);
        }
        return $result;
    }

    protected function buildPreviewHtml(string $expression): string
    {
        $viewFactoryData = new ViewFactoryData(
            templatePathAndFilename: 'EXT:in2frequently/Resources/Private/Templates/Form/ExpressionPreview.html'
        );
        $view = $this->viewFactory->create($viewFactoryData);
        try {
            $view->assignMultiple([
                'times' => $this->expressionResolver->resolveUpcomingDates($expression),
                'cronExpression' => $this->expressionResolver->resolveCronExpression($expression),
            ]);
        } catch (\Throwable) {
        }
        return $view->render();
    }
}
