document.getElementById('loginForm').onsubmit = function(e) {
    e.preventDefault();
    var form = this;
    var data = new FormData(form);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/auth/login', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            var resp = JSON.parse(xhr.responseText);
            if (resp.success) {
                window.location.href = '/';
            } else {
                var err = document.getElementById('loginError');
                err.style.display = 'block';
                err.innerText = resp.error || 'Ошибка входа';
            }
        }
    };
    xhr.send(data);
};