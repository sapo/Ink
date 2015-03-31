Ink.requireModules(['Ink.Util.Validator_1'], function (val) {

var nbsp = '\u00a0';
var tsu = '\u30C4'; // japanese Tsu character (ツ)
var ellipsis = '\u2026'; // ellipsis character (…)
var ccedil = '\u00E7'; // C with a cedilla (ç)

function invalid (a, msg) {
    strictEqual(a, false, msg || 'should be invalid!');
}
function valid (a, msg) {
    strictEqual(a, true, msg || 'should be valid!');
}

module('checkCharacterGroups');
var grps = val.checkCharacterGroups;
test('numbers', function () {
    valid   (grps('1', {numbers: true}));
    invalid (grps('a', {numbers: true}));
});
test('ascii', function () {
    valid   (grps('asd', {asciiAlpha: true}));
    invalid (grps('asd1', {asciiAlpha: true}));
    invalid (grps('asd' + ccedil, {asciiAlpha: true}));
    valid   (grps(' ', {whitespace: true}));
    valid   (grps(' ', {whitespace: true}));
});
test('latin1', function () {
    valid   (grps('asd' + ccedil, {latin1Alpha: true}));
    invalid (grps('asd' + tsu, {latin1Alpha: true}));
    invalid (grps('asd' + ellipsis, {latin1Alpha: true, latin1Punctuation: true}));
    invalid (grps('asd' + nbsp, {latin1Alpha: true, latin1Punctuation: true}));
});
test('unicode', function () {
    valid   (grps('asd', {unicodeAlpha: true}));
    valid   (grps('asd' + tsu + ccedil, {unicodeAlpha: true}));
    invalid (grps(ellipsis, {unicodeAlpha: true}));
    valid   (grps(ellipsis, {unicodePunctuation: true}));
});
test('whitespace', function () {
    valid   (grps(' ', {space: true}));
    invalid (grps('\n', {singleLineWhitespace: true}));
    valid   (grps('\n', {newline: true}));
    valid   (grps(' \t\n', {whitespace: true}));
});
test('undefined values', function () {
    invalid (grps(undefined));
});

module('alphanumeric check functions');
test('unicode', function () {
    valid   (val.unicode('FábioSan' + tsu /* <- Awesome guy*/));
    valid   (val.unicode('Fábio San' + tsu, {whitespace: true}));
    invalid (val.unicode(' '));
    invalid (val.unicode(ellipsis));
});
test('latin1', function () {
    valid   (val.latin1('Fábio'));
    invalid (val.latin1('Fábio Santos'));
    valid   (val.latin1('Fábio Santos', {whitespace: true}));
    invalid (val.latin1('FábioSan' + tsu));
    valid   (val.latin1(ccedil));
});
test('ascii', function () {
    valid   (val.ascii('Fabio'));
    invalid (val.ascii(ccedil));
});


module('EAN');

test('isEAN validating ean-13 codes', function () {
    valid  (val.isEAN('5601314222208'))
    invalid(val.isEAN('5601314222201'))

    invalid(val.isEAN('9780262011531'))

    valid  (val.isEAN('9330071314999'))
    invalid(val.isEAN('9330071314990'))

    valid  (val.isEAN('9330071314999'))
    invalid(val.isEAN('9330071314990'))
    invalid(val.isEAN('0016820054453'))
})

test('isEAN validating ean-8 codes', function () {
    valid  (val.isEAN('96384077', 'ean-8'))
    invalid(val.isEAN('96384074', 'ean-8'))

    invalid(val.isEAN('96384077', 'ean-13'))
    invalid(val.isEAN('96384077'))
});

module('number');
var numb = function (numb, options) {
    return val.number(numb, Ink.extendObj({returnNumber: true}, options));
};

test('integer numbers', function () {
    strictEqual(numb('.'), false);
    strictEqual(numb('123.123', {decimalPlaces: 0}), false);
    strictEqual(numb('123'), 123);
    strictEqual(numb(0), 0);
});

test('decimal numbers', function () {
    strictEqual(numb('0.123', {decimalPlaces: 3}), 0.123);
    strictEqual(numb('.123', {decimalPlaces: 3}), 0.123);
    
    strictEqual(numb('.123', {decimalPlaces: 2}), false);
});

test('negative numbers', function () {
    strictEqual(numb('-1.23', {decimalPlaces: 3}), -1.23);
    strictEqual(numb('-1.23', {decimalPlaces: 3, negative: false}), false);
});

test('max/min', function () {
    var minMax = {min: -100, max: 100};
    strictEqual(numb('123', minMax), false);
    strictEqual(numb('50', minMax), 50);
    strictEqual(numb('-50', minMax), -50);
    strictEqual(numb('-150', minMax), false);
});

test('thousand separator', function () {
    strictEqual(numb('1`344`123'), false);
    strictEqual(numb('1`344`123', {thousandSep: '`'}), 1344123);
    strictEqual(numb('1.344.123'), false);
    strictEqual(numb('1.344.123', {thousandSep: '.'}), 1344123);
    strictEqual(numb('1344123', {thousandSep: '.'}), 1344123);
    strictEqual(numb('1Thousand344Thousand123', {thousandSep: '`'}), false);
    strictEqual(numb('1Thousand344Thousand123', {thousandSep: 'Thousand'}), 1344123);
    strictEqual(numb('Thousand'), false);
    strictEqual(numb('Thousand', {thousandSep: 'Thousand'}), false);
});

test('NaN, Inf', function () {
    strictEqual(numb(Infinity), false);
    strictEqual(numb(-Infinity), false);
    strictEqual(numb(NaN), false);

    strictEqual(numb('Infinity'), false);
    strictEqual(numb('-Infinity'), false);
    strictEqual(numb('NaN'), false);
});

module('dates');

test('_daysInMonth', function () {
    equal(val._daysInMonth( 2, 2004 ), 29, 'february of a leap year');
    equal(val._daysInMonth( 2, 2001 ), 28, 'february of a common year');
    equal(val._daysInMonth( 1, 2010 ), 31, 'january');
    equal(val._daysInMonth( 8, 2010 ), 31, 'august');
});

test('isDate: basic', function () {
    ok(val.isDate('dd-mm-yyyy', '1-1-2000'));
    ok(!val.isDate('dd-mm-yyyy', '1-21-2000'));
});

test('bug: always february in isDate?', function () {
    ok(val.isDate('yyyy-mm-dd', '2001-01-28'), 'this passes');
    ok(val.isDate('yyyy-mm-dd', '2001-01-30'), 'this fails because it\'s always february (???) ');
});

});
