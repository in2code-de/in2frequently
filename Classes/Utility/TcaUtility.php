<?php

declare(strict_types=1);

namespace In2code\In2frequently\Utility;

class TcaUtility
{
    protected const LANGUAGE_FILE_PREFIX = 'LLL:EXT:in2frequently/Resources/Private/Language/backend.xlf:';

    public static function getValuePickerItems(): array
    {
        return [
            [self::LANGUAGE_FILE_PREFIX . 'valuePicker.everyDay', 'every day'],
            [self::LANGUAGE_FILE_PREFIX . 'valuePicker.everyDayAt3am', 'every day at 3 AM'],
            [self::LANGUAGE_FILE_PREFIX . 'valuePicker.everyFirst', 'every 1st'],
            [self::LANGUAGE_FILE_PREFIX . 'valuePicker.everyFifteenth', 'every 15th'],
            [self::LANGUAGE_FILE_PREFIX . 'valuePicker.everyTwentySeventh', 'every 27th'],
            [self::LANGUAGE_FILE_PREFIX . 'valuePicker.everyTwentySeventhMidnight', 'every 27th midnight'],
            [self::LANGUAGE_FILE_PREFIX . 'valuePicker.everyFirstAt8am', 'every 1st at 8am'],
            [self::LANGUAGE_FILE_PREFIX . 'valuePicker.everyFifteenthAt8am', 'every 15th at 8am'],
            [self::LANGUAGE_FILE_PREFIX . 'valuePicker.everyFridayAt5am', 'every friday at 17:00'],
        ];
    }
}
