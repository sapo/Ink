App.status = {
  controllers : {},
  models      : {}
};

/**
 * Status update client side behaviour
 */
App.status.controllers.status = { 
  
  /**
   * Index page bahaviour
   */   
  index : function () {
    $(document).ready(function() {
      $('#select_user').change(function() {
        $("#status_update_archive_work_indicator").show();
        window.location = $(this).val();
      });
    });
  }
};

/**
 * Update dialog behavior
 */
App.widgets.status_update_dialog = function() {
  return {
    init : function(item_id, value) {
      var status_update_form = $("#update_status_form");
      var status_update_button = $("#status_update_button");
      var status_update_indicator = $("#status_update_indicator");
      var status_update_table = $("#status_updates_table tbody");
      var status_update_table_wrapper = $("#status_updates_dialog .table_wrapper");
      var status_update_input = status_update_form.find('input[name=status]');
      
      status_update_input[0].focus();
      
      $('.status_update_top_links li a').hover(function () {
        $('li:eq(0)', $(this).parent().parent()).text($(this).attr('title'));
      }, function () {
        $('li:eq(0)', $(this).parent().parent()).text('');
      });
      
      status_update_button.click(function() {
        status_update_form.submit();
      });
      
      status_update_form.submit(function() {
        var message = jQuery.trim(status_update_input.val());
        
        if(message == '') {
          status_update_input.val('')[0].focus();
          return false;
        } // if
        
        status_update_button.hide();
        status_update_indicator.show();
        
        $.ajax({
          url  : App.extendUrl(status_update_form.attr('action'), { async : 1 }),
          type : 'POST',
          data : {
            'submitted' : 'submitted',
            'status[message]' : message
          },
          success : function(response) {
            status_update_table_wrapper.show();
            status_update_table_wrapper.scrollTo(0);
            status_update_table.prepend(response);
            var counter = 1;
            status_update_table.find('tr').each(function() {
              var new_class = counter % 2 ? 'odd' : 'even';
              $(this).removeClass('odd').removeClass('even').addClass(new_class);
              counter++;
            });
            
            status_update_table.find('tr:first td').highlightFade();
            
            status_update_button.show();
            status_update_indicator.hide();
            
            status_update_input.val('')[0].focus();
          },
          error : function(response) {
            status_update_button.show();
            status_update_indicator.hide();
            status_update_input.val('')[0].focus();
            
            alert(App.lang('We are sorry, but system failed to save your status message. Please try again later.'));
          }
        });
        
        return false;
      });
    }
  };
  
}();

$(document).ready(function() {
  
  // check if there are new messages
  var update_status_menu_badge_item = function () {
    var count_url = App.data.path_info_through_query_string ? 
      App.extendUrl(App.data.url_base, { path_info : 'status/count-new-messages' }) :
      App.data.url_base + '/status/count-new-messages';
    
    $.ajax({
      type: "GET",
      url: count_url,
      success : function (response) {
        App.MainMenu.setItemBadgeValue('status', 'main', response);
      }
    })
  } // update_status_menu_badge_item
  setInterval(update_status_menu_badge_item, 60000 * 3); 
  
  // init menu item button
  $('#menu_item_status a').click(function() {
    var status_update_url = App.extendUrl($(this).attr('href'), { 
      async : 1 
    });
    
    App.ModalDialog.show('status_updates', App.lang('Status Updates'), $('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Loading...') + '</p>').load(status_update_url), {
      buttons : false
    });
    App.MainMenu.setItemBadgeValue('status', 'main', 0);
    return false;
  });
})
