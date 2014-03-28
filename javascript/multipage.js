/*jshint browser:true, node:false, laxcomma:true */
/*global Ink:false */

Ink.requireModules(
    ['Inkx_Autocomplete', 'Ink.Net.Ajax_1', 'Ink.Dom.Event_1', 'Ink.Dom.Selector_1'],
    function(Autocomplete, Ajax, Event/*, Selector*/) {

        var err = function() {
            console.log('error', arguments);
        };



        var fetchToElement = function(uri, selDestination, cb) {
            new Ajax(uri, {
                method: 'GET',
                onSuccess: function(tmp, data) {
                    var destEl = Ink.s(selDestination);
                    destEl.innerHTML = data;
                    if (cb) {
                        cb(null);
                    }
                },
                onException: function() { if (cb) { cb('exception'); } },
                onTimeout:   function() { if (cb) { cb('timeout');   } },
                onFailure:   function() { if (cb) { cb('failure');   } }
            });
        };



        fetchToElement('modules.html', '.left-part');

        var onHashProcessed = function(err) {
            if (err) { return console.log(err); }
            Ink.s('.main-part').scrollTop = 0;
            //var anchorEl = Ink.s('a[name="' + this + '"]');
            //console.log('anchorEl', anchorEl);
            location.hash = '#' + this;
            //console.log('focusing ' + this + '!\n');
        };

        var processHash = function(hash, ev) {
            if (Ink.s('a[name="' + hash + '"]')) {
                return;//console.log('found anchor ' + hash + ' locally!\n');
            }

            if (ev) {
                Event.stop(ev);
            }

            var parts = hash.split('-');
            var cb = Ink.bind(onHashProcessed, hash);
            if (parts.length > 1) {
                location.hash = '#';
                fetchToElement(parts[0] + '.html', '.main-part', cb);
                return;//console.log('composed hash, fetching ' + parts[0] + ' via AJAX...');
            }
            fetchToElement(hash + '.html', '.main-part', cb);
            //console.log('module hash, fetching ' + hash + '!\n');
        };

        var onHashChange = function(ev) {
            var hash = location.hash;
            if (!hash || hash.length < 2) { return; }
            hash = hash.substring(1);
            processHash(hash, ev);
        };

        Event.observe(window, 'hashchange', onHashChange);

        onHashChange();



        var ac, allowedKinds = 'mcf';

        var filterMEl = Ink.s('#filter-m');
        var filterCEl = Ink.s('#filter-c');
        var filterFEl = Ink.s('#filter-f');

        var onFilterChange = function() {
            allowedKinds = [
                filterMEl.checked ? 'm' : '',
                filterCEl.checked ? 'c' : '',
                filterFEl.checked ? 'f' : ''
            ].join('');
            ac.test();
        };
        Event.observe(filterMEl, 'change', onFilterChange);
        Event.observe(filterCEl, 'change', onFilterChange);
        Event.observe(filterFEl, 'change', onFilterChange);

        new Ajax('identifiers.json', {
            method: 'GET',
            evalJS: 'force',
            onSuccess: function(tmp, model) { // item structure: 0: text compare, 1: m/c/f, 2: real name, 3: file, 4: hash, 5: ancestors
                ac = new Autocomplete('.autocomplete', {
                     model:      model
                    ,maxResults: 12
                    ,isMatch: function(text, item) {
                        return allowedKinds.indexOf(item[1]) !== -1 && item[0].indexOf(text) !== -1;
                    }
                    ,itemRenderer: function(text, item) {
                        var l = text.length;
                        var i = item[0].lastIndexOf(text);
                        var match = item[2].split('');
                        match.splice(i+l, 0, '</b>');
                        match.splice(i,   0, '<b>');
                        match = match.join('');
                        return ['<li class="', item[1], '"><a href="#', item[4], '"><span>', item[1].toUpperCase(), '</span>', item[5] ? item[5] + ' ' : '', match, '</a></li>\n'].join('');
                    }
                });
                //ac._el.focus();
            },
            onException: err,
            onTimeout:   err,
            onFailure:   err
        });
    }
);
