<?php

namespace App;


function e(string $text): string {
    return htmlspecialchars(trim($text));
}

function set_old_value(string $value): string {
    if (empty($value)) {
        return '';
    } else {
        $value = e($value);
        return "value='$value'";
    }
}

function select_if(string $value, string $expected): string {
    return $value === $expected ? 'selected' : '';
}

function check_if(bool $value): string {
    return $value ? 'checked' : '';
}