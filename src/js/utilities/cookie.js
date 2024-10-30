export function updateCookie(key, value) {
    document.cookie = encodeURIComponent(key) + '=' + encodeURIComponent(value) + '; path=/';
}

export function getCookies() {
    let cookies = [];
    let cookies_data = document.cookie.split('; ');

    for (let iterator in cookies_data) {
        let data = cookies_data[iterator].split('=');

        cookies[data[0]] = data[1];
    }

    return cookies;
}

export function deleteCookie(key) {
    setCookie(key, "", 0)
}

export function setCookie(c_name, value, exdays = null, urlEncode = false) {
    let exdate = new Date;
    exdate.setDate(exdate.getDate() + exdays);
    const c_value = urlEncode ? encodeURIComponent(value) : value;
    const domain = window.location.host;
    document.cookie = c_name + "=" + c_value + (null === exdays ? "" : "; expires=" + exdate.toUTCString()) + ";domain=" + domain + ";path=/";
}
