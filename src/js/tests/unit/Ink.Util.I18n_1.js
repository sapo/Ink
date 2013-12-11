/*globals equal,test*/
Ink.requireModules( [ 'Ink.Util.I18n' ] , function ( I18n ) {
    'use strict';

    var _test = window.test,
        test = function () {
            I18n.reset();
            return _test.apply(this, [].slice.call(arguments));
        };

    function make() {
        return new I18n(dict, 'pt_PT');
    }

    var dict = {'pt_PT': {
        'me': 'eu',
        'i have a {} for you': 'tenho um {} para ti',
        '1:, {1}, 2: {2}': '2: {2}, 1: {1}',
        'day': ['um dia', '{} dias'],
        'one day' : 'um dia' ,
        '{} days' : '{} dias'
    }};

    test('basic usage', function () {
        var i18n = make();
        equal(i18n.text('me'), 'eu');
    });

    test('alias', function () {
        var i18n = make();
        var aliased = i18n.alias();
        equal(aliased('me'), 'eu');
    });

    test('lang()', function () {
        var i18n = make();
        equal(i18n.lang(), 'pt_PT');
        equal(i18n.lang('en_US'), i18n);
        equal(i18n.lang(), 'en_US');
    });

    test('append', function () {
        var i18n = make();
        i18n.append({'pt_PT': {
            'sfraggles': 'braggles'
        }});
        equal(i18n.text('sfraggles'), 'braggles');
    });

    test('testMode', function() {
        var i18n = make();
        var _ = i18n.alias();
        i18n.testMode(true);
        equal(i18n.testMode(), true);
        equal(_('unknown'), '[unknown]');
        equal(_('me'), 'eu');

        i18n.testMode(false);
        equal(i18n.testMode(), false);
        equal(_('unknown'), 'unknown');
        equal(_('me'), 'eu');
    });

    test('replacements', function () {
        var _ = make().alias();
        equal(_('i have a {} for you', 'presente'), 'tenho um presente para ti');
        equal(_('1:, {1}, 2: {2}', 1, 2), '2: 2, 1: 1');
    });

    test('ntext()', function() {
        var i18n = make();

        equal(i18n.ntext('animal', 'animals', 1),
            'animal');

        equal(i18n.ntext('animal', 'animals', 2),
            'animals');

        equal(i18n.ntext('day', 1), 'um dia');
        equal(i18n.ntext('day', 2), '2 dias');
        
        // Classic API
        equal(i18n.ntext('one day', '{} days', 1), 'um dia');
        equal(i18n.ntext('one day', '{} days', 2), '2 dias');

    });

    test('ordinal', function () {
        var dict = {
            pt_PT : {
                _ordinals: '&ordm;'
            },
            fr_FR: {
                _ordinals: {
                    'default': '<sup>e</sup>',
                    exceptions: {
                        1: '<sup>er</sup>'
                    }
                }
            },
            en_US: {
                _ordinals: {
                    'default': 'th',
                    byLastDigit: {
                        1: 'st',
                        2: 'nd',
                        3: 'rd'
                    },
                    exceptions: {
                        0: '',
                        11: 'th',
                        12: 'th',
                        13: 'th'
                    }
                }
            }
        };

        var i18n = new I18n(dict, 'fr_FR');
        equal(i18n.ordinal(1), '<sup>er</sup>');
        equal(i18n.ordinal(2), '<sup>e</sup>');
        equal(i18n.ordinal(11), '<sup>e</sup>');

        equal(i18n.lang( 'en_US' ).ordinal(1), 'st');
        equal(i18n.ordinal(2), 'nd');
        equal(i18n.ordinal(12), 'th');
        equal(i18n.ordinal(22), 'nd');
        equal(i18n.ordinal(3), 'rd');
        equal(i18n.ordinal(4), 'th');
        equal(i18n.ordinal(5), 'th');

        equal(i18n.lang( 'pt_PT' ).ordinal(1), '&ordm;'); // Returns 'º'
        equal(i18n.ordinal(4), '&ordm;'); // Returns 'º'
    });

    test('ordinal (with functions)', function () {
        var dict = {
            'en_US': {
                _ordinals: {
                    byLastDigit: function (digit/*, num*/) {return digit === 0 ? 'th' : undefined;},
                    exceptions: function (num/*, digit*/) {return num === 3 ? 'rd' : undefined;}
                }
            },
            'en_UK': {
                _ordinals: function( num , digit ) {
                        return num === 3   ? 'rd' : 
                               digit === 0 ? 'th' :
                                             undefined;
                }
            }
        };
        var i18n = new I18n(dict, 'en_US');
        equal(i18n.ordinal(0), 'th');
        equal(i18n.ordinal(10), 'th');
        equal(i18n.ordinal(200), 'th');
        equal(i18n.ordinal(3), 'rd');
        equal(i18n.ordinal(123), '');
        equal(i18n.ordinal(12312312), '');
        equal(i18n.lang( 'en_UK' ).ordinal(0), 'th');
        equal(i18n.ordinal(10), 'th');
        equal(i18n.ordinal(200), 'th');
        equal(i18n.ordinal(3), 'rd');
        equal(i18n.ordinal(123), '');
        equal(i18n.ordinal(12312312), '');
    });

    test('multilang', function () {
        var i18n = make();
        i18n.append({
            pt_PT: {
                yeah_text: 'pois'
            },
            en_US: {
                yeah_text: 'yeah'
            }
        });
        equal(i18n.text('yeah_text'), 'pois');
        i18n.lang( 'en_US' );
        equal(i18n.text('yeah_text'), 'yeah');
    });

    test('alias doctest', function () {
        var i18n = new I18n({
           'pt_PT': {
               'hi': 'olá',
               '{} day': '{} dia',
               '{} days': '{} dias',
               '_ordinals': {
                   'default': 'º'
               }
           }
        }, 'pt_PT');
        var _ = i18n.alias();
        equal(_('hi'), 'olá');
        equal(_('{} days', 3), '3 dias');
        equal(_.ntext('{} day', '{} days', 2), '2 dias');
        equal(_.ntext('{} day', '{} days', 1), '1 dia');
        equal(_.ordinal(3), 'º');
    });

    test('escaping braces', function () {
        var i18n = make();
        i18n.lang('pt_PT').append({pt_PT: {
            escNew: '{{}}{{1}}',
            escOld: '{{%s}}{{%s:1}}'
        }});
        equal(i18n.text('escNew'), '{}{1}')
        equal(i18n.text('escOld'), '{%s}{%s:1}')
    });

    (function () {
        var i18n = make();
        i18n.lang('pt_PT').append({
            pt_PT: {
                '{person1} said hi to {person2}': '{person1} disse olá à {person2}',
                '{person-1} said hi to {person-2}': '{person-1} disse olá à {person-2}',
                '{} said hi to {1}': '{} disse olá à {1}',
                '{1} said hi to {person-2}': '{} disse olá à {person-2}',
                '{person-1} said hi to {1}': '{person-1} disse olá à {1}'
            }
        });
        test('named parameters, array parameters', function () {
            equal(i18n.text('{person1} said hi to {person2}', {
                person1: 'root',
                person2: 'sapo'}),
                'root disse olá à sapo');

            equal(i18n.text('{person-1} said hi to {person-2}', {
                'person-1': 'root',
                'person-2': 'sapo'}),
                'root disse olá à sapo');
        });

        test('mixing types', function () {
            equal(i18n.text('{} said hi to {1}', 'root', 'sapo'),
                'root disse olá à root');

            equal(
                i18n.text('{1} said hi to {person-2}', {"person-2": 'sapo'}, 'root'),
                'root disse olá à sapo');

            equal(
                i18n.text('{person-1} said hi to {1}', {'person-1': 'rute'}, 'sapo'),
                'rute disse olá à sapo');
        });
    }());

    test('functions', function () {
        var i18n = make().lang('pt_PT').append({
            pt_PT: {
                'say-hi': function (par1, par2) {
                    return par1 + ' ' + par2
                }
            }
        });
        equal(i18n.text('say-hi', 'par1', 'par2'), 'par1 par2');
    });

    test('arrays, objects', function () {
        var i18n = make().lang('pt_PT').append({
            pt_PT: {
                array: [1, 2],
                object: {'a': '-a-', 'b': '-b-'}
            }
        });
        equal(i18n.text('array', 0), '1');
        equal(i18n.text('object', 'a'), '-a-');
    });

    test('old replacement API compatibility', function () {
        var i18n = make().lang('pt_PT').append({pt_PT: {
            "hello, {%s}": 'olá, {%s}',
            "hello, {%s:1}": 'olá, {%s:1}'
        }});

        equal(
            i18n.text('hello, {%s}', 'coisinho'),
            'olá, coisinho');
        equal(
            i18n.text('hello, {%s:1}', 'coisinho'),
            'olá, coisinho');
    });

    test('Global stuff', function () {
        I18n.lang('en_US')
        equal(new I18n({}).lang(), 'en_US');

        var inst = new I18n({}, 'pt_PT');
        
        I18n.append({
            pt_PT: {
                hello: 'olá'
            },
            pt_BR: {
                hello: 'oi'
            }
        });

        equal(inst.text('hello'), 'olá');
        inst.lang('pt_BR');
        equal(inst.text('hello'), 'oi');

        inst.append({pt_PT: {hello: 'olá2'}, pt_BR: {hello: 'oi2'}});

        inst.lang('pt_PT');
        equal(inst.text('hello'), 'olá2');
        inst.lang('pt_BR');
        equal(inst.text('hello'), 'oi2');
    });
});
