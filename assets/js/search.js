Ink.requireModules(['Ink.Net.Ajax_1','Ink.Dom.Css_1','Ink.Dom.Element_1','Ink.Dom.Selector_1','Ink.Dom.Event_1','Ink.Util.Json_1','Ink.Util.Url_1'],function(ajax,css,elm,sel,ev,json,url){



  function addEvent(selector) {
    if(typeof(selector) === 'string') {
      var elm = Ink.ss(selector);
    } else {
      var elm = selector;
    }

    ev.observeMulti(elm, 'submit', function(event) {

      var tgt = ev.element(event);
      var val = tgt.value;
      if(val !== '') {
        doRequest(val);
      }

    });
  };

  function fillSearchInput(selector){
    var qs = url.getQueryString(url.getUrl());
    var searchFields = Ink.ss(selector);
    if(qs.search){
      for(var i=0; i<searchFields.length; i++){
      if(searchFields[i].value === "") {
          searchFields[i].value = qs.search;
        }
      }
    }
    
  }

  var doOnSuccess = function(json) {

    Ink.log(json);
    if(response.numFound === 0) {
      return;
    }
    var data = json.response.docs;

    var cur;
    for(var i=0, t=data.length; i < t; i++) {
      cur = data[i];
      Ink.log(cur);
    }

  }

  function doRequest(value) {
    var request = new ajax(
      'http://services.sapo.pt/RSS/Feed/site/ink',
      {
        method: 'get',
        paramenters: 'wt=json&indent=on&fl=Title,Url,RSSWorksId&q='+encodeURIComponent(value),
        onSuccess: function(req) {
          if(req.responseJSON) {
            var json = req.responseJSON;

            doOnSuccess(json);
          }
        },
        onFailure: function () {
          Ink.warn('failed')
        }
      }
      );
  }

  addEvent('.docsearch');
  fillSearchInput('.search-field');


});
