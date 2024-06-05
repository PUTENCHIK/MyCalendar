<?php

namespace Logic\TasksList;

$path = $_SERVER['DOCUMENT_ROOT'];

use App\App;
use App\AppStates;
use App\MySQLConnector;
use App\CalendarFilters;

include_once $path.'/src/app/App.php';
include_once $path.'/src/app/AppStates.php';
include_once $path.'/src/app/CalendarFilters.php';

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['app'])) {
    $app = $_SESSION['app'];

    header('Content-Type: text/plain');
//    print_r($_POST);
//    exit;

    if ($_POST) {
        $db = new MySQLConnector();
        if (isset($_POST['add-task'])) {
            $app->setState(AppStates::$adding);
            header('Location: ../templates/task_page.php');
            exit;
        }
        elseif (isset($_POST['change'])) {
            $n = $_POST['change'];
            $db_task = $db->get_task($n);

            if (! is_null($db_task)) {
                $app->setCurrentTask($n);
                $app->setState(AppStates::$changing);
                $app->setInputs($db_task);
                header('Location: ../templates/task_page.php');
                exit;
            }
        } elseif (isset($_POST['is-completed'])) {
            $db_task = $db->get_task($_POST['is-completed']);
            if (!is_null($db_task)) {
                $db->set_task_completeness(
                    $db_task['id'],
                    !(int)($db_task['is_completed']),
                );
            }
        } elseif (isset($_POST['task-filter'])) {
            try {
                $filter_id = CalendarFilters::get_filter(name: $_POST['task-filter']);
                $app->setFilter($filter_id);
                if ($app->is_filter(CalendarFilters::$other)) {
                    if (isset($_POST['date-filter'])) {
                        switch ($_POST['date-filter']) {
                            case 'today':
                                $start = date('Y-m-d 00:00:00', time());
                                $end = date('Y-m-d 23:59:59', time());
                                break;
                            case 'tomorrow':
                                $start = date('Y-m-d 00:00:00', strtotime('tomorrow'));
                                $end = date('Y-m-d 23:59:59', strtotime('tomorrow'));
                                break;
                            case 'this-week':
                                $day = date('w');
                                $start = date('Y-m-d 00:00:00', strtotime('-' . ((int)$day-1) . ' days'));
                                $end = date('Y-m-d 23:59:59', strtotime('+' . (7-$day) . ' days'));
                                break;
                            case 'next-week':
                                $day = date('w');
                                $start = date('Y-m-d 00:00:00', strtotime('+' . (8-$day) . ' days'));
                                $end = date('Y-m-d 23:59:59', strtotime('+' . (14-$day) . ' days'));
                                break;

                            default:
                                $start = $app->getDateInterval()['start'];
                                $end = $app->getDateInterval()['end'];
                                break;
                        }
                        $app->setDateInterval($start, $end);
                    } elseif (!empty($_POST['certain-date'])) {
                        $app->setDateInterval(
                            $_POST['certain-date'] . ' 00:00:00',
                            $_POST['certain-date'] . ' 23:59:59',
                        );
                    } else {
                        $app->setDateInterval(
                            date('Y-m-d 00:00:00', time()),
                            date('Y-m-d 23:59:59', time()),
                        );
                    }
                } else {
                    $app->clear_interval();
                }
            } catch (\Exception $e) {
                $app->setFilter(CalendarFilters::$current);
            }
        }
    }
}

header('Location: ../templates/tasks_list.php');
exit;