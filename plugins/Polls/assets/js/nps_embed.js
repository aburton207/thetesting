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
            var message = document.createElement('div');
            message.id = 'nps-response-message';
            message.className = 'nps-response-message';
            message.innerHTML = data.message;
            form.parentNode.replaceChild(message, form);
        });
    });
});
