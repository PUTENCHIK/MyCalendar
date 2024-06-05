window.addEventListener("DOMContentLoaded", () => {

    let label = document.getElementsByClassName("calendar")[0];
    let input = document.getElementsByName("certain-date")[0];

    if (label !== undefined) {
        label.addEventListener("click", (e) => {
            input.showPicker();
        });
    }

});