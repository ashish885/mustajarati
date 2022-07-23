// @ts-nocheck
const getDoNotTrack = () => {
    if (navigator.doNotTrack) {
        return navigator.doNotTrack;
    }
    if (navigator.msDoNotTrack) {
        return navigator.msDoNotTrack;
    }
    if (window.doNotTrack) {
        return window.doNotTrack;
    }
    return null;
}

const getNetworkInfo = () => {
    var info = navigator.connection;
    if (info !== undefined) {
        return {
            downlink: info.downlink || "unknown",
            downlinkMax: info.downlinkMax || info.downlink || "unknown",
            effectiveType: info.effectiveType || "unknown",
            rtt: info.rtt || "unknown",
            type: info.type || "unknown"
        };
    }
    return {};
}

const getHasLiedLanguages = () => {
    // We check if navigator.language is equal to the first language of navigator.languages
    if (typeof navigator.languages !== 'undefined') {
        try {
            var firstLanguages = navigator.languages[0].substr(0, 2);
            if (firstLanguages !== navigator.language.substr(0, 2)) {
                return true;
            }
        } catch (err) {
            return true;
        }
    }
    return false;
}

const getHasLiedResolution = () => {
    return window.screen.width < window.screen.availWidth || window.screen.height < window.screen.availHeight;
}

const getHasLiedOs = () => {
    var userAgent = navigator.userAgent.toLowerCase();
    var oscpu = navigator.oscpu;
    var platform = navigator.platform.toLowerCase();
    var os; // We extract the OS from the user agent (respect the order of the if else if statement)
    if (userAgent.indexOf('windows phone') >= 0) {
        os = 'Windows Phone';
    } else if (userAgent.indexOf('win') >= 0) {
        os = 'Windows';
    } else if (userAgent.indexOf('android') >= 0) {
        os = 'Android';
    } else if (userAgent.indexOf('linux') >= 0 || userAgent.indexOf('cros') >= 0) {
        os = 'Linux';
    } else if (userAgent.indexOf('iphone') >= 0 || userAgent.indexOf('ipad') >= 0) {
        os = 'iOS';
    } else if (userAgent.indexOf('mac') >= 0) {
        os = 'Mac';
    } else {
        os = 'Other';
    } // We detect if the person uses a mobile device
    var mobileDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0 || navigator.msMaxTouchPoints > 0;
    if (mobileDevice && os !== 'Windows Phone' && os !== 'Android' && os !== 'iOS' && os !== 'Other') {
        return true;
    } // We compare oscpu with the OS extracted from the UA
    if (typeof oscpu !== 'undefined') {
        oscpu = oscpu.toLowerCase();
        if (oscpu.indexOf('win') >= 0 && os !== 'Windows' && os !== 'Windows Phone') {
            return true;
        } else if (oscpu.indexOf('linux') >= 0 && os !== 'Linux' && os !== 'Android') {
            return true;
        } else if (oscpu.indexOf('mac') >= 0 && os !== 'Mac' && os !== 'iOS') {
            return true;
        } else if ((oscpu.indexOf('win') === -1 && oscpu.indexOf('linux') === -1 && oscpu.indexOf('mac') === -1) !== (os === 'Other')) {
            return true;
        }
    } // We compare platform with the OS extracted from the UA
    if (platform.indexOf('win') >= 0 && os !== 'Windows' && os !== 'Windows Phone') {
        return true;
    } else if ((platform.indexOf('linux') >= 0 || platform.indexOf('android') >= 0 || platform.indexOf('pike') >= 0) && os !== 'Linux' && os !== 'Android') {
        return true;
    } else if ((platform.indexOf('mac') >= 0 || platform.indexOf('ipad') >= 0 || platform.indexOf('ipod') >= 0 || platform.indexOf('iphone') >= 0) && os !== 'Mac' && os !== 'iOS') {
        return true;
    } else if ((platform.indexOf('win') === -1 && platform.indexOf('linux') === -1 && platform.indexOf('mac') === -1) !== (os === 'Other')) {
        return true;
    }
    return typeof navigator.plugins === 'undefined' && os !== 'Windows' && os !== 'Windows Phone';
}

const getHasLiedBrowser = () => {
    var userAgent = navigator.userAgent.toLowerCase();
    var productSub = navigator.productSub; // we extract the browser from the user agent (respect the order of the tests)
    var browser;
    if (userAgent.indexOf('firefox') >= 0) {
        browser = 'Firefox';
    } else if (userAgent.indexOf('opera') >= 0 || userAgent.indexOf('opr') >= 0) {
        browser = 'Opera';
    } else if (userAgent.indexOf('chrome') >= 0) {
        browser = 'Chrome';
    } else if (userAgent.indexOf('safari') >= 0) {
        browser = 'Safari';
    } else if (userAgent.indexOf('trident') >= 0) {
        browser = 'Internet Explorer';
    } else {
        browser = 'Other';
    }
    if ((browser === 'Chrome' || browser === 'Safari' || browser === 'Opera') && productSub !== '20030107') {
        return true;
    } // eslint-disable-next-line no-eval
    var tempRes = eval.toString().length;
    if (tempRes === 37 && browser !== 'Safari' && browser !== 'Firefox' && browser !== 'Other') {
        return true;
    } else if (tempRes === 39 && browser !== 'Internet Explorer' && browser !== 'Other') {
        return true;
    } else if (tempRes === 33 && browser !== 'Chrome' && browser !== 'Opera' && browser !== 'Other') {
        return true;
    } // We create an error to see how it is handled
    var errFirefox;
    try {
        // eslint-disable-next-line no-throw-literal
        throw 'a';
    } catch (err) {
        try {
            err.toSource();
            errFirefox = true;
        } catch (errOfErr) {
            errFirefox = false;
        }
    }
    return errFirefox && browser !== 'Firefox' && browser !== 'Other';
}

