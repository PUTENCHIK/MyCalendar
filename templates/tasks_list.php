<?php

namespace Pages\TasksList;

$path = $_SERVER['DOCUMENT_ROOT'];

use App\App;
use App\AppStates;
use App\MySQLConnector;
use App\CalendarFilters;
use function App\select_if;
use function App\set_old_value;

include_once $path.'/src/app/App.php';
include_once $path.'/src/app/AppStates.php';
include_once $path.'/src/app/CalendarFilters.php';

include_once $path.'/src/functions.php';

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['app'])) {
    $_SESSION['app'] = new App();
}

$app = $_SESSION['app'];
$app->check_state(AppStates::$list);
$interval = $app->getDateInterval();

try {
    $db = new MySQLConnector();
    $all_tasks = $db->get_tasks();
    $tasks = $db->get_tasks($app->getFilter(), $interval['start'], $interval['end']);
    $types = $db->get_db_types();
} catch (\Exception $e) {
    $db = null;
}

?>


<html lang="ru">
    <head>
        <title>Список задач</title>
        <meta charset="UTF-8">
        <link href="../static/css/task-list-style.css" rel="stylesheet">
    </head>
    <body>
        <div class="main">
			<?php if (!is_null($db)): ?>

			<form method="post" action="../logic/tasks_list_logic.php" name="main-form">
				<h1>Мой календарь</h1>
				<?php if (!empty($all_tasks)): ?>

				<fieldset class="task-list">
					<legend>Список задач</legend>
					<div class="settings">
						<div class="task-filter">
							<select class="type" name="task-filter">
								<?php foreach (CalendarFilters::get_filters() as $filter): ?>

									<option value="<?= $filter['value'] ?>" <?= select_if(CalendarFilters::get_filter(id: $app->getFilter()), $filter['value']) ?>>
										<?= $filter['label'] ?>
									</option>

								<?php endforeach ?>
							</select>
						</div>
						<?php if ($app->is_filter(CalendarFilters::$other)): ?>

							<div class="extra-settings">
								<label class="label-date">
									<input
										type="date"
										name="certain-date"
										min="1970-01-01"
										max="2038-12-31"
										<?= set_old_value(explode(' ', $app->getDateInterval()['start'])[0]) ?>
									>
									<img class="icon calendar" src="../static/icons/calendar.png" alt="date">
								</label>
								<div class="date-filters">
									<div class="date-filter">
										<button class="link" name="date-filter" value="today">сегодня</button>
									</div>
									<div class="date-filter">
										<button class="link" name="date-filter" value="tomorrow">завтра</button>
									</div>
									<div class="date-filter">
										<button class="link" name="date-filter" value="this-week">на эту неделю</button>
									</div>
									<div class="date-filter">
										<button class="link" name="date-filter" value="next-week">на след. неделю</button>
									</div>
								</div>
							</div>

						<?php endif ?>
					</div>

					<div class="tasks">
						<?php if (!empty($tasks)): ?>
							<div class="tasks-row">
								<div>Тип</div>
								<div>Задача</div>
								<div>Место</div>
								<div>Дата и время</div>
							</div>
							<?php foreach ($tasks as $task): ?>
								<div class="tasks-row">
									<div><?= $types[$task['type_id']-1]['name'] ?></div>
									<div><?= $task['theme'] ?></div>
									<div><?= $task['place'] ?></div>
									<div><?= $task['datetime'] ?></div>
									<div class="cell-button">
										<button class="checkbox <?= $task['is_completed'] ? 'checked' : '' ?>" name="is-completed" value="<?= $task['id'] ?>">
											<img class="icon" src="../static/icons/tick.png" alt="1">
										</button>
									</div>
									<div class="cell-button">
										<button name="change" value="<?= $task['id'] ?>">
											<img class="icon pencil" src="../static/icons/pencil.png" alt="pencil">
										</button>
									</div>
								</div>
							<?php endforeach ?>
						<?php else: ?>
							<p class="text">В этой категории нет ни одного задания</p>
						<?php endif ?>
					</div>
				</fieldset>

				<?php else: ?>
				<p>Вы пока что не добавили не одной задачи.</p>
				<?php endif ?>

				<div class="buttons-box">
					<button class="button primary" name="add-task" value="yes">Добавить задачу</button>
				</div>
			</form>
			<?php else: ?>

			<p>
				Не удалось подключиться к БД. Проверьте развёрнутую БД или измените конфигурацию в классе
				src/app/MySQLConnector. Модель базы данных находится в static/sql/my_calendar.sql
			</p>

			<?php endif ?>
		</div>
		<script src="../static/js/calendar-button.js"></script>
		<script src="../static/js/submit-from.js"></script>
    </body>
</html>