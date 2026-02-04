/**
 * Reusable Salon API helper: CSRF-aware fetch for POST, PUT, DELETE.
 * Assumes meta tag: <meta name="csrf-token" content="...">
 */
(function (global) {
    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function getJsonHeaders() {
        var headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
        var token = getCsrfToken();
        if (token) headers['X-CSRF-TOKEN'] = token;
        return headers;
    }

    function handleResponse(res) {
        return res.text().then(function (text) {
            var body;
            var toParse = text ? text.trim() : '';
            if (toParse && toParse.indexOf('{') !== -1) {
                toParse = toParse.substring(toParse.indexOf('{'));
            }
            try {
                body = toParse ? JSON.parse(toParse) : null;
            } catch (e) {
                if (!res.ok) {
                    var plain = text ? text.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim() : '';
                    var err = new Error(plain && plain.length < 200 ? plain : 'Request failed');
                    err.status = res.status;
                    err.body = text;
                    throw err;
                }
                var err = new Error('Server returned invalid response. Check the console or try again.');
                err.status = res.status;
                err.body = text;
                throw err;
            }
            if (!res.ok) {
                var msg = (body && body.message) ? body.message : (typeof body === 'string' ? body : 'Request failed');
                var err = new Error(msg);
                err.status = res.status;
                err.body = body;
                throw err;
            }
            return body;
        });
    }

    function apiPost(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: getJsonHeaders(),
            body: JSON.stringify(data),
            credentials: 'same-origin'
        }).then(handleResponse);
    }

    function apiPut(url, data) {
        return fetch(url, {
            method: 'PUT',
            headers: getJsonHeaders(),
            body: JSON.stringify(data),
            credentials: 'same-origin'
        }).then(handleResponse);
    }

    function apiDelete(url) {
        return fetch(url, {
            method: 'DELETE',
            headers: getJsonHeaders(),
            credentials: 'same-origin'
        }).then(handleResponse);
    }

    function getFormDataHeaders() {
        var headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
        var token = getCsrfToken();
        if (token) headers['X-CSRF-TOKEN'] = token;
        return headers;
    }

    function apiPostFormData(url, formData) {
        return fetch(url, {
            method: 'POST',
            headers: getFormDataHeaders(),
            body: formData,
            credentials: 'same-origin'
        }).then(handleResponse);
    }

    function apiPutFormData(url, formData) {
        return fetch(url, {
            method: 'POST',
            headers: getFormDataHeaders(),
            body: formData,
            credentials: 'same-origin'
        }).then(handleResponse);
    }

    global.salonApi = {
        getCsrfToken: getCsrfToken,
        post: apiPost,
        put: apiPut,
        delete: apiDelete,
        postFormData: apiPostFormData,
        putFormData: apiPutFormData
    };
})(typeof window !== 'undefined' ? window : this);
