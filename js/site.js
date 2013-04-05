SAPO.Dom.Loaded.run(function(){

    var progressBarInterval = setInterval( function(){
        SAPO.Ink.siteInkInstances['ProgressBar'].forEach(function(item){
            item.setValue( ( ( Math.random()*100 ) + 1 ) );
        });
    },3000);

});