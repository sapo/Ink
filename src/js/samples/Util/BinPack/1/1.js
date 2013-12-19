Ink.requireModules(['Ink.Util.BinPack_1'], function(IBP) {
    var r256 = function() { return ~~(Math.random() * 196); };

    var genBlocks = function() {
        var i, f, num, bl, res = [];
        for (i = 0, f = arguments.length; i < f; ++i) {
            bl = arguments[i];
            bl.area = bl.w * bl.h;
            if ('num' in bl) {
                num = bl.num;
                res = new Array(num).concat(res);
                while (num) {
                    res[ --num ] = { w:bl.w, h:bl.h, area:bl.area };
                }
            }
            else {
                res.push(bl);
            }
        }
        return res;
    };



    // example configurations
    var ex1 = {
        dims: [512, 512],
        blocks: genBlocks(
            {w:  2, h:  2, num:256}, // powers of 2
            {w:  4, h:  4, num:128},
            {w:  8, h:  8, num: 64},
            {w: 16, h: 16, num: 32},
            {w: 32, h: 32, num: 16},
            {w: 64, h: 64, num:  8},
            {w:128, h:128, num:  4},
            {w:256, h:256, num:  2} ),
        sorter: 'area'
    };

    var ex2 = {
        blocks: genBlocks(
            {w:100, h:100, num:  3}, // tall
            {w: 60, h: 60, num:  3},
            {w: 50, h: 20, num: 20},
            {w: 20, h: 50, num: 20},
            {w:250, h:250, num:  1},
            {w:250, h:100, num:  1},
            {w:100, h:250, num:  1},
            {w:400, h: 80, num:  1},
            {w:80,  h:400, num:  1},
            {w: 10, h: 10, num:100},
            {w:  5, h:  5, num:500} ),
        sorter: 'maxside'
    };

    var ex3 = {
        blocks: genBlocks(
            {w:50, h:400, num: 2}, // complex
            {w:50, h:300, num: 5},
            {w:50, h:200, num:10},
            {w:50, h:100, num:20},
            {w:50, h: 50, num:40} ),
        sorter: 'maxside'
    };



    // apply bin pack...
    var r = IBP.binPack(ex2);



    // display stuff...
    console.log(r);

    var cvsEl = Ink.i('c');
    var ctx = cvsEl.getContext('2d');

    Ink.i('filled'  ).innerHTML = (r.filled * 100).toFixed(2) + ' %';
    Ink.i('unfitted').innerHTML = ['(', r.unfitted.length, ' / ', r.blocks.length, '):<br/>', r.unfitted.join('<br/>') ].join('');
    Ink.i('dims'    ).innerHTML = r.dimensions.join(' x ');

    cvsEl.setAttribute('width',  r.dimensions[0]);
    cvsEl.setAttribute('height', r.dimensions[1]);

    r.fitted.forEach(function(bl) {
      ctx.fillStyle = ['rgb(', r256(), ',', r256(), ',', r256(), ')'].join('');
      ctx.fillRect(bl.fit.x, bl.fit.y, bl.w, bl.h);
    });

});
