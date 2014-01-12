

Ink.requireModules(['Ink.Util.Url_1', 'Ink.Dom.Element_1'], function (Url, InkElement) {
    'use strict';
    
    var anUrl = 'http://expresso.sapo.pt:81/melhoresparatrabalhar?mid1=ex.menus/20&m2=429#123';
    var noPortUrl = 'http://expresso.sapo.pt/melhoresparatrabalhar?mid1=ex.menus/20&m2=429#123';

    module('parseUrl');
    test('parse a complex URL', function () {
        var parsed = Url.parseUrl(anUrl);
        equal(parsed.scheme, 'http');
        equal(parsed.host, 'expresso.sapo.pt:81');
        equal(parsed.path, '/melhoresparatrabalhar');
        equal(parsed.query, 'mid1=ex.menus/20&m2=429');
        equal(parsed.fragment, '123');
    });

    test('parse a complex URL, no port specified', function () {
        var parsed = Url.parseUrl(noPortUrl);
        equal(parsed.scheme, 'http');
        equal(parsed.host, 'expresso.sapo.pt');
        equal(parsed.path, '/melhoresparatrabalhar');
        equal(parsed.query, 'mid1=ex.menus/20&m2=429');
        equal(parsed.fragment, '123');
    });

    test('a simpler url', function () {
        var parsed = Url.parseUrl('http://someth.someth.com/');
        equal(parsed.scheme, 'http');
        equal(parsed.host, 'someth.someth.com');
        equal(parsed.path, '/');
        equal(parsed.query, false);
        equal(parsed.fragment, false);
    });

    module('format');

    test('format an object from parseUrl', function () {
        var parsed = Url.parseUrl(anUrl);
        equal(Url.format(parsed), anUrl);
    });

    test('format an object from parseUrl, missing query', function () {
        var url = 'http://exampleurls.sapo.pt/#hashhhh';
        var parsed = Url.parseUrl(url);
        equal(Url.format(parsed), url);
    });

    test('format an object from parseUrl, missing hash', function () {
        var url = 'http://exampleurls.sapo.pt/?search=asd';
        var parsed = Url.parseUrl(url);
        equal(Url.format(parsed), url);
    });

    test('format window.location objects', function () {
        equal(
            Url.format(window.location),
            Url.format({
                scheme: window.location.protocol.replace(':', ''),
                host: window.location.host,
                pathname: window.location.pathname,
                query: window.location.search.replace('?', ''),
                fragment: window.location.hash.replace('#', '')
            }));
    });
});
