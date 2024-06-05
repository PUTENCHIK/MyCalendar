<?php


namespace App;


class CalendarFilters {
    public static int $current = 1;
    public static int $old = 2;
    public static int $completed = 3;
    public static int $other = 4;
    public static int $all = 5;

    public static function get_filter(int $id = null, string $name = null): mixed {
        if (!is_null($id)) {
            return self::get_filters()[$id-1]['value'];
        } else if (!is_null($name)) {
            foreach (self::get_filters() as $index => $filter) {
                if ($filter['value'] === $name) {
                    return $index+1;
                }
            }
            throw new \Exception("No filter's name: $name");
        } else {
            throw new \Exception('No need params: $id or $name');
        }
    }

    public static function get_filters(): array {
        return [
            ['value' => 'current', 'label' => 'Текущие задачи'],
            ['value' => 'old', 'label' => 'Просроченные задачи'],
            ['value' => 'completed', 'label' => 'Выполненные задачи'],
            ['value' => 'other', 'label' => 'Задачи на конкретные даты'],
            ['value' => 'all', 'label' => 'Все задачи'],
        ];
    }
}