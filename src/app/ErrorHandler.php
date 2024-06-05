<?php


namespace App;


class ErrorHandler {
    public static function add_error_block(string $expected, ?array $error): string {
//        print_r($expected);
//        print_r($error);

        if (is_null($error) or !isset($error['location']) or !isset($error['message']) or
                $expected !== $error['location']) {
            return '';
        } else {
            $message = $error['message'];
            return "<div class='error-row'><div></div><div class='error-block'>$message</div></div>";
        }
    }
}