<?php

declare(strict_types=1);

namespace In2code\In2frequently\TCA;

use TYPO3\CMS\Core\Configuration\Event\AfterTcaCompilationEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class AddPaletteToAccessTabListener
{
    protected const PALETTE_ACCESS = '--palette--;;access';
    protected const PALETTE_VISIBILITY = '--palette--;;in2frequently_visibility';

    public function __invoke(AfterTcaCompilationEvent $event): void
    {
        $tca = $event->getTca();
        if (isset($tca['tt_content']['types'])) {
            foreach ($tca['tt_content']['types'] as &$typeConfig) {
                if (isset($typeConfig['showitem'])) {
                    $parts = GeneralUtility::trimExplode(',', $typeConfig['showitem'], true);
                    $accessIndex = array_search(self::PALETTE_ACCESS, $parts, true);

                    if ($accessIndex !== false) {
                        array_splice($parts, $accessIndex + 1, 0, [self::PALETTE_VISIBILITY]);
                        $typeConfig['showitem'] = implode(', ', $parts);
                    }
                }
            }
            unset($typeConfig);
            $event->setTca($tca);
        }
    }
}
