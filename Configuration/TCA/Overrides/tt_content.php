<?php

declare(strict_types=1);

defined('TYPO3') || die();

use In2code\In2frequently\Utility\TcaUtility;

$languageFilePrefix = 'LLL:EXT:in2frequently/Resources/Private/Language/backend.xlf:';

$tca = [
    'columns' => [
        'tx_in2frequently_active' => [
            'label' => $languageFilePrefix . 'TCA.frequently_active',
            'onChange' => 'reload',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
                'fieldWizard' => [
                    'in2frequentlyVisibilityStatus' => [
                        'renderType' => 'in2frequentlyVisibilityStatus',
                    ],
                ],
            ],
        ],
        'tx_in2frequently_starttime' => [
            'label' => $languageFilePrefix . 'TCA.frequently_starttime',
            'displayCond' => 'FIELD:tx_in2frequently_active:REQ:true',
            'config' => [
                'type' => 'input',
                'default' => '',
                'placeholder' => 'first day of this month',
                'size' => 50,
                'eval' => 'trim',
                'valuePicker' => [
                    'items' => TcaUtility::getValuePickerItems(),
                ],
                'fieldWizard' => [
                    'in2frequentlyExpressionPreview' => [
                        'renderType' => 'in2frequentlyExpressionPreview',
                    ],
                ],
            ],
        ],
        'tx_in2frequently_endtime' => [
            'label' => $languageFilePrefix . 'TCA.frequently_endtime',
            'displayCond' => 'FIELD:tx_in2frequently_active:REQ:true',
            'config' => [
                'type' => 'input',
                'default' => '',
                'placeholder' => 'last day of this month',
                'size' => 50,
                'eval' => 'trim',
                'valuePicker' => [
                    'items' => TcaUtility::getValuePickerItems(),
                ],
                'fieldWizard' => [
                    'in2frequentlyExpressionPreview' => [
                        'renderType' => 'in2frequentlyExpressionPreview',
                    ],
                ],
            ],
        ],
    ],
    'palettes' => [
        'in2frequently_visibility' => [
            'label' => $languageFilePrefix . 'palette.in2frequently_visibility',
            'showitem' => 'tx_in2frequently_active, --linebreak--, tx_in2frequently_starttime, tx_in2frequently_endtime',
        ],
    ],
];

$GLOBALS['TCA']['tt_content'] = array_merge_recursive($GLOBALS['TCA']['tt_content'], $tca);
