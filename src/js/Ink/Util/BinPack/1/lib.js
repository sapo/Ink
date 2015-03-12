/**
 * Binary Packing algorithm implementation
 * @module Ink.Util.BinPack_1
 * @version 1
 */

Ink.createModule('Ink.Util.BinPack', '1', [], function() {

    'use strict';

    /*jshint boss:true */

    // https://github.com/jakesgordon/bin-packing/

    /*
        Copyright (c) 2011, 2012, 2013 Jake Gordon and contributors

        Permission is hereby granted, free of charge, to any person obtaining a copy
        of this software and associated documentation files (the "Software"), to deal
        in the Software without restriction, including without limitation the rights
        to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
        copies of the Software, and to permit persons to whom the Software is
        furnished to do so, subject to the following conditions:

        The above copyright notice and this permission notice shall be included in all
        copies or substantial portions of the Software.

        THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
        IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
        FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
        AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
        LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
        OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
        SOFTWARE.
    */



    var Packer = function(w, h) {
        this.init(w, h);
    };

    Packer.prototype = {

        init: function(w, h) {
            this.root = { x: 0, y: 0, w: w, h: h };
        },

        fit: function(blocks) {
            var n, node, block;
            for (n = 0; n < blocks.length; ++n) {
                block = blocks[n];
                if (node = this.findNode(this.root, block.w, block.h)) {
                    block.fit = this.splitNode(node, block.w, block.h);
                }
            }
        },

        findNode: function(root, w, h) {
            if (root.used) {
                return this.findNode(root.right, w, h) || this.findNode(root.down, w, h);
            }
            else if ((w <= root.w) && (h <= root.h)) {
                return root;
            }
            else {
                return null;
            }
        },

        splitNode: function(node, w, h) {
            node.used = true;
            node.down  = { x: node.x,     y: node.y + h, w: node.w,     h: node.h - h };
            node.right = { x: node.x + w, y: node.y,     w: node.w - w, h: h          };
            return node;
        }

    };



    var GrowingPacker = function() {};

    GrowingPacker.prototype = {

        fit: function(blocks) {
            var n, node, block, len = blocks.length;
            var w = len > 0 ? blocks[0].w : 0;
            var h = len > 0 ? blocks[0].h : 0;
            this.root = { x: 0, y: 0, w: w, h: h };
            for (n = 0; n < len ; n++) {
                block = blocks[n];
                if (node = this.findNode(this.root, block.w, block.h)) {
                    block.fit = this.splitNode(node, block.w, block.h);
                }
                else {
                    block.fit = this.growNode(block.w, block.h);
                }
            }
        },

        findNode: function(root, w, h) {
            if (root.used) {
                return this.findNode(root.right, w, h) || this.findNode(root.down, w, h);
            }
            else if ((w <= root.w) && (h <= root.h)) {
                return root;
            }
            else {
                return null;
            }
        },

        splitNode: function(node, w, h) {
            node.used = true;
            node.down  = { x: node.x,     y: node.y + h, w: node.w,     h: node.h - h };
            node.right = { x: node.x + w, y: node.y,     w: node.w - w, h: h          };
            return node;
        },

        growNode: function(w, h) {
            var canGrowDown  = (w <= this.root.w);
            var canGrowRight = (h <= this.root.h);

            var shouldGrowRight = canGrowRight && (this.root.h >= (this.root.w + w)); // attempt to keep square-ish by growing right when height is much greater than width
            var shouldGrowDown  = canGrowDown  && (this.root.w >= (this.root.h + h)); // attempt to keep square-ish by growing down  when width  is much greater than height

            if (shouldGrowRight) {
                return this.growRight(w, h);
            }
            else if (shouldGrowDown) {
                return this.growDown(w, h);
            }
            else if (canGrowRight) {
                return this.growRight(w, h);
            }
            else if (canGrowDown) {
                return this.growDown(w, h);
            }
            else {
                return null; // need to ensure sensible root starting size to avoid this happening
            }
        },

        growRight: function(w, h) {
            this.root = {
                used: true,
                x: 0,
                y: 0,
                w: this.root.w + w,
                h: this.root.h,
                down: this.root,
                right: { x: this.root.w, y: 0, w: w, h: this.root.h }
            };
            var node;
            if (node = this.findNode(this.root, w, h)) {
                return this.splitNode(node, w, h);
            }
            else {
                return null;
            }
        },

        growDown: function(w, h) {
            this.root = {
                used: true,
                x: 0,
                y: 0,
                w: this.root.w,
                h: this.root.h + h,
                down:  { x: 0, y: this.root.h, w: this.root.w, h: h },
                right: this.root
            };
            var node;
            if (node = this.findNode(this.root, w, h)) {
                return this.splitNode(node, w, h);
            }
            else {
                return null;
            }
        }

    };



    var sorts = {
        random:  function() { return Math.random() - 0.5; },
        w:       function(a, b) { return b.w - a.w; },
        h:       function(a, b) { return b.h - a.h; },
        a:       function(a, b) { return b.area - a.area; },
        max:     function(a, b) { return Math.max(b.w, b.h) - Math.max(a.w, a.h); },
        min:     function(a, b) { return Math.min(b.w, b.h) - Math.min(a.w, a.h); },
        height:  function(a, b) { return sorts.msort(a, b, ['h', 'w']);               },
        width:   function(a, b) { return sorts.msort(a, b, ['w', 'h']);               },
        area:    function(a, b) { return sorts.msort(a, b, ['a', 'h', 'w']);          },
        maxside: function(a, b) { return sorts.msort(a, b, ['max', 'min', 'h', 'w']); },
        msort:   function(a, b, criteria) { /* sort by multiple criteria */
            var diff, n;
            for (n = 0; n < criteria.length; ++n) {
                diff = sorts[ criteria[n] ](a, b);
                if (diff !== 0) {
                    return diff;
                }
            }
            return 0;
        }
    };



    // end of Jake's code



    // aux, used to display blocks in unfitted property
    var toString = function() {
      return [this.w, ' x ', this.h].join('');
    };



    /**
     * Binary Packing algorithm implementation
     *
     * Based on the work of Jake Gordon
     *
     * see https://github.com/jakesgordon/bin-packing/
     *
     * @namespace Ink.Util.BinPack
     * @version 1
     * @static
     */
    var BinPack = {

        /**
        * @method binPack
        * @param {Object}       o               Options
        * @param {Array}        o.blocks        Array of items with width and height integer attributes.
        * @param {Array}        [o.dimensions]  Flag to fix container dimensions
        * @param {String}       [o.sorter]      Sorting function. One of: random, height, width, area, maxside
        * @return {Object}                      Returns an object containing container dimensions, filled ratio, fitted blocks, unfitted blocks and all blocks
        * @static
        */
        binPack: function(o) {
            var i, f, bl;



            // calculate area if not there already
            for (i = 0, f = o.blocks.length; i < f; ++i) {
                bl = o.blocks[i];
                if (! ('area' in bl) ) {
                    bl.area = bl.w * bl.h;
                }
            }



            // apply algorithm
            var packer = o.dimensions ? new Packer(o.dimensions[0], o.dimensions[1]) : new GrowingPacker();

            if (!o.sorter) { o.sorter = 'maxside'; }

            o.blocks.sort( sorts[ o.sorter ] );

            packer.fit(o.blocks);

            var dims2 = [packer.root.w, packer.root.h];



            // layout is done here, generating report data...
            var fitted   = [];
            var unfitted = [];

            for (i = 0, f = o.blocks.length; i < f; ++i) {
                bl = o.blocks[i];
                if (bl.fit) {
                    fitted.push(bl);
                }
                else {
                    bl.toString = toString; // TO AID SERIALIZATION
                    unfitted.push(bl);
                }
            }

            var area = dims2[0] * dims2[1];
            var fit = 0;
            for (i = 0, f = fitted.length; i < f; ++i) {
                bl = fitted[i];
                fit += bl.area;
            }

            return {
                dimensions: dims2,
                filled:     fit / area,
                blocks:     o.blocks,
                fitted:     fitted,
                unfitted:   unfitted
            };
        }
    };



    return BinPack;

});