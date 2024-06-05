window.addEventListener("DOMContentLoaded", () => {

    const filter_select = document.getElementsByName("task-filter")[0];
    const main_form = document.getElementsByName("main-form")[0];

    if (filter_select !== undefined) {
        filter_select.addEventListener("change", () => {
            main_form.submit();
        });
    }

    const tasks_table = document.getElementsByClassName("tasks")[0];

    if (tasks_table !== undefined) {
        tasks_table.addEventListener("click", (event) => {
            let checkbox = event.target;
            if (checkbox.className === "is-task-completed") {
                main_form.submit();
            }
        });
    }

    const certain_date = document.getElementsByName("certain-date")[0];

    if (certain_date !== undefined) {
        certain_date.addEventListener("change", () => {
            const from = new Date(1970, 1, 1);
            const to = new Date(2038, 12, 31);
            let date = new Date(certain_date.value);

            if (date > from && date < to) {
                main_form.submit();
            }
        });
    }
});