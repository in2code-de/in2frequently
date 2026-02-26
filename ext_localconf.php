<?php

declare(strict_types=1);

use In2code\In2frequently\Form\FieldWizard\ExpressionPreviewWizard;

defined('TYPO3') || die();

/**
 * Add Preview for upcoming 3 dates in backend to the new fields
 */
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1740481001] = [
    'nodeName' => 'in2frequentlyExpressionPreview',
    'priority' => 40,
    'class' => ExpressionPreviewWizard::class,
];
