document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('nps-form');
    if (!form) {
        return;
    }

    form.addEventListener('change', function (e) {
        if (e.target && e.target.name && e.target.name.indexOf('score') === 0) {
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                form.innerHTML = data.message;
            });
        }
    });
});