const getTouchSupport = () => {
    var maxTouchPoints = 0;
    var touchEvent;
    if (typeof navigator.maxTouchPoints !== 'undefined') {
        maxTouchPoints = navigator.maxTouchPoints;
    } else if (typeof navigator.msMaxTouchPoints !== 'undefined') {
        maxTouchPoints = navigator.msMaxTouchPoints;
    }
    try {
        document.createEvent('TouchEvent');
        touchEvent = true;
    } catch (_) {
        touchEvent = false;
    }
    var touchStart = 'ontouchstart' in window;
    return {
        maxTouchPoints: maxTouchPoints,
        canCreateTouchEvent: touchEvent,
        touchStarted: touchStart
    };
}

const getIPs = () => {
    return new Promise((resolve, reject) => {
        var logInfo = false;
        window.RTCPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
        if (typeof window.RTCPeerConnection == 'undefined')
            return resolve(null);
        var pc = new RTCPeerConnection();
        var ips = [];
        pc.createDataChannel("");
        pc.createOffer()
            .then(offer => pc.setLocalDescription(offer))
            .catch(err => resolve(null));
        pc.onicecandidate = event => {
            if (!event || !event.candidate) {
                // All ICE candidates have been sent.
                if (ips.length == 0)
                    return resolve(null);
                return resolve((ips.length > 0 ? ips[0] : ips));
            }

            var parts = event.candidate.candidate.split(' ');
            var [base, componentId, protocol, priority, ip, port, , type, ...attr] = parts;
            var component = ['rtp', 'rtpc'];

            if (!ips.some(e => e == ip))
                ips.push(ip);
            if (!logInfo)
                return;

            console.log(" candidate: " + base.split(':')[1]);
            console.log(" component: " + component[componentId - 1]);
            console.log("  protocol: " + protocol);
            console.log("  priority: " + priority);
            console.log("        ip: " + ip);
            console.log("      port: " + port);
            console.log("      type: " + type);

            if (attr.length) {
                console.log("attributes: ");
                for (var i = 0; i < attr.length; i += 2)
                    console.log("> " + attr[i] + ": " + attr[i + 1]);
            }

            console.log();
        };
    });
}

const getVisitorInfo = async function () {
    var colorDepth = window.screen.colorDepth;
    var pixelRatio = window.devicePixelRatio;
    var screenResolution = `${window.screen.width}x${window.screen.height}`;
    var availableScreenResolution = `${window.screen.width}x${window.screen.height}`;
    var timeZoneOffset = new Date().getTimezoneOffset();
    var timeZone = (window.Intl && window.Intl.DateTimeFormat) ? new window.Intl.DateTimeFormat().resolvedOptions().timeZone : null;
    var sessionStorage = !!window.sessionStorage;
    var localStorage = !!window.localStorage;
    var indexedDb = !!window.indexedDB;
    var addBehavior = !!(document.body && document.body.addBehavior);
    var openDatabase = !!window.openDatabase;
    var localIp = await getIPs();
    var domain = window.location.host;
    return { 'localIp': localIp, 'domain': domain, 'addBehavior': addBehavior, 'availableScreenResolution': availableScreenResolution, 'colorDepth': colorDepth, 'cpuClass': navigator.cpuClass, 'deviceMemory': navigator.deviceMemory, 'doNotTrack': navigator.doNotTrack, 'hardwareConcurrency': navigator.hardwareConcurrency, 'indexedDb': indexedDb, 'language': navigator.language, 'liedBrowser': navigator.liedBrowser, 'liedLanguages': navigator.liedLanguages, 'liedOs': navigator.liedOs, 'liedResolution': navigator.liedResolution, 'localStorage': localStorage, 'navigator': navigator.navigator, 'appCodeName': navigator.appCodeName, 'appName': navigator.appName, 'appVersion': navigator.appVersion, 'buildID': navigator.buildID, 'clipboard': navigator.clipboard, 'cookieEnabled': navigator.cookieEnabled, 'credentials': navigator.credentials, 'doNotTrack': navigator.doNotTrack, 'geolocation': navigator.geolocation, 'hardwareConcurrency': navigator.hardwareConcurrency, 'language': navigator.language, 'languages': navigator.languages, 'maxTouchPoints': navigator.maxTouchPoints, 'mediaCapabilities': navigator.mediaCapabilities, 'mediaDevices': navigator.mediaDevices, 'mimeTypes': navigator.mimeTypes, 'onLine': navigator.onLine, 'oscpu': navigator.oscpu, 'permissions': navigator.permissions, 'platform': navigator.platform, 'plugins': navigator.plugins, 'product': navigator.product, 'productSub': navigator.productSub, 'serviceWorker': navigator.serviceWorker, 'storage': navigator.storage, 'userAgent': navigator.userAgent, 'vendor': navigator.vendor, 'vendorSub': navigator.vendorSub, 'webdriver': navigator.webdriver, 'network': navigator.network, 'openDatabase': openDatabase, 'pixelRatio': pixelRatio, 'platform': navigator.platform, 'screenResolution': screenResolution, 'sessionStorage': sessionStorage, 'timeZone': timeZone, 'timeZoneOffset': timeZoneOffset, 'touchSupport': navigator.touchSupport, 'webDriver': navigator.webDriver, 'doNotTrack': getDoNotTrack(), 'network': getNetworkInfo(), 'liedLanguages': getHasLiedLanguages(), 'liedResolution': getHasLiedResolution(), 'liedOs': getHasLiedOs(), 'liedBrowser': getHasLiedBrowser(), 'touchSupport': getTouchSupport() };
}