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
        var results = Ink.s('#search-results');
        if(json && json.response && json.response.numFound > 0) {
            var docs = json.response.docs;
            var cur, hlId, resultsList;
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
                  insertBottom: resultsList,
                  setHTML: '<h2><a href="'+cur.Url+'" title="'+cur.Title+'">'+cur.Title+'</a></h2><div class="result-snippet">'+hlId.Content[0]+'</div>'
                });
                InkCss.addClassName(result,'show');
            }
        } else {
            InkCss.addClassName(results,'show');
            var noResults = InkElement.create('p',{
                insertBottom: results,
                className: 'nope',
                setHTML: "We're sorry, but we could not find any matches for <strong>" + curQS.search + "</strong>."
            });
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
                    onCreate: function(){
                        handleLoader('show');
                    },
                    onSuccess: function(obj) {
                        if(obj.responseText) {
                            var req = obj.responseText;
                            var json = UtilJson.parse(req);
                            processResponse(json);
                            Ink.log(json);
                        }
                        handleLoader();
                    },
                    onFailure: function() {
                        var results = Ink.s('#search-results');
                        InkCss.addClassName(results,'show');
                        var noResults = InkElement.create('p',{
                            className: 'nope',
                            insertBottom: results,
                            setTextContent: "Oops, something went wrong!"
                        });
                    }
                });
    }

    function handleLoader(action) {
        var loader = Ink.s('.loader');
        if(action === 'show'){
            InkCss.addClassName(loader,'show');
        } else {
            InkCss.removeClassName(loader,'show');
        }
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
          Ink.s('.search-field.main').focus();
        }
      }
    }

    fillSearchInput('.search-field');

});
