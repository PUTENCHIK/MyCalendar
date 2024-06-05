<?php

namespace Logic\TaskPage;

$path = $_SERVER['DOCUMENT_ROOT'];

use App\App;
use App\AppStates;
use App\MySQLConnector;
use function App\e;

include_once $path.'/src/app/AppStates.php';
include_once $path.'/src/app/App.php';

include_once $path.'/src/functions.php';

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['app'])) {
    $app = $_SESSION['app'];

//    header('Content-Type: text/plain');
//    print_r($_POST);

    if ($_POST) {
        $app->clear_errors();
        if (isset($_POST['to-main'])) {
            $app->setState(AppStates::$list);
            header('Location: ../templates/tasks_list.php');
            exit;
        }
        elseif (isset($_POST['reset'])) {
            $app->clear_errors();
            $app->clear_inputs();
            header('Location: ../templates/task_page.php');
            exit;
        }
        elseif (isset($_POST['save'])) {
            $data = [
                'theme' => e($_POST['theme'] ?? ''),
                'type' => e($_POST['type'] ?? ''),
                'place' => e($_POST['place'] ?? ''),
                'date' => e($_POST['date'] ?? ''),
                'time' => e($_POST['time'] ?? ''),
                'duration' => e($_POST['duration'] ?? ''),
                'comment' => e($_POST['comment'] ?? ''),
            ];

            $app->setInputs($data);

            foreach ($data as $key => $value) {
                if (empty($value)) {
                    $app->add_error($key, 'Незаполнено значение');
                }
            }

            if (! empty($app->getErrors())) {
//                print_r($app->getErrors());
                header('Location: ../templates/task_page.php');
                exit;
            }

            try {
                $db = new MySQLConnector();
                if ($app->is_state(AppStates::$adding)) {
                    $db->add_task(
                        $data['theme'],
                        $data['type'],
                        $data['place'],
                        $data['date'] . ' ' . $data['time'],
                        $data['duration'],
                        $data['comment'],
                    );
                } elseif ($app->is_state(AppStates::$changing)) {
                    $db->update_task(
                        $app->getCurrentTask(),
                        $data['theme'],
                        $data['type'],
                        $data['place'],
                        $data['date'] . ' ' . $data['time'],
                        $data['duration'],
                        $data['comment'],
                    );
                }

                $app->setState(AppStates::$list);
                header('Location: ../templates/tasks_list.php');
                exit;
            } catch (\Exception $e) {
                $app->add_error('type', $e->getMessage());
                header('Location: ../templates/task_page.php');
                exit;
            }
        }
    }
}

header('Location ../templates/tasks_list.php');
exit;
