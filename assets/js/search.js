// Ink.requireModules(['Ink.Net.Ajax_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Dom.Event_1','Ink.Util.Json_1','Ink.Util.Url_1'],function(ajax,css,elm,sel,ev,json,url){
//
//
//
//   function addEvent(selector) {
//     if(typeof(selector) === 'string') {
//       var elm = Ink.ss(selector);
//     } else {
//       var elm = selector;
//     }
//
//     ev.observeMulti(elm, 'submit', function(event) {
//
//       var currentUrl = url.parseUrl(url.getUrl());
//
//       if(currentUrl.path === '/search/') {
//         event.preventDefault();
//       }
//
//       var searchString = Ink.s('.search-field');
//
//       if(searchString.value !== ''){
//         doRequest(searchString.value);
//       }
//
//     });
//   };
//
//   function fillSearchInput(selector){
//     var qs = url.getQueryString(url.getUrl());
//     var searchFields = Ink.ss(selector);
//     if(qs.search){
//       for(var i=0; i<searchFields.length; i++){
//       if(searchFields[i].value === "") {
//           searchFields[i].value = qs.search;
//         }
//       }
//     }
//
//   }
//
//   var doOnSuccess = function(json) {
//
//     console.log(json);
//
//     Ink.log(json);
//     if(response.numFound === 0) {
//       return;
//     }
//
//     var pages = json.response.docs;
//     var highlights = json.highlights;
//
//     var cur;
//     for(var i=0, t=pages.length; i < t; i++) {
//       cur = pages[i];
//       Ink.log(cur);
//       Ink.log(highlights[cur.RSSWorksId])
//     }
//
//   }
//
//   function doRequest(value) {
//
//     console.log('search: ' + value);
//
//     var request = new ajax(
//       'http://services.sapo.pt/RSS/Feed/site/ink',
//       {
//         method: 'get',
//         cors: true,
//         paramenters: 'wt=json&indent=on&fl=Title,Url,RSSWorksId&hl=true&q='+encodeURIComponent(value),
//         onSuccess: function(req) {
//           if(req.responseJSON) {
//             var json = req.responseJSON;
//             doOnSuccess(req);
//           }
//         },
//         onFailure: function () {
//           Ink.warn('failed')
//         }
//       }
//       );
//   }
//
//   addEvent('.docsearch');
//   fillSearchInput('.search-field');
//
//
// });


Ink.requireModules(['Ink.Net.Ajax_1', 'Ink.Dom.Event_1', 'Ink.Util.Url_1', 'Ink.Util.Json_1','Ink.Dom.Css_1','Ink.Dom.Element_1'], function(InkAjax, InkEvent, UtilUrl, UtilJson, InkCss, InkElement) {


    function addEvent() {
        InkEvent.observe(Ink.s('.docsearch'), 'submit', function(event) {
            InkEvent.stopDefault(event);
            var form = InkEvent.element(event);
            var elm = form['search']
            var val = elm.value;
            if(val !== '') {
                window.location.href = '?search='+val;
            }
        });
    }

    function processResponse(json) {
        if(json && json.response && json.response.numFound > 0) {
            var docs = json.response.docs;
            var cur, hlId, results, resultsList;
            results = Ink.s('#search-results');
            resultsList = InkElement.create('ul',{
                className: 'results-list unstyled',
                insertBottom: results
            });

            InkCss.addClassName(results,'show');

            for(var i=0, t=docs.length; i < t; i++) {
                cur = docs[i];
                hlId = json.highlighting[cur.RSSWorksId];
                var result = InkElement.create('li',{
                  className: 'result-item',
                  insertBottom: resultsList
                });
                result.innerHTML = '<h2><a href="'+cur.Url+'" title="'+cur.Title+'">'+cur.Title+'</a></h2><div class="result-snippet">'+hlId.Content[0]+'</div>';
                InkCss.addClassName(result,'show');
            }
        }
    }

    var curQS = UtilUrl.getQueryString();

    if(typeof(curQS.search) !== 'undefined' && curQS.search !== '') {
        var value = decodeURIComponent(curQS.search);
        //http://services.sapo.pt/RSS/Feed/site/ink?wt=json&indent=on&fl=Title,Url,RSSWorksId&q=grid%20space
        var url = 'http://services.sapo.pt/RSS/Feed/site/ink';
        new InkAjax(url, {
                    method: 'GET',
                    parameters: 'wt=json&indent=on&hl=true&fl=Title,Url,RSSWorksId&q='+encodeURIComponent(value),
                    cors: true,
                    onSuccess: function(obj) {
                        if(obj.responseText) {
                            var req = obj.responseText;
                            var json = UtilJson.parse(req);
                            processResponse(json);
                            Ink.log(json);
                        }
                    },
                    onFailure: function() {
                        Ink.warn('failed request');
                    }
                });
    }

    function fillSearchInput(selector){
      var qs = UtilUrl.getQueryString(UtilUrl.getUrl());
      if(qs !== '' && qs !== 'undefined'){
        var searchFields = Ink.ss(selector);
        if(qs.search){
          for(var i=0; i<searchFields.length; i++){
          if(searchFields[i].value === "") {
              searchFields[i].value = qs.search;
            }
          }
        }
      }
    }

    fillSearchInput('.search-field');

});
