/**
 * Walk through images in a wrapper and make sure that they are not wider than 
 * the wrapper itself
 */
jQuery.fn.scaleBigImages = function() {
  return this.each(function() {
    var wrapper = $(this);
    var wrapper_width = wrapper.width();
    
    wrapper.find('img').each(function() {
      var image = $(this);
      
      image.load(function () {
        var width = image.width();
        
        if(width > wrapper_width) {
          if (image.parents('a').length == 0) {
            var link = $('<a></a>')
              .attr('href', image.attr('src'))
              .attr('title', App.lang('Open Full Size (:widthx:heightpx)', { 'width' : image.width(), 'height' : image.height() }))
              .click(function() {
                window.open($(this).attr('href'), App.lang('Full Size Image'));
                return false;
              });
            
            image.after(link).appendTo(link);
          } // if
          
          var scale = wrapper_width / width;
          
          image.css('height', Math.round(image.height() * scale));
          image.css('width', wrapper_width);
        } // if
      });
    });
  });
};