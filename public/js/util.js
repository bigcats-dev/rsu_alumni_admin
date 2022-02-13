function loadScript(script, callback) {
    // Usage:  Load different file types with one callback
    Promise.all(script).then(function () {
        callback();
    }).catch(function () {
        console.log('Oh no, epic failure!');
    });
}

addScript = (() => {
    function _load(tag) {
        return function (url) {
            // This promise will be used by Promise.all to determine success or failure
            return new Promise(function (resolve, reject) {
                var element = document.createElement(tag);
                var parent = 'body';
                var attr = 'src';

                // Important success and error for the promise
                element.onload = function () {
                    resolve(url);
                };
                element.onerror = function () {
                    reject(url);
                };

                // Need to set different attributes depending on tag type
                switch (tag) {
                    case 'script':
                        element.async = true;
                        break;
                    case 'link':
                        element.type = 'text/css';
                        element.rel = 'stylesheet';
                        attr = 'href';
                        parent = 'head';
                        break;
                    default: break;
                }

                // Inject into document to kick off loading
                element[attr] = url;
                document[parent].appendChild(element);
            });
        };
    }

    return {
        css: _load('link'),
        js: _load('script')
    }
})()

function niceBytes(x) {
    const units = ['bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    let l = 0, n = parseInt(x, 10) || 0;
    while (n >= 1024 && ++l) {
        n = n / 1024;
    }
    return (n.toFixed(n < 10 && l > 0 ? 1 : 0) + ' ' + units[l]);
}

function previewImages(files, content) {
    content.empty()
    if (files) {
        [].forEach.call(files, readAndPreview);
    }

    function readAndPreview(file) {

        if (!/\.(jpe?g|png|gif)$/i.test(file.name)) {
            return
        }

        var reader = new FileReader();

        reader.addEventListener("load", function () {
            content.append(
                $('<li/>')
                    .append(
                        $('<img/>')
                            .attr('src', this.result))
                    .append(
                        $('<span/>')
                            .addClass('text-danger')
                            .html(file.name)))
        });

        reader.readAsDataURL(file);
    }

}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms))
}

function string(str, target = '-') {
    return !str || str.length === 0 ? target : str
}

function toNumber(number) {
    return number ? number.replace(/,/g, '') : 0
}

function convertToDateEn(date) {
    var arr = date.split('/');
    return arr[0] + '/' + arr[1] + '/' + (parseInt(arr[2]) - 543);
}

function convertToDateYearTh(date) {
    var arr = date.split('/');
    return arr[0] + '/' + arr[1] + '/' + (parseInt(arr[2]) + 543);
}

const browserName = (function (agent) {
    switch (true) {
        case agent.indexOf("edge") > -1: return "MS Edge";
        case agent.indexOf("edg/") > -1: return "Edge ( chromium based)";
        case agent.indexOf("opr") > -1 && !!window.opr: return "Opera";
        case agent.indexOf("chrome") > -1 && !!window.chrome: return "Chrome";
        case agent.indexOf("trident") > -1: return "MS IE";
        case agent.indexOf("firefox") > -1: return "Mozilla Firefox";
        case agent.indexOf("safari") > -1: return "Safari";
        default: return "other";
    }
})(window.navigator.userAgent.toLowerCase());
