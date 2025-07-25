function filterProducts(params) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/products/filter?' + new URLSearchParams(params).toString(), true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById('productsList').innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}