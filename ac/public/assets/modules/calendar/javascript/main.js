/**
 * Remember focused calendar cell ID
 */
jQuery.fn.calendarCurrentCellId = null;

/**
 * Focus single calendar cell
 */
jQuery.fn.calendarFocusCell = function() {
  var cell = $(this);
  var link = cell.find('div.day_num a');
  var details = cell.find('div.day_details');
  
  cell.addClass('zoomed');
          
  cell.find('div.day_brief').hide();
  
  if(details.children().length) {
    details.show();
  } else {
    details.show();
    details.append('<img src="' + App.data.assets_url + '/images/indicator.gif" class="indicator" alt="Working..." />');
    details.load(App.extendUrl(link.attr('href'), { skip_layout : 1 }), function() {
      details.find('a').click(function(e) {
        e.stopPropagation();
        return true;
      });
    });
  } // if
};

/**
 * Unfocus calendar cell
 */
jQuery.fn.calendarUnfocusCell = function() {
  var cell = $(this);
  
  cell.removeClass('collapsed').removeClass('zoomed');
  
  cell.find('div.day_brief').show();
  cell.find('div.day_details').hide();
};

/**
 * Attach calendar behavior to DOM
 */
$(document).ready(function() {
  $('#calendar td.day_cell').click(function() {
    var cell = $(this);
    var table = cell.parent().parent().parent();
    
    if(jQuery.fn.calendarCurrentCellId) {
      if(cell.attr('id') == jQuery.fn.calendarCurrentCellId) {
        table.find('td.day_cell').removeClass('collapsed').removeClass('zoomed');
        cell.calendarUnfocusCell();
        
        jQuery.fn.calendarCurrentCellId = null;
      } else {
        $('#' + jQuery.fn.calendarCurrentCellId).calendarUnfocusCell();
        table.find('td.day_cell').addClass('collapsed').removeClass('zoomed');
        cell.calendarFocusCell();
        
        jQuery.fn.calendarCurrentCellId = cell.attr('id');
      }
    } else {
      table.find('td.day_cell').addClass('collapsed').removeClass('zoomed');
      cell.calendarFocusCell();
      
      jQuery.fn.calendarCurrentCellId = cell.attr('id');
    } // if
    
    return false;
  });
  
  $('#calendar td.day_cell a').click(function(e) {
    e.stopPropagation();
    location.href = $(this).attr('href');
  });
});