<?php


namespace App;

include_once "MySQLConnector.php";


class App {
    private int $state;
    private ?int $current_task;
    private array $errors;
    private array $inputs;
    private int $filter;
    private array $date_interval;

    public function __construct() {
        $this->state = AppStates::$list;
        $this->current_task = null;
        $this->errors = [];
        $this->filter = CalendarFilters::$current;
        $this->clear_interval();
    }

    public function getState(): int {
        return $this->state;
    }

    public function setState(int $state): void {
        $this->state = $state;
    }

    public function getCurrentTask(): ?int {
        return $this->current_task;
    }

    public function setCurrentTask(?int $current_task): void {
        $this->current_task = $current_task;
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function clear_errors(): void {
        $this->errors = [];
    }

    public function add_error(string $location, string $message): void {
        $this->errors[] = [
            'location' => $location,
            'message' => $message,
        ];
    }

    public function getInputs(): array {
        return $this->inputs;
    }

    public function setInputs(array $inputs): void {
        $this->inputs = $inputs;
    }

    public function clear_inputs(): void {
        $this->inputs = [];
    }

    public function getFilter(): int {
        return $this->filter;
    }

    public function setFilter(int $filter): void {
        $this->filter = $filter;
    }

    public function is_filter(int $expected): bool {
        return $this->getFilter() === $expected;
    }

    public function check_state(int $expected): void {
        if (in_array($this->getState(), [AppStates::$adding, AppStates::$changing]) and $expected === AppStates::$list) {
            header('Location: ../templates/task_page.php');
        } elseif ($this->getState() === AppStates::$list and in_array($expected, [AppStates::$adding, AppStates::$changing])) {
            header('Location: ../templates/tasks_list.php');
        }
    }

    public function getDateInterval(): array {
        return $this->date_interval;
    }

    public function setDateInterval(string $start, string $end): void {
        $this->date_interval['start'] = $start;
        $this->date_interval['end'] = $end;
    }

    public function clear_interval(): void {
        $this->date_interval = [
            'start' => null,
            'end' => null,
        ];
    }

    public function is_state(int $expected): bool {
        return $this->getState() === $expected;
    }

    public function info(): array {
        return [
            'state' => $this->getState(),
            'current_task' => $this->getCurrentTask(),
            'errors' => $this->getState(),
            'inputs' => $this->getInputs(),
            'filter' => $this->getFilter(),
            'date_interval' => $this->getDateInterval(),
        ];
    }
}