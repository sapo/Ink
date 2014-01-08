Ink.requireModules(['Ink.Net.Ajax_1','Ink.Dom.Css','Ink.Dom.Element','Ink.Dom.Selector','Ink.Dom.Event','Ink.Util.Json_1'],function(a,c,el,s,ev,j){

  var jsonIndex;

  var index = lunr(function () {
    this.field('content', 1);
    this.field('title', 50);
    this.field('tags', 100);
    this.ref('url');
  });

  function dateToTimestamp(date) {
    date=date.split("-");
    var newDate=date[1]+"/"+date[0]+"/"+date[2];
    var ts = new Date(newDate).getTime();
    return ts.toString();
  }

  var request = new a('/search.json',{method: 'GET',onSuccess: function(response){

    jsonIndex  = response.responseJSON[1];
    
    for (var i = 0; i < response.responseJSON[1].length; i++){        
      index.add(response.responseJSON[1][i]);
    }

    var indexJson = j.stringify(index.toJSON());
  }});

  var searchElm = Ink.s('#search');
  var resultList = Ink.s('#resultsList');

  ev.observe(searchElm,'keyup',function(evElm){

      resultList.innerHTML = '';

      if(searchElm.value.length > 3){

        var results = index.search(searchElm.value.toString()).map(function(result) {

            var searchField = "url";
            var searchVal = result.ref;

            for (var i=0 ; i < jsonIndex.length ; i++)
            {
                if (jsonIndex[i][searchField] == searchVal) {
                    var resultUrl = jsonIndex[i].url;
                    var resultTitle = jsonIndex[i].title;                          
                    var resultItem = el.create('li');
                    var resultLink = el.create('a',{'href':resultUrl});
                    resultLink.innerHTML = resultTitle;
                    resultItem.appendChild(resultLink);
                    resultList.appendChild(resultItem);
                }
            }
        });            
        resultList.style.display = 'block';
      } else {
        resultList.style.display = 'block';
      }
  });

});