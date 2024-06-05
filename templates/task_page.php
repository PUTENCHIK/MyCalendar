<?php

namespace Pages\TaskPage;

$path = $_SERVER['DOCUMENT_ROOT'];

use App\App;
use App\AppStates;
use App\MySQLConnector;
use App\ErrorHandler;
use function App\select_if;
use function App\set_old_value;

include_once $path.'/src/app/AppStates.php';
include_once $path.'/src/app/App.php';
include_once $path.'/src/app/MySQLConnector.php';
include_once $path.'/src/app/ErrorHandler.php';

include_once $path.'/src/functions.php';

if (!isset($_SESSION)) {
	session_start();
}

if (!isset($_SESSION['app'])) {
    $_SESSION['app'] = new App();
}

$app = $_SESSION['app'];
$app->check_state(AppStates::$adding);

$current_task = $app->getCurrentTask();
$title = $app->is_state(AppStates::$adding) ? 'Новая задача' : "Задача #$current_task";
$error = empty($app->getErrors()) ? null : $app->getErrors()[0];
$inputs = $app->getInputs();

try {
    $db = new MySQLConnector();
} catch (\Exception $e) {
	$db = null;
}

?>


<html lang="ru">
    <head>
        <title>Задача</title>
        <meta charset="UTF-8">
        <link href="../static/css/task-page-style.css" rel="stylesheet">
    </head>
    <body>
        <div class="main">
            <?php if (!is_null($db)): ?>

			<form method="post" action="../logic/task_page_logic.php">
				<h1>Мой календарь</h1>
				<fieldset class="task-page">
					<legend><?= $title ?></legend>
					<div class="inputs-box">
						<label>
							<span>Тема:</span>
							<input name="theme" type="text" maxlength="255" <?= set_old_value($inputs['theme'] ?? '') ?>>
						</label>
						<?= ErrorHandler::add_error_block('theme', $error) ?>

						<label>
							<span>Тип:</span>
							<select name="type">
								<?php foreach ($db->get_db_types() as $type): ?>

								<option value="<?= $type['label'] ?>" <?= select_if($inputs['type'] ?? '', $type['label']) ?>>
									<?= $type['name'] ?>
								</option>

								<?php endforeach ?>
							</select>
						</label>
                        <?= ErrorHandler::add_error_block('type', $error) ?>

						<label>
							<span>Место:</span>
							<input name="place" type="text" maxlength="255" <?= set_old_value($inputs['place'] ?? '') ?>>
						</label>
                        <?= ErrorHandler::add_error_block('place', $error) ?>

						<label>
							<span>Дата и время:</span>
							<div>
								<input type="date" name="date" min="1970-01-01" max="2038-12-31" <?= set_old_value($inputs['date'] ?? '') ?>>
								<input type="time" name="time" <?= set_old_value($inputs['time'] ?? '') ?>>
							</div>
						</label>
                        <?= ErrorHandler::add_error_block('date', $error) ?>
                        <?= ErrorHandler::add_error_block('time', $error) ?>

						<label>
							<span>Длительность:</span>
							<input type="time" name="duration" <?= set_old_value($inputs['duration'] ?? '') ?>>
						</label>
                        <?= ErrorHandler::add_error_block('duration', $error) ?>

						<label>
							<span>Комментарий:</span>
							<textarea name="comment" maxlength="1000"><?= $inputs['comment'] ?? '' ?></textarea>
						</label>
                        <?= ErrorHandler::add_error_block('comment', $error) ?>
					</div>
				</fieldset>
				<div class="buttons-box">
					<button class="button" name="to-main" value="yes">На главную</button>
					<button class="button" name="reset" value="yes">Очистить</button>
					<button class="button primary" name="save" value="yes">Сохранить</button>
				</div>
			</form>
            <?php else: ?>

				<p>
					Не удалось подключиться к БД. Проверьте развёрнутую БД или измените конфигурацию в классе
					src/app/MySQLConnector. Модель базы данных находится в static/sql/my_calendar.sql
				</p>

            <?php endif ?>
        </div>
    </body>
</html>