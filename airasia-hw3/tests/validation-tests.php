<?php
require_once __DIR__ . '/../includes/validation.php';

if (PHP_SAPI !== 'cli' && !headers_sent()) {
    header('Content-Type: text/plain; charset=UTF-8');
}

$tests = [
    'Valid Gift Card data passes validation' => static function (): bool {
        return isValidCardName('Amazon Gift Card')
            && isValidCardType('Retail')
            && isValidCardValue('$25.00')
            && isValidPointsRequired('1,000')
            && isValidGiftCardId('12');
    },
    'Blank Card Name fails validation' => static function (): bool {
        return !isValidCardName('   ');
    },
    'Overlong Card Name fails validation' => static function (): bool {
        return !isValidCardName(str_repeat('A', 101));
    },
    'Altered or invalid Card Type fails validation' => static function (): bool {
        return !isValidCardType('Amazon');
    },
    'Zero or negative Card Value fails validation' => static function (): bool {
        return !isValidCardValue('0') && !isValidCardValue('-25.00');
    },
    'Invalid Points Required fails validation' => static function (): bool {
        return !isValidPointsRequired('100.5')
            && !isValidPointsRequired('1,00')
            && !isValidPointsRequired('0');
    },
    'Invalid Gift Card identifier fails validation' => static function (): bool {
        return !isValidGiftCardId('12abc') && !isValidGiftCardId('0');
    },
];

$failures = 0;
foreach ($tests as $name => $test) {
    try {
        $passed = $test();
        $detail = '';
    } catch (Throwable $error) {
        $passed = false;
        $detail = ' (' . $error->getMessage() . ')';
    }

    echo ($passed ? 'PASS' : 'FAIL') . ': ' . $name . $detail . PHP_EOL;
    if (!$passed) {
        $failures++;
    }
}

echo count($tests) . ' tests; ' . $failures . ' failed.' . PHP_EOL;
exit($failures === 0 ? 0 : 1);
