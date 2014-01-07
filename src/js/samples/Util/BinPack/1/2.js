Ink.requireModules(
    ['Ink.Util.BinPack_1', 'Ink.Util.Image_1'],
    function(IBP, IImg) {

    var maxDims = [196, 196];

    var images = [
        'http://lorempixel.com/196/128/sports/1/',
        'http://lorempixel.com/196/128/sports/2/',
        'http://lorempixel.com/196/128/sports/3/',
        'http://lorempixel.com/128/128/sports/4/',
        'http://lorempixel.com/128/128/sports/5/',
        'http://lorempixel.com/128/128/sports/6/',
        'http://lorempixel.com/128/196/sports/7/',
        'http://lorempixel.com/128/196/sports/8/',
        'http://lorempixel.com/128/196/sports/9/',
        'http://lorempixel.com/196/128/nature/1/',
        'http://lorempixel.com/196/128/nature/2/',
        'http://lorempixel.com/196/128/nature/3/',
        'http://lorempixel.com/128/128/nature/4/',
        'http://lorempixel.com/128/128/nature/5/',
        'http://lorempixel.com/128/128/nature/6/',
        'http://lorempixel.com/128/196/nature/7/',
        'http://lorempixel.com/128/196/nature/8/',
        'http://lorempixel.com/128/196/nature/9/',
        'http://lorempixel.com/196/128/nightlife/1/',
        'http://lorempixel.com/196/128/nightlife/2/',
        'http://lorempixel.com/196/128/nightlife/3/',
        'http://lorempixel.com/128/128/nightlife/4/',
        'http://lorempixel.com/128/128/nightlife/5/',
        'http://lorempixel.com/128/128/nightlife/6/',
        'http://lorempixel.com/128/196/nightlife/7/',
        'http://lorempixel.com/128/196/nightlife/8/',
        'http://lorempixel.com/128/196/nightlife/9/',
        'http://lorempixel.com/196/128/fashion/1/',
        'http://lorempixel.com/196/128/fashion/2/',
        'http://lorempixel.com/196/128/fashion/3/',
        'http://lorempixel.com/128/128/fashion/4/',
        'http://lorempixel.com/128/128/fashion/5/',
        'http://lorempixel.com/128/128/fashion/6/'/*,
        'http://lorempixel.com/128/196/fashion/7/',
        'http://lorempixel.com/128/196/fashion/8/',
        'http://lorempixel.com/128/196/fashion/9/'*/
    ];

    IImg.measureImages(images, function(o) {
        // create image blocks
        var uri, dims, dims2, images = [];
        for (uri in o.measured) {
            dims = o.measured[uri];
            dims2 = IImg.maximizeBox(maxDims, dims)[0];
            images.push({
                uri: uri,
                dims: dims,
                w: dims2[0],
                h: dims2[1]
            });
        }



        // apply bin pack...
        var r = IBP.binPack({
            blocks: images
            //dimensions: [512, 512],
            //sorter: 'width'
        });



        // display stuff...
        var ctnEl = Ink.i('ctn');
        var st = ctnEl.style;
        st.width  = r.dimensions[0] + 'px';
        st.height = r.dimensions[1] + 'px';

        var i, f, img, imgEl;
        for (i = 0, f = r.fitted.length; i < f; ++i) {
            img = r.fitted[i];
            imgEl = document.createElement('image');
            imgEl.setAttribute('width',  img.w);
            imgEl.setAttribute('height', img.h);
            st = imgEl.style;
            st.left = img.fit.x + 'px';
            st.top  = img.fit.y + 'px';
            imgEl.src = img.uri;
            ctnEl.appendChild(imgEl);
        }

        if (r.unfitted.length) {
            alert('Unfitted: ' + r.unfitted.length);
        }

    });

});
