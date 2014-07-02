/**
 * Array Utilities
 * @module Ink.Util.Array_1
 * @version 1
 */

Ink.createModule('Ink.Util.Array', '1', [], function() {

    'use strict';

    var arrayProto = Array.prototype;

    /**
     * @namespace Ink.Util.Array_1
     */

    var InkArray = {

        /**
         * Checks if a value is an array
         *
         * @method isArray
         * @param testedObject {Mixed} The object we want to check
         **/
        isArray: Array.isArray || function (testedObject) {
            return {}.toString.call(testedObject) === '[object Array]';
        },

        /**
         * Loops through an array, grouping similar items together.
         * @method groupBy
         * @param arr {Array} The input array.
         * @param [options] {Object} options object, containing:
         * @param [options.key] {Function} A function which computes the group key by which the items are grouped.
         * @param [options.pairs] {Boolean} Set to `true` if you want to output an array of `[key, [group...]]` pairs instead of an array of groups.
         * @return {Array} An array of arrays of chunks.
         *
         * @example
         *
         *        InkArray.groupBy([1, 1, 2, 2, 3, 1])  // -> [ [1, 1], [2, 2], [3], [1] ]
         *        InkArray.groupBy([1.1, 1.2, 2.1], { key: Math.floor })  // -> [ [1.1, 1.2], [2.1] ]
         *        InkArray.groupBy([1.1, 1.2, 2.1], { key: Math.floor, pairs: true })  // -> [ [1, [1.1, 1.2]], [2, [2.1]] ]
         *
         **/
        groupBy: function (arr, options) {
            options = options || {};
            var ret = [];
            var latestGroup;
            function eq(a, b) {
                return outKey(a) === outKey(b);
            }
            function outKey(item) {
                if (typeof options.key === 'function') {
                    return options.key(item);
                } else {
                    return item;
                }
            }

            for (var i = 0, len = arr.length; i < len; i++) {
                latestGroup = [arr[i]];

                // Chunkin'
                while ((i + 1 < len) && eq(arr[i], arr[i + 1])) {
                    latestGroup.push(arr[i + 1]);
                    i++;
                }

                if (options.pairs) {
                    ret.push([outKey(arr[i]), latestGroup]);
                } else {
                    ret.push(latestGroup);
                }
            }
            return ret;
        },

        /**
         * Replacement for Array.prototype.reduce.
         *
         * Produces a single result from a list of values by calling an "aggregator" function.
         *
         * Falls back to Array.prototype.reduce if available.
         *
         * @method reduce
         * @param array {Array} Input array to be reduced.
         * @param callback {Function} `function (previousValue, currentValue, index, all) { return {Mixed} }` to execute for each value.
         * @param initial {Mixed} Object used as the first argument to the first call of `callback`
         *
         * @example
         *          var sum = InkArray.reduce([1, 2, 3], function (a, b) { return a + b; });  // -> 6
         */
        reduce: function (array, callback, initial) {
            if (arrayProto.reduce) {
                return arrayProto.reduce.apply(array, [].slice.call(arguments, 1));
            }

            // From https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/Reduce#Polyfill
            var t = Object( array ), len = t.length >>> 0, k = 0, value;
            if ( arguments.length >= 3 ) {
                value = initial;
            } else {
                while ( k < len && !(k in t) ) k++;
                if ( k >= len )
                    throw new TypeError('Reduce of empty array with no initial value');
                value = t[ k++ ];
            }
            for ( ; k < len ; k++ ) {
                if ( k in t ) {
                    value = callback( value, t[k], k, t );
                }
            }
            return value;
        },

        /**
         * Checks if a value exists in array
         *
         * @method inArray
         * @public
         * @static
         * @param {Mixed} value     Value to check
         * @param {Array} arr       Array to search in
         * @return {Boolean}        True if value exists in the array
         * @sample Ink_Util_Array_inArray.html
         */
        inArray: function(value, arr) {
            if (typeof arr === 'object') {
                for (var i = 0, f = arr.length; i < f; ++i) {
                    if (arr[i] === value) {
                        return true;
                    }
                }
            }
            return false;
        },

        /**
         * Sorts an array of objects by an object property
         *
         * @method sortMulti
         * @param {Array}           arr         Array of objects to sort
         * @param {String}  key         Property to sort by
         * @return {Array|Boolean}      False if it's not an array, returns a sorted array if it's an array.
         * @public
         * @static
         * @sample Ink_Util_Array_sortMulti.html
         */
        sortMulti: function(arr, key) {
            if (typeof arr === 'undefined' || arr.constructor !== Array) { return false; }
            if (typeof key !== 'string') { return arr.sort(); }
            if (arr.length > 0) {
                if (typeof(arr[0][key]) === 'undefined') { return false; }
                arr.sort(function(a, b){
                    var x = a[key];
                    var y = b[key];
                    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
                });
            }
            return arr;
        },

        /**
         * Gets the indexes of a value in an array
         *
         * @method keyValue
         * @param   {String}      value     Value to search for.
         * @param   {Array}       arr       Array to run the search in.
         * @param   {Boolean}     [first]   Flag to stop the search at the first match. It also returns an index number instead of an array of indexes.
         * @return  {Boolean|Number|Array}  False for no matches. Array of matches or first match index.
         * @public
         * @static
         * @sample Ink_Util_Array_keyValue.html
         */
        keyValue: function(value, arr, first) {
            if (typeof value !== 'undefined' && typeof arr === 'object' && this.inArray(value, arr)) {
                var aKeys = [];
                for (var i = 0, f = arr.length; i < f; ++i) {
                    if (arr[i] === value) {
                        if (typeof first !== 'undefined' && first === true) {
                            return i;
                        } else {
                            aKeys.push(i);
                        }
                    }
                }
                return aKeys;
            }
            return false;
        },

        /**
         * Shuffles an array.
         *
         * @method shuffle
         * @param   {Array}       arr    Array to shuffle
         * @return  {Array|Boolean}      Shuffled Array or false if not an array.
         * @public
         * @static
         * @sample Ink_Util_Array_shuffle.html
         */
        shuffle: function(arr) {
            if (typeof(arr) !== 'undefined' && arr.constructor !== Array) { return false; }
            var total   = arr.length,
                tmp1    = false,
                rnd     = false;

            while (total--) {
                rnd        = Math.floor(Math.random() * (total + 1));
                tmp1       = arr[total];
                arr[total] = arr[rnd];
                arr[rnd]   = tmp1;
            }
            return arr;
        },

        /**
         * Runs a function through each of the elements of an array
         *
         * @method forEach
         * @param   {Array}     arr     The array to be cycled/iterated
         * @param   {Function}  cb      The function receives as arguments the value, index and array.
         * @return  {Array}             Iterated array.
         * @public
         * @static
         * @sample Ink_Util_Array_forEach.html
         */
        forEach: function(array, callback, context) {
            if (arrayProto.forEach) {
                return arrayProto.forEach.call(array, callback, context);
            }
            for (var i = 0, len = array.length >>> 0; i < len; i++) {
                callback.call(context, array[i], i, array);
            }
        },

        /**
         * Alias for backwards compatibility. See forEach
         *
         * @method each
         */
        each: function () {
            InkArray.forEach.apply(InkArray, [].slice.call(arguments));
        },

        /**
         * Runs a function for each item in the array.
         * That function will receive each item as an argument and its return value will change the corresponding array item.
         * @method map
         * @param {Array}       array       The array to map over
         * @param {Function}    map         The map function. Will take `(item, index, array)` as arguments and `this` will be the `context` argument.
         * @param {Object}      [context]   Object to be `this` in the map function.
         *
         * @sample Ink_Util_Array_map.html
         */
        map: function (array, callback, context) {
            if (arrayProto.map) {
                return arrayProto.map.call(array, callback, context);
            }
            var mapped = new Array(len);
            for (var i = 0, len = array.length >>> 0; i < len; i++) {
                mapped[i] = callback.call(context, array[i], i, array);
            }
            return mapped;
        },

        /**
         * Filters an array based on a truth test.
         * This method runs a test function on all the array values and returns a new array with all the values that pass the test.
         * @method filter
         * @param {Array}       array       The array to filter
         * @param {Function}    test        A test function taking `(item, index, array)`
         * @param {Object}      [context]   Object to be `this` in the test function.
         * @return {Array}                  Returns the filtered array
         *
         * @sample Ink_Util_Array_filter.html
         */
        filter: function (array, test, context) {
            if (arrayProto.filter) {
                return arrayProto.filter.call(array, test, context);
            }
            var filtered = [],
                val = null;
            for (var i = 0, len = array.length; i < len; i++) {
                val = array[i]; // it might be mutated
                if (test.call(context, val, i, array)) {
                    filtered.push(val);
                }
            }
            return filtered;
        },

        /**
         * Checks if some element in the array passes a truth test
         *
         * @method some
         * @param   {Array}       arr       The array to iterate through
         * @param   {Function}    cb        The callback to be called on the array's elements. It receives the value, the index and the array as arguments.
         * @param   {Object}      context   Object of the callback function
         * @return  {Boolean}               True if the callback returns true at any point, false otherwise
         * @public
         * @static
         * @sample Ink_Util_Array_some.html
         */
        some: function(arr, cb, context){

            if (arr === null){
                throw new TypeError('First argument is invalid.');
            }

            var t = Object(arr);
            var len = t.length >>> 0;
            if (typeof cb !== "function"){ throw new TypeError('Second argument must be a function.'); }

            for (var i = 0; i < len; i++) {
                if (i in t && cb.call(context, t[i], i, t)){ return true; }
            }

            return false;
        },

        /**
         * Compares the values of two arrays and return the matches
         *
         * @method intersect
         * @param   {Array}   arr1      First array
         * @param   {Array}   arr2      Second array
         * @return  {Array}             Empty array if one of the arrays is false (or do not intersect) | Array with the intersected values
         * @public
         * @static
         * @sample Ink_Util_Array_intersect.html
         */
        intersect: function(arr1, arr2) {
            if (!arr1 || !arr2 || arr1 instanceof Array === false || arr2 instanceof Array === false) {
                return [];
            }

            var shared = [];
            for (var i = 0, I = arr1.length; i<I; ++i) {
                for (var j = 0, J = arr2.length; j < J; ++j) {
                    if (arr1[i] === arr2[j]) {
                        shared.push(arr1[i]);
                    }
                }
            }

            return shared;
        },

        /**
         * Converts an array-like object to an array
         *
         * @method convert
         * @param   {Array}   arr   Array to be converted
         * @return  {Array}         Array resulting of the conversion
         * @public
         * @static
         * @sample Ink_Util_Array_convert.html
         */
        convert: function(arr) {
            return arrayProto.slice.call(arr || [], 0);
        },

        /**
         * Removes duplicated values in an array.
         *
         * @method unique
         * @param {Array}   arr   Array to filter
         * @return {Array}        Array with only unique values
         * @public
         * @static
         */
        unique: function(arr){
            if(!Array.prototype.lastIndexOf){ //IE8 slower alternative
                var newArr = [];

                InkArray.forEach(InkArray.convert(arr), function(i){
                    if(!InkArray.inArray(i,newArr)){
                        newArr.push(i);
                    }
                });
                return newArr;
            }//else
            return InkArray.filter(InkArray.convert(arr), function (e, i, arr) {
                            return arr.lastIndexOf(e) === i;
                        });
        },

        /**
         * Simulates python's range(start, stop, step) function.
         *
         * Creates a list with numbers counting from start until stop, using a for loop.
         *.
         * The optional step argument defines how to step ahead. You can pass a negative number to count backwards (see the examples below).
         *
         * @method range
         * @param {Number} start    The array's first element.
         * @param {Number} stop     Stop counting before this number.
         * @param {Number} [step=1] Interval between numbers. You can use a negative number to count backwards.
         *
         * @sample Ink_Util_Array_1_range.html
         **/
        range: function range(a, b, step) {
            // From: https://github.com/mcandre/node-range
            if (!step) {
                step = 1;
            }

            var r = [];
            var x;

            if (step > 0) {
                for (x = a; x < b; x += step) {
                    r.push(x);
                }
            } else {
                for (x = a; x > b; x += step) {
                    r.push(x);
                }
            }

            return r;
        },

        /**
         * Inserts a value on a specified index
         *
         * @method insert
         * @param {Array}   arr     Array where the value will be inserted
         * @param {Number}  idx     Index of the array where the value should be inserted
         * @param {Mixed}   value   Value to be inserted
         * @public
         * @static
         * @sample Ink_Util_Array_insert.html
         */
        insert: function(arr, idx, value) {
            arr.splice(idx, 0, value);
        },

        /**
         * Removes a range of values from the array
         *
         * @method remove
         * @param   {Array}     arr     Array where the value will be removed
         * @param   {Number}    from    Index of the array where the removal will start removing.
         * @param   {Number}    rLen    Number of items to be removed from the index onwards.
         * @return  {Array}             An array with the remaining values
         * @public
         * @static
         * @sample Ink_Util_Array_remove.html
         */
        remove: function(arr, from, rLen){
            var output = [];

            for(var i = 0, iLen = arr.length; i < iLen; i++){
                if(i >= from && i < from + rLen){
                    continue;
                }

                output.push(arr[i]);
            }

            return output;
        }
    };

    return InkArray;

});
