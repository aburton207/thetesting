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
            // clear form fields on success
            if (data.success && typeof form.reset === 'function') {
                form.reset();
            }

            // remove any existing message
            var existingMessage = document.getElementById('nps-response-message');
            if (existingMessage) {
                existingMessage.remove();
            }

            // append new message below the form
            var message = document.createElement('div');
            message.id = 'nps-response-message';
            message.innerHTML = data.message;
            form.parentNode.insertBefore(message, form.nextSibling);
        });
    });
});
