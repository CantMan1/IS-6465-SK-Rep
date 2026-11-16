<?php

function getAllowedCardTypes(): array
{
    return ['Retail', 'Travel', 'Dining', 'Entertainment', 'Other'];
}

function isValidCardName($value): bool
{
    if (!is_string($value)) {
        return false;
    }

    $name = trim($value);
    $length = preg_match_all('/./us', $name);

    return $length !== false && $length > 0 && $length <= 100;
}

function isValidCardType($value): bool
{
    return is_string($value)
        && in_array(trim($value), getAllowedCardTypes(), true);
}

function isValidCardValue($value): bool
{
    if (is_int($value) || is_float($value)) {
        return is_finite((float) $value) && $value > 0;
    }

    if (!is_string($value)) {
        return false;
    }

    $value = trim($value);
    if (preg_match('/^\$?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d{1,2})?$/D', $value) !== 1) {
        return false;
    }

    $amount = (float) str_replace(['$', ','], '', $value);
    return is_finite($amount) && $amount > 0;
}

function isValidPointsRequired($value): bool
{
    if (is_int($value)) {
        return $value > 0;
    }

    if (!is_string($value)) {
        return false;
    }

    $value = trim($value);
    if (preg_match('/^(?:\d+|\d{1,3}(?:,\d{3})+)$/D', $value) !== 1) {
        return false;
    }

    $normalized = ltrim(str_replace(',', '', $value), '0');
    $points = filter_var(
        $normalized === '' ? '0' : $normalized,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    return $points !== false;
}

function isValidGiftCardId($value): bool
{
    if (is_int($value)) {
        return $value > 0;
    }

    if (!is_string($value)) {
        return false;
    }

    $id = filter_var(trim($value), FILTER_VALIDATE_INT);
    return $id !== false && $id > 0;
}