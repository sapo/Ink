var cluster = require('cluster');
var http = require('http');
var fs = require('fs');
var query = require('querystring');
//var recess = require('recess');
var less = require('less');

//var numCPUs = require('os').cpus().length;

var configserver = require('./configserver.js').configserver;
var configless = require('./configless.js').configless;


function InkCssMaker(_conf) {

    this.init(_conf);

};

InkCssMaker.prototype = {

    init: function(config) 
    {
        this._lessFilesContent = {};

        this._readLessFiles();

        this._numProcs = 4;

        this._initForks(config);
        //this._runServer(config); 

    },

    _initForks: function(config)
    {
        if(cluster.isMaster) {
            for(var i=0; i < this._numProcs; i++) {
                cluster.fork();
            }

            var _that = this;
            cluster.on('exit', function(worker, code, signal) {
                        console.log('worker ' + worker.process.pid + ' died');
                        cluster.fork();
                    });
        } else {
            this._runServer(config); 
        }
    },

    _runServer: function(config)
    {
        var _that = this;
        http.createServer(function (req, resp) {

                switch(req.url) {
                    case '/':
                        resp.writeHead(200, "OK", {'Content-Type': 'text/html'});
                        resp.end(_that._procRoot());
                    break;
                    case '/form':
                        resp.writeHead(200, "OK", {'Content-Type': 'text/html'});
                        resp.end(_that._procForm());
                    break;
                    case '/getcss':
                        if(req.method == 'POST') {
                            var body = '';
                            req.on('data', function(chunk) {
                                body += chunk.toString();
                            });
                            req.on('end', function(chunk) {
                                _that._procGetCss(resp, body);
                                /*
                                resp.writeHead(200, "OK", {'Content-Type': 'text/plain'});
                                resp.end(_that._procGetCss(body));
                                */
                            });
                        } else {
                            resp.writeHead(405, "Method not supported", {'Content-Type': 'text/html'});
                            resp.end('<html><head></head><body><h1>405 Method not supported</body></html>\n');
                        }
                    break;
                    default:
                        resp.writeHead(404, "Not found", {'Content-Type': 'text/html'});
                        resp.end('<html><head></head><body><h1>404 Not Found</body></html>\n');
                }

        }).listen(config.port, config.host);
    },

    _procRoot: function(chunk) 
    {
        var aStr = [
            '<html>',
            '<head>',
            '</head>',
            '<body>',
                '<h1>Make a POST</h1>',
                '<hr />',
                '<a href="/form">form</a>',
                '',
                '',
                '',
            '<body>',
            '</html>'
            ];
        return aStr.join("\n");
    },

    _procForm: function() 
    {
        var aStr = [
            '<html>',
            '<head></head>',
            '<body>',
            //'<form method="post" enctype="multipart/form-data" action="/getzip">',
            '<form method="post" action="/getcss">',
            /*
            '<input type="text" name="nome1[]" value="um valor1"><br />',
            '<input type="text" name="nome1[]" value="um valor2"><br />',
            '<input type="text" name="nome1[]" value="um valor3"><br />',
            '<input type="text" name="nome1[]" value="um valor4"><br />',
            '<textarea name="less" cols="60" rows="10"></textarea><br />',
            */
            //'<input type="file" name="file" ><br />',
            '',
            '<input type="submit" value="Submit">',
            '</form>',
            '</body></html>'
        ];
        return aStr.join("\n");
    
    },
    
    _procGetCss: function(resp, body)
    {
        //console.log(body);
        var aPost = query.parse(body);
        console.log(aPost);

        var choosenLessFiles = [];
        if(typeof(aPost['modules[]']) != 'undefined' && aPost['modules[]'].length > 0) {
            for(var i=0, t = aPost['modules[]'].length; i < t; i++) {
                if(configless.files.indexOf(aPost['modules[]'][i]+'.less') > -1) {
                    choosenLessFiles.push(aPost['modules[]'][i]+'.less');
                }
            }
        }

        if(choosenLessFiles.length === 0) {
            resp.writeHead(406, "Not Acceptable", {'Content-Type': 'text/plain'});
            resp.end('406 - Not Acceptable');
            return;
        }

        var choosenLessVars = [];
        if(typeof(aPost['vars[]']) != 'undefined' && aPost['vars[]'].length > 0) {
            for(var i=0, t = aPost['vars[]'].length; i < t; i++) {
                choosenLessVars.push(aPost['vars[]'][i]+';');
            }
        }


        //console.log('#############\n');
        console.log(choosenLessFiles);
        console.log(choosenLessVars);
        //console.log('#############\n');

        var _compress = false;
        if(typeof(aPost['compress']) != 'undefined') {
            _compress = true;
        }

        var content = '';
        var aLess = [];
        var curCont = false;
        for(var i=0, t=choosenLessFiles.length; i < t; i++) {

            var curCont = this._lessFilesContent[encodeURIComponent(choosenLessFiles[i])];

            content += curCont+"\n\n";
        }

        if(choosenLessVars.length > 0) {
            content += choosenLessVars.join("\n");
        }

        console.log(content);

        var lessParser = new(less.Parser);

        lessParser.parse(content, function (e, t) {
                if(e) {
                    console.log('ERROR:');
                    console.log(e);
                    resp.writeHead(501, "Server Error", {'Content-Type': 'text/plain'});
                    resp.end('501 - Server Error');
                    return;
                }
                var css = t.toCSS({ compress: _compress });

                console.log("\n ############################### \n");
                console.log(css);
                //var css = t.toCSS();
                resp.writeHead(200, "OK", {'Content-Type': 'text/css'});
                resp.end(css);
        });


        /*
        var lessFileName = this._createInkLess(aLess);

        console.log(lessFileName);

        if(lessFileName) {
            recess([lessFileName], {compile: true}, function(err, obj) {
                if (err) { 
                    console.log(err);
                    return;
                }
                console.log(obj.output.length);
                console.log(obj.errors);

                var out = '';
                for(var i=0, t=obj.output.length; i < t; i++) {
                    out += obj.output[i];
                }

                resp.writeHead(200, "OK", {'Content-Type': 'text/plain'});
                resp.end(out);
            });
        }
        */
        return;
    },

    _readLessFiles: function()
    {
        for(var i=0, t=configless.files.length; i < t; i++) {
            //console.log(configless.path + configless.files[i]);
            this._lessFilesContent[encodeURIComponent(configless.files[i])] = fs.readFileSync(configless.path + configless.files[i], 'utf8');

        }
    },


    debug: function() {}
};


new InkCssMaker(configserver);
console.log('Server running at http://'+configserver.host+':'+configserver.port+'/');

