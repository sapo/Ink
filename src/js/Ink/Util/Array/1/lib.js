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
