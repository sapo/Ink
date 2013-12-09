import os, sys, urllib2

if 'BUILD_RELEASE' in os.environ:
    ko = "http://knockoutjs.com/downloads/knockout-3.0.0.js"
    strict = "'use strict;'"
else:
    ko = "http://knockoutjs.com/downloads/knockout-3.0.0.debug.js"
    strict = ''

prefix = """/**
 * @module Ink.Data.Binding
 * @author rui.carmo AT sapo.pt
 * @version 1
 */
Ink.createModule(
    'Ink.Data.Binding',      // full module name
    '1',                     // module version
    [],                    // array of dependency modules
    function() {             // this fn will be called asynchronously with dependencies as arguments
        %(strict)s
        /**
         * First attempt at binding Knockout to Ink
         *
         * @class Ink.Data.Binding
         * @static
         */
        // END OF PREFIX
""" % locals()

suffix = """
        // START OF SUFFIX
        return window.ko;
    }
);
"""

open(os.path.join(os.path.dirname(__file__),'lib.js'),'w').write(prefix + urllib2.urlopen(ko).read() + suffix)
