<html lang="ru">
    <head>
        <title>Список задач</title>
        <meta charset="UTF-8">
        <link href="../static/css/task-list-style.css" rel="stylesheet">
    </head>
    <body>
        <div class="main">
            <h1>Мой календарь</h1>
            <fieldset>
                <legend>Список задач</legend>
                <div class="settings">
                    <div class="task-filter">
                        <form method="post" action="" id="task-filter">
                            <select>
                                <option>Текущие задачи</option>
                                <option>Просроченные задачи</option>
                                <option>Выполненные задачи</option>
                                <option>Задачи на конкретные даты</option>
                            </select>
                        </form>
                    </div>
                    <div class="extra-settings">
                        <form method="post" action="" id="extra-settings">
                            <label class="label-date">
                                <input type="date" name="certain-date">
                                <img src="../static/icons/calendar.png" alt="date">
                            </label>
                            <div>

                            </div>
                        </form>
                    </div>
                </div>
            </fieldset>
        </div>
    </body>
</html>