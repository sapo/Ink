QUnit.config.testTimeout = 4000;

Ink.requireModules(['Ink.Dom.Browser_1'], function (Browser) {
    function checkIs(string, browser, version) {
        Browser._sniffUserAgent(string);
        strictEqual(Browser[browser], true, 'Browser.' + browser + ' should be true');
        strictEqual(Browser.version, version, 'Browser version match');
    }

    test('sniffing some known agent strings', function () {
        checkIs('Mozilla/4.0 (compatible; MSIE 5.0; Windows NT;)', 'IE', '5.0');
        checkIs('Mozilla/4.0 (compatible; MSIE 5.0b1; Mac_PowerPC)', 'IE', '5.0b1');
        checkIs('Mozilla/4.0 (Windows; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)', 'IE', '6.0');
        checkIs('Mozilla/4.0 (Mozilla/4.0; MSIE 7.0; Windows NT 5.1; FDM; SV1; .NET CLR 3.0.04506.30)', 'IE', '7.0');
        checkIs('Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 1.1.4322; InfoPath.2; .NET CLR 3.5.21022; .NET CLR 3.5.30729; MS-RTC LM 8; OfficeLiveConnector.1.4; OfficeLivePatch.1.3; .NET CLR 3.0.30729)', 'IE', '8.0');
        checkIs('Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)', 'IE', '9.0');
        checkIs('Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; Media Center PC 6.0; InfoPath.3; MS-RTC LM 8; Zune 4.7)', 'IE', '9.0');
        checkIs('Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)', 'IE', '10.0');
        checkIs('Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)', 'IE', '10.0');
        checkIs('Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Win64; x64; Trident/6.0)', 'IE', '10.0');
        checkIs('Mozilla/5.0 (compatible; MSIE 10.0; Windows Phone 8.0; Trident/6.0; IEMobile/10.0; ARM; Touch; NOKIA; Lumia 920)', 'IE', '10.0');

        // Most used user agent strings
        checkIs('Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)', 'IE', '8.0');
        checkIs('Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36', 'CHROME', '31.0.1650.57');
        checkIs('Mozilla/5.0 (Windows NT 6.3; WOW64; rv:25.0) Gecko/20100101 Firefox/25.0', 'GECKO', '25.0');
        checkIs('Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; WOW64; Trident/6.0)', 'IE', '10.0');
        checkIs('Mozilla/5.0 (iPad; CPU OS 7_0_4 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11B554a Safari/9537.53', 'SAFARI', '7.0');
        checkIs('Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko', 'IE', '11.0');
        checkIs('Mozilla/5.0 (Windows NT 6.2; WOW64; rv:25.0) Gecko/20100101 Firefox/25.0', 'GECKO', '25.0');
        checkIs('Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko', 'IE', '11.0');
        checkIs('Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)', 'IE', '9.0');
        checkIs('Mozilla/5.0 (Windows NT 6.0; rv:25.0) Gecko/20100101 Firefox/25.0', 'GECKO', '25.0');
        checkIs('Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)', 'IE', '9.0');
        checkIs('Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0)', 'IE', '9.0');
        checkIs('Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36', 'CHROME', '31.0.1650.57');
        checkIs('Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)', 'IE', '10.0');
        checkIs('Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; WOW64; Trident/6.0)', 'IE', '10.0');
        checkIs('Mozilla/5.0 (Windows NT 6.1; rv:25.0) Gecko/20100101 Firefox/25.0', 'GECKO', '25.0');
        checkIs('Mozilla/5.0 (Windows NT 6.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36', 'CHROME', '31.0.1650.57');
        checkIs('Mozilla/5.0 (Windows NT 5.1; rv:25.0) Gecko/20100101 Firefox/25.0', 'GECKO', '25.0');
        checkIs('Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)', 'IE', '10.0');
        checkIs('Mozilla/5.0 (Windows NT 6.1; WOW64; rv:25.0) Gecko/20100101 Firefox/25.0', 'GECKO', '25.0');
        checkIs('Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0', 'CHROME', '31.0');
        checkIs('Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36', 'CHROME', '31.0.1650.57');

        // Some safaris. Source: http://www.useragentstring.com/pages/Safari/
        checkIs('Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25', 'SAFARI', '6.0');
        checkIs('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2', 'SAFARI', '5.1.7');
    });

    test('IE11, which removed the "MSIE" string', function () {
        checkIs('Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; .NET4.0E; .NET4.0C; Media Center PC 6.0; rv:11.0) like Gecko', 'IE', '11.0');
        checkIs('Mozilla/5.0 (IE 11.0; Windows NT 6.3; Trident/7.0; .NET4.0E; .NET4.0C; rv:11.0) like Gecko', 'IE', '11.0');
    });

    test('regression: IE11 mobile, who pretends to be safari', function() {
        checkIs('Mozilla/5.0 (Mobile; Windows Phone 8.1; Android 4.0; ARM; Trident/7.0; Touch; rv:11.0; IEMobile/11.0; NOKIA; Lumia 1520) like iPhone OS 7_0_3 Mac OS X AppleWebKit/537 (KHTML, like Gecko) Mobile Safari/537', 'IE', '11.0')
    })
});

