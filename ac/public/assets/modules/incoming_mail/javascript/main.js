App.incoming_mail = {
  controllers : {},
  models      : {}
};

/**
 * Incoming mail client side behaviour
 */
App.incoming_mail.controllers.incoming_mail_admin = { 
  /**
   * Archive page bahaviour
   *
   */
  index       : function () {
    $(document).ready(function() {
      $('#only_active_toggler').change(function() {
        var toggle_form = $(this).parents('form');
        var url = toggle_form.attr('action');
        var checkbox = toggle_form.find('.input_checkbox:checked');
        
        url = App.extendUrl(url, {only_problematic : checkbox.length})
        window.location = url;
      });
    });
  },
  
  add_mailbox : function () {    
    $(document).ready(function() {
      App.incoming_mail.AddEditForm.init('mailbox_form');
    });
  },
  
  edit_mailbox : function () {    
    $(document).ready(function() {
      App.incoming_mail.AddEditForm.init('mailbox_form');
    });
  },
  
  view_mailbox : function () {
    $(document).ready(function() {
      $('#only_active_toggler').change(function() {
        var toggle_form = $(this).parents('form');
        var url = toggle_form.attr('action');
        var checkbox = toggle_form.find('.input_checkbox:checked');
        
        url = App.extendUrl(url, {only_problematic : checkbox.length})
        window.location = url;
      });
    });
  }
};

/**
* Incoming mail frontend
*/
App.incoming_mail.controllers.incoming_mail_frontend = {
  index: function () {    
    $(document).ready(function() {
      
       $('.incoming_mails_table').checkboxes();
      
        $('.incoming_mails_table .import_button').click(function() {
          return true;
          var anchor = $(this);
          var init_import_form = function() {
            $('#import_form .submit_button').click(function () {
              $('#import_form').ajaxSubmit({
                success : function(response) {
                  $('#import_mail.dialog div.body > p').html(response);
                  anchor.parent().parent().addClass('imported_sucessfully');
                },
                error : function(response) {
                  if (response.status == 409) {
                    $('#import_mail.dialog div.body > p').html(response.responseText);
                  } // if
                  init_import_form();
                }
              });
              $('#import_mail.dialog div.body > p').html('<img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Loading...'));
              return false;
            });
          };
          
          var dialog_url = App.extendUrl(anchor.attr('href'), { 
            skip_layout : 1, 
            async : 1 
          });
          
          App.ModalDialog.show('import_mail', App.lang('Import Email'), $('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Loading...') + '</p>').load(dialog_url, function() {
            init_import_form();
          }), {
            buttons : false,
            width : 978,
            height: 350
          });
          return false;
        });
    });
  }
};

App.incoming_mail.importPendingEmailForm = function() {
  var form;
  return {
    init : function(form_id) {
      form = $('#'+form_id);
      
      // set project select box behaviour
      $('#project_id', form).change(function () {
        // var ajax_request_url = App.data.additional_fields_url + $(this).val() + '&user_id=' + $('#user_id', form).val() + '&object_type=' + $('#object_type', form).val();

        var ajax_request_url = App.extendUrl(App.data.additional_fields_url, {
          'skip_layout' : 1,
          'async' : 1,
          'project_id' : $(this).val(),
          'user_id' : $('#user_id', form).val(),
          'object_type' : $('#object_type', form).val()
        });
        
        $('#additional_fields_loader', form).text(App.lang('Loading')).load(ajax_request_url, null, function () {
          // set object type selector behaviour
          $('#object_type', form).change(function () {
            if ($(this).val() == 'comment') {
              $('#parent_id_block', form).show()
            } else {
              $('#parent_id_block', form).hide()
            } // if
          });
          
          if ($('#object_type', form).val() != 'comment') {
            $('#parent_id_block', form).hide()
          } // if
        });
      });
      
      // set object type selector behaviour
      $('#object_type', form).change(function () {
        if ($(this).val() == 'comment') {
          $('#parent_id_block', form).show()
        } else {
          $('#parent_id_block', form).hide()
        } // if
      });
      
      if ($('#object_type', form).val() != 'comment') {
        $('#parent_id_block', form).hide()
      } // if
    }
  };
}();

App.incoming_mail.AddEditForm = function() {
  return {
    init : function(wrapper_id) {
      var mailbox_form = $('#' + wrapper_id);
      var result_container = $('#test_connection .test_connection_results', mailbox_form);
      var result_image = $('img:eq(0)', result_container);
      var result_output = $('span:eq(0)', result_container);
      
      $('#test_connection button').click(function () {
        result_output.text('');
        result_image.attr('src', App.data.indicator_url);
        
        mailbox_form.ajaxSubmit({ 
          success:    function(response) {
            result_output.text(response);
            result_image.attr('src', App.data.ok_indicator_url);
            result_container.removeClass('connection_error');
            result_container.addClass('connection_ok');
          },
          error:      function(response) {
            result_output.text(response.responseText);
            result_image.attr('src', App.data.error_indicator_url);
            result_container.removeClass('connection_ok');
            result_container.addClass('connection_error');
          },
          url: App.data.test_mailbox_connection_url
        });
      });
      
      // Change port value when type is change and port value is empty or 
      // uses default value of other type
      var type_change_handler = function() {
        var port_field = $('#mailboxPort');
        
        if($(this).val() == 'POP3') {
          if(port_field.val() == '' || port_field.val() == '143') {
            port_field.val('110').parent().highlightFade();
          } // if
        } else {
          if(port_field.val() == '' || port_field.val() == '110') {
            port_field.val('143').parent().highlightFade();
          } // if
        } // if
      };
      
      $('#mailboxType').click(type_change_handler).change(type_change_handler);
    }
    
  };
  
}();

$(document).ready(function() {  
  // check if there are problematic incoming mails
  var update_incoming_mail_menu_badge_item = function () {
    var count_url = App.data.path_info_through_query_string ? 
      App.extendUrl(App.data.url_base, { path_info : 'incoming-mail/count-pending' }) :
      App.data.url_base + '/incoming-mail/count-pending';
      
    var inbox_url = App.data.path_info_through_query_string ? 
      App.extendUrl(App.data.url_base, { path_info : 'incoming-mail' }) :
      App.data.url_base + '/incoming-mail';
      
    var group_id = 'main';
    var menu_item_id = 'incoming_mail';
    
    $.ajax({
      type: "GET",
      url: count_url,
      success : function (response) {
        if (response > 0) {
         if (!App.MainMenu.itemExists(menu_item_id, group_id)) {
      	   App.MainMenu.addToGroup({
      	     label       : App.lang('Inbox'),
      	     href        : inbox_url,
      	     icon        : App.data.assets_url + '/modules/incoming_mail/images/icon_menu.gif',
      	     id          : menu_item_id,
      	     badge_value : response
      	   }, group_id);
         } else {
           App.MainMenu.setItemBadgeValue(menu_item_id, group_id, response);
         }
        } else {
          App.MainMenu.removeButton(menu_item_id, group_id);
        } // if
      } // if
    })
  } // update_incoming_mail_menu_badge_item
  
  setInterval(update_incoming_mail_menu_badge_item, 60000 * 3); 
})

