document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('nps-form');
    if (!form) {
        return;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form)
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            form.innerHTML = data.message;
        });
    });
});
