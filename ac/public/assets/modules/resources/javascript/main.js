App.resources = {
  controllers : {},
  models      : {}
};

/**
 * Assignments controller behavior
 */
App.resources.controllers.assignments = {
  
  /**
   * Assignments index page
   */
  index : function() {
    $(document).ready(function() {
      $('#assignments_filter_select select').change(function() {
        var filter_url = $(this).val();
        if(filter_url != location.href) {
          $(this).after(' ' + App.lang('Loading ...')).attr('disabled', 'disabled');
          location.href = filter_url;
        } // if
      });
      
      $('#assignments_filter_options a').hover(function() {
        $('#assignments_filter_options span.tooltip').text($(this).attr('title'));
      }, function() {
        $('#assignments_filter_options span.tooltip').text('');
      }); 
      
      $('#toggle_filter_details').click(function() {
        $('#assignments_filter_details').toggle('fast');
        return false;
      });
      
      // Complete / Move to trash tasks
      $('#assignments a.complete_assignment, #assignments a.remove_assignment').click(function() {
        var link = $(this);
        
        // Block additional clicks
        if(link[0].block_clicks) {
          return false;
        } else {
          link[0].block_clicks = true;
        } // if
        
        var img = link.find('img');
        var old_src = img.attr('src');
        
        img.attr('src', App.data.indicator_url);
        
        $.ajax({
          url     : link.attr('href'),
          type    : 'POST',
          data    : {'submitted' : 'submitted'},
          success : function(response) {
            link.parent().parent().remove();
            
            var counter = 1;
            $('#assignments tr.assignment_row').each(function() {
              var new_class = counter % 2 == 1 ? 'odd' : 'even';
              $(this).removeClass('even').removeClass('odd').addClass(new_class);
              counter++;
            });
          },
          error   : function() {
            img.attr('src', old_src);
            link[0].block_clicks = false;
          }
        });
        
        return false;
      });
    });
  }
  
}

/**
 * Select assignees widget
 */
App.widgets.SelectAssignees = function() {
  
  /**
   * Update value of hidden owner_id field based on selected user
   *
   * @param string widget_id
   * @param string control_name
   * @return undefined
   */
  var update_responsible_user = function(widget_id, control_name) {
    var widget = $('#' + widget_id);
    var users_list = widget.find('div.select_assignees_widget_users ul.users_list');
    
    if(users_list.length == 0) {
      widget.find('input.responsible_assignee_id').remove();
    } else {
      var responsible_user = users_list.find('li.responsible');
      var responsible_user_id = 0;
      
      if(responsible_user.length == 0) {
        var responsible_user = users_list.find('li:first');
        responsible_user.addClass('responsible');
        responsible_user_id = responsible_user.attr('user_id');
      } else {
        var responsible_user_id = responsible_user.attr('user_id');
      } // if
      
      var hidden_field = widget.find('input.responsible_assignee_id');
      if(hidden_field.length > 0) {
        hidden_field.attr('value', responsible_user_id);
      } else {
        hidden_field = users_list.after('<input type="hidden" name="' + control_name + '[1]" value="' + responsible_user_id + '" class="responsible_assignee_id" />');
      } // if
    } // if
  };
  
  /**
   * Initialize behavior of user list item in an user list
   *
   * @param string widget_id
   * @param string control_name
   * @return undefined
   */
  var init_user_list_items = function(widget_id, control_name) {
    var users_list = $('#' + widget_id + ' div.select_assignees_widget_users ul.users_list');
    var list_items = users_list.find('li');
    
    list_items.hover(function() {
      $(this).attr('title', App.lang('Set as responsible')).addClass('hovered');
    }, function() {
      $(this).attr('title', '').removeClass('hovered');
    }).click(function() {
      list_items.removeClass('responsible');
      $(this).addClass('responsible');
      update_responsible_user(widget_id, control_name);
    });
  };
  
  // Public interface
  return {
    
    /**
     * Initialize select users widget
     *
     * @param string widget_id
     * @param string control_name
     * @param string company_id
     * @param string project_id
     * @param Array exclude_ids
     * @return void
     */
    init : function(widget_id, control_name, company_id, project_id, exclude_ids) {
      var widget = $('#' + widget_id);
      
      widget.find('.assignees_button').select_multiple_users({
        'widget_id'    : widget_id,
        'company_id'   : company_id,
        'project_id'   : project_id,
        'selected_ids' : function() {
          var selected_ids = [];
          $('#' + widget_id + ' div.select_assignees_widget_users input[type=hidden]').each(function() {
            selected_ids.push($(this).attr('value'));
          });
          return selected_ids;
        },
        'exclude_ids'  : exclude_ids,
        'on_ok'        : function(selected_users) {
          var responsible_user_id = widget.find('input.responsible_assignee_id').val();
          
          widget.find('div.select_assignees_widget_users').empty();
          
          if(selected_users.length > 0) {
            for(var i = 0; i < selected_users.length; i++) {
              App.widgets.SelectAssignees.add_user(widget_id, control_name, selected_users[i]['id'], selected_users[i]['name'], responsible_user_id == selected_users[i]['id']);
            } // for
            
            // Done adding users
            update_responsible_user(widget_id, control_name);
            init_user_list_items(widget_id, control_name);
          } else {
            widget.find('div.select_assignees_widget_users').append('<p class="details">' + App.lang('No users selected') + '</p>');
          } // if
        } // function
      });
      
      return false;
    },
    
    /**
     * Add new user to the list
     *
     * @param integer widget_id
     * @param string control_name
     * @param integer user_id
     * @param string display_name
     * @param boolean is_owner
     * @return void
     */
    add_user : function(widget_id, control_name, user_id, display_name, is_owner) {
      var widget = $('#' + widget_id);
      var users_list = widget.find('div.select_assignees_widget_users ul.users_list');
      
      if(users_list.length == 0) {
        widget.find('div.select_assignees_widget_users').empty();
        users_list = $('<ul class="users_list"</ul>');
        widget.find('div.select_assignees_widget_users').append(users_list);
      } // if
      
      var list_item_class = is_owner ? 'responsible' : 'not_responsible'; 
      
      users_list.append('<li class="' + list_item_class + '" user_id="' + user_id + '">' + App.clean(display_name) + '</li>');
      users_list.after('<input type="hidden" name="' + control_name + '[0][]" value="' + user_id + '" />');
    },
    
    /**
     * When we are done adding users we should call this method to initialize 
     * widget behaviro
     *
     * @param string widget_id
     * @param string control_name
     * @return undefined
     */
    done_adding_users : function(widget_id, control_name) {
      update_responsible_user(widget_id, control_name);
      init_user_list_items(widget_id, control_name);
    }
    
  };
  
}();

/**
 * Select assignees widget
 */
App.widgets.SelectAsigneesInlineWidget = function () {
  /**
   * Wrapper that containts this widget
   *
   * @var jQuery
   */
  var wrapper;
  
  /**
   * Hidden field that contains value of responsible person
   *
   * @var jQuery
   */
  var responsible_field;
  
  /**
   * Block that contains information about currently responsible person
   *
   * @var jQuery
   */
  var responsible_information_block;
  
  /**
   * Enable responsible person
   *
   * @var boolean
   */
  var enable_responsible;
  
  /**
   * Sets current checkbox to be responsible one
   * 
   * @param jQuery checkbox
   * @return null
   */
  var set_as_responsible = function (checkbox) {
    var responsible_span = checkbox.next();
    if (!checkbox.attr('checked')) {
      checkbox.attr('checked','checked');
      checkbox.change();
    } //if
    wrapper.find('span.responsible_setter.responsible').removeClass('responsible');
    responsible_span.addClass('responsible');
    responsible_field.val(checkbox.val());
    responsible_information_block.html(App.lang('<strong>:user</strong> is responsible', {
      'user' : responsible_span.html()
    }));
    responsible_information_block.find('strong').highlightFade();
  };
  
  /**
   * Sets current checkbox as not responsible
   * 
   * @param jQuery checkbox
   * @return null
   */
  var set_as_not_responsible = function (checkbox) {
    var responsible_span = checkbox.next();
    responsible_span.removeClass('responsible');
    
    var first_checked = wrapper.find('.company_user input:checked:first');
    if (first_checked.length < 1) {
      responsible_field.val('');
      responsible_information_block.html(App.lang('No one is responsible'));
    } else {
      set_as_responsible(first_checked);
    } // if
  };
  
  return {
    /**
     * Function that handles all widget behaviour
     *
     * @param string wrapper_id
     * @param boolean wrapper_id
     */
    init  : function (wrapper_obj, wrapper_id, choose_responsible, responsible_id) {
      wrapper = wrapper_obj;
      enable_responsible = choose_responsible;
      if (enable_responsible) {
        responsible_field = wrapper.find('#'+wrapper_id+'_responsible');
        responsible_information_block = wrapper.find('.select_asignees_inline_widget_responsible_block .placeholder');
      } // if
           
      // handle company checkboxes behaviour
      wrapper.find('.company_name input').click(function () {
        var company_checkbox = $(this);
        var company_group = company_checkbox.parents('.user_group:first');
        
        // set all user checkbox states to current company checkbox state
        company_group.find('.company_user input').attr('checked', company_checkbox.attr('checked'));
        
        if (enable_responsible) {
          if (!company_checkbox.attr('checked')) {
            // checks if there is responsible in group and if yes make it not responsible
            var group_responsible = company_group.find('.company_user input').next('.responsible');
            if (group_responsible.length > 0) {
              set_as_not_responsible(group_responsible.prev());
            } // if
          } else {
            // check if there is a responsible person and if not, it uses first in the group as responsible
            if (wrapper.find('.company_user input').next('.responsible').length == 0) {
              set_as_responsible(company_group.find('.company_user input:first'));
            } // if
          } // if
          
        } // if
        
        company_checkbox.blur();
      });
      
      // handle user checkboxes behaviour
      wrapper.find('.company_user input').change(function () {
        var company_user = $(this);
        var responsible_span = company_user.next();
        
        var company_checkbox = company_user.parents('.user_group:first').find('.company_name input:first');
        
        var total_group_checkboxes_count = company_user.parents('.user_group:first').find('.company_user input').length;
        var checked_group_checkboxes_count = company_user.parents('.user_group:first').find('.company_user input:checked').length;
        
        // if all checkboxes in group are checked, we need to check company checkbox, otherwise we need to uncheck it
        if (total_group_checkboxes_count == checked_group_checkboxes_count) {
          company_checkbox.attr('checked', 'checked');
        } else {
          company_checkbox.attr('checked', '');
        } // if
        
        if (enable_responsible) {
          // if uncheck responsible person it becomes not responsible
          if (!company_user.attr('checked') && responsible_span.is('.responsible')) {
            set_as_not_responsible(company_user);
          } // if
          
          // if you check assignee and, he is only assignee it automatically becomes responsible one
          if (company_user.attr('checked') && (wrapper.find('.company_user input:checked').length == 1)) {
            set_as_responsible(company_user);
          } // if
        } // if
        
        company_user.blur();
      });
      
      if (enable_responsible) {
        // if enable_responsible is true we need to setup responsible person chooser
        wrapper.find('.responsible_setter').hover(function () {
          $(this).css('text-decoration', 'underline');
        }, function () {
          $(this).css('text-decoration', 'none');
        }).click(function () {
          var this_span = $(this);
          var this_checkbox = this_span.prev();
          set_as_responsible(this_checkbox);
        });
      } else {
        // otherwise we need to simulate label clicking
        wrapper.find('.responsible_setter').click(function () {
          var this_span = $(this);
          var this_checkbox = this_span.prev();
          if (this_checkbox.attr('checked')==false) {
            this_checkbox.attr('checked','checked');
          } else {
            this_checkbox.attr('checked','');
          } // if
          this_checkbox.change();
        });
      } // if
      
      // initial setup
      wrapper.find('.user_group').each(function () {
        $(this).find('.company_user input:first').change();
      });
      
      if (enable_responsible && responsible_id) {
        var init_responsible = wrapper.find('#' + wrapper_id + '_user_' + responsible_id + ':first');
        if (init_responsible.length > 0) {
          set_as_responsible(init_responsible);
        } // if
      } // if
      //$_select_assignees_responsible
    }
  }
}();

/**
 * Send reminder behavior
 */
App.widgets.SendReminder = function() {
  
  /**
   * Public interface
   */
  return {
    
    /**
     * Initialize send reminder link if present
     */
    init : function() {
      $('li.send_reminder a').click(function() {
        var link = $(this);
        
        var reminder_url = App.extendUrl(link.attr('href'), { 
          skip_layout : 1 
        });
        
        App.ModalDialog.show(
          'send_reminder_users', // name
          App.lang('Remind'),  // caption
          $('<p><img src="' + App.data.indicator_url + '" alt="" /> ' + App.lang('Loading...') + '</p>').load(reminder_url, function() {
            var form = $('#send_reminder_users_form');
            form.submit(function() {
              form.attr('action', form.attr('action') + '&skip_layout=1');
              form.block(App.lang('Working...'));
              form.ajaxSubmit({
                success : function(response) {
                  form.after(response);
                  form.remove();
                }
              });
              return false;
            });
          }), // body
          {
            buttons : false,
            width: 500
          }
        );
        return false;
      });
    },
    
    /**
     * Initialize form behavior
     */
    init_form : function() {
      var form = $('#send_reminder_users_form');
            
      /**
       * Change details in who radio group
       */
      var change_who = function() {
        form.find('div.content_wrapper').hide();
        form.find('div.label_wrapper input:checked').each(function() {
          $(this).parent().parent().find('div.content_wrapper').show();
        });
      };
      
      form.find('div.label_wrapper input').click(change_who);
      change_who();
    }
    
  };
  
}();

/**
 * Add / Edit assignments filter form behavior
 */
App.resources.AssignmentFilterForm = function() {
  
  /**
   * Form instance
   *
   * @var jQuery
   */
  var form;
  
  // Public interface
  return {
    
    /**
     * Initialize assignment filter form
     *
     * @param string form_id
     * @param string partial_generator_url
     * @return void
     */
    init : function(form_id, partial_generator_url) {
      form = $('#' + form_id);
      
      form.find('select.filter_async_select').change(function() {
        var select = $(this);
        var row = select.parent().parent();
        var cell_additional = row.find('td.filter_select_additional');
        var option = select.find('option[value=' + select.val() + ']');
        
        if(option.attr('class') == 'filter_async_option') {
          cell_additional.each(function() {
            cell_additional.empty().append('<img src="' +  App.data.indicator_url + '" alt="" />');
            $.ajax({
              url     : partial_generator_url,
              type    : 'GET',
              data    : {
                'select_box'   : select.attr('name'),
                'option_value' : option.val()
              },
              success : function(response) {
                cell_additional.empty();
                cell_additional.append(response);
              },
              error   : function() {
                cell_additional.empty();
              }
            });
          })
        } else {
          cell_additional.empty();
        }
      })
    }
    
  };
  
}();

/**
 * Object assignments form behavior
 */
App.resources.ObjectAttachments = function() {
  
  // Public interface
  return {
    
    /**
     * Initialize behavior
     *
     * @param string wrapper_id
     */
    init : function(wrapper_id) {
      var wrapper = $('#' + wrapper_id);
      
      /**
       * File details
       */
      wrapper.find('a.show_file_details').click(function() {
        var brief = wrapper.find('div.brief_files_view');
        var full = wrapper.find('div.full_files_view')
        
        if(brief.css('display') == 'none') {
          full.css('display', 'none');
          brief.css('display', 'block');
          
          $(this).text(App.lang('Show Details'));
        } else {
          brief.css('display', 'none');
          full.css('display', 'block');
          
          $(this).text(App.lang('Hide Details'));
        }
        
        return false;
      });
      
      /**
       * Reindex table rows
       */
      var reindex_odd_even_rows = function() {
        var counter = 1;
        wrapper.find('tr').each(function() {
          var row = $(this);
          row.removeClass('even').removeClass('odd');
          if(counter % 2 == 1) {
            row.addClass('odd');
          } else {
            row.addClass('even');
          } // if
          counter++;
        });
      };
      
      /**
       * Prepare details row
       */
      var prepare_details_row = function(row) {
        row.find('td.options a').click(function() {
          var link = $(this);
          
          if (!confirm(App.lang('Are you sure that you want to permanently remove this attachment? There is no Undo!'))) {
            return false;
          } // if
          
          // Block additional clicks
          if(link[0].block_clicks) {
            return false;
          } else {
            link[0].block_clicks = true;
          } // if
          
          var img = link.find('img');
          var old_src = img.attr('src');
          
          img.attr('src', App.data.indicator_url);
          
          $.ajax({
            url     : App.extendUrl(link.attr('href'), {async: 1}),
            type    : 'POST',
            data    : {'submitted' : 'submitted'},
            success : function() {
              var row = link.parent().parent();
              wrapper.find('li.attachment_' + row.attr('attachment_id')).remove(); // remove from brief view
              row.remove(); // remove from detail
              
              reindex_odd_even_rows();
              if(wrapper.find('table tr').length < 1) {
                wrapper.find('div.body').append('<p class="details center files_moved_to_trash">' + App.lang('All files moved to Trash') + '</p>');
              } // if
            },
            error   : function() {
              img.attr('src', old_src);
            }
          });
          
          return false;
        });
      };
      
      // Initialize rows
      wrapper.find('div.full_files_view table tr').each(function() {
        prepare_details_row($(this));
      });
      
      // Prepare form behavior
      var form_wrapper = wrapper.find('div.attach_another_file');
      var form = form_wrapper.find('form');
      
      form.submit(function() {
        var initial_action = form.attr('action');
        
        // Alter form action
        form.attr('action', App.extendUrl(initial_action, { async : 1 }));
        
        var list = wrapper.find('div.brief_files_view ul');
        var details = wrapper.find('div.full_files_view table tbody');
        
        if(UniForm.is_valid(form)) {
          form_wrapper.block(App.lang('Working...'));
          form.ajaxSubmit({
            success : function(response) {
              // Lets get rid of the notification if we already removed all 
              // attachments
              wrapper.find('p.files_moved_to_trash').remove();
              
              // jQuery acts a bit weird here. Insted of providing response as 
              // a string it tries to append it to the BODY so some markup 
              // (tr, td) gets discarded. That is why we need to use temp 
              // table in order to get properly marked-up row
              var tmp_table = $(response);
              var row = tmp_table.find('tr');
              
              details.append(row);
              tmp_table.remove();
              
              var row_class = details.find('tr').length % 2  == 1 ? 'odd' : 'even';
              
              row.addClass(row_class);
              prepare_details_row(row);
              row.find('td').highlightFade({
                complete : function() {
                  $(this).attr('style', '');
                }
              });
              
              // Now, lets create brief list entry (extract data from row)
              var brief = $('<li class="attachment_' + row.attr('attachment_id') + '"></li>');
              
              row.find('a.filename').clone().appendTo(brief);
              brief.append(' <span class="details">(' + row.find('span.filesize').text() + ')</span>');
              list.append(brief);
              brief.highlightFade();
              
              // Unblock and reset
              form_wrapper.unblock();
              form[0].reset();
            },
            error : function() {
              form_wrapper.unblock();
              form[0].reset();
            }
          });
        } // if
        
        // Revert back to initial action
        form.attr('action', initial_action);
        return false;
      });
      
      /**
       * Attach another file
       */
      wrapper.find('a.attach_another_file').click(function() {
        var link = $(this);
        
        // Toggle section options
        if(form_wrapper.css('display') == 'block') {
          form_wrapper.css('display', 'none');
          link.text(App.lang('Attach Another File'));
        } else {
          form_wrapper.css('display', 'block');
          link.text(App.lang('Done Adding Files'));
        } // if
        
        return false;
      });
    }
  };
}();

/**
 * Attach files widget behavior
 */
App.resources.AttachFiles = function() {
  
  // Public interface
  return {
    
    /**
     * Initialize widget behavior
     *
     * @param String wrapper_id
     */
    init : function(wrapper_id) {
      var wrapper = $('#' + wrapper_id);
      var name_counter = 1;
      
      var inputs_wrapper = $('<div class="attach_files_inputs"></div>').prependTo(wrapper);
      var max_files = parseInt(wrapper.attr('max_files'));
      
      if(max_files > 1) {
        var attach_another_wrapper = $('<p class="attach_files_add_another"></p>');
        inputs_wrapper.after(attach_another_wrapper);
        $('<div class="clear"></div>').insertAfter(attach_another_wrapper);
        
        var attach_another = $('<a href="#" class="button_add">' + App.lang('Attach Another File') + '</a>').appendTo(attach_another_wrapper).click(function() {
          add_input();
          show_hide_attach_another();
          return false;
        });
      } // if
      
      /**
       * Add single file input to the list of inputs
       */
      var add_input = function() {
        var input_wrapper = $('<div class="attach_files_input"></div>');
        var input = $('<input type="file" name="attach_from_files_file_' + name_counter +'" />').appendTo(input_wrapper);
        name_counter++;
        if(max_files > 1) {
          var remove_link = $('<a href="#"><img src="' + App.data.assets_url + '/images/dismiss.gif" /></a>').appendTo(input_wrapper).click(function() {
            if(inputs_wrapper.find('div.attach_files_input').length > 1) {
              input_wrapper.remove();
              show_hide_attach_another();
            } // if
            return false;
          });
        } // if
        
        inputs_wrapper.append(input_wrapper); // and add...
      };
      
      var show_hide_attach_another = function() {
        if(inputs_wrapper.find('div.attach_files_input').length >= max_files) {
          attach_another_wrapper.hide();
        } else {
          attach_another_wrapper.show();
        } // if
      };
      
      add_input(); // add input
    }
    
  };
  
}();

/**
 * Manage categories behavior
 */
App.resources.ManageCategories = function() {
  
  /**
   * Manage categories tab used to initialize the popup
   *
   * This value is present only on pages where we have categories tabs
   *
   * @var jQuery
   */
  var manage_categories_tab = false;
  
  /**
   * Initialize single categories table row
   *
   * @var jQuery
   */
  var init_row = function(row) {
    var table = row.parent().parent();
    
    // Rename category
    row.find('td.options a.rename_category').click(function() {
      var link = $(this);
      
      // Block additional clicks
      if(link[0].block_clicks) {
        return false;
      } // if
      
      var row = link.parent().parent().addClass('renaming');
      var name_cell = row.find('td.name');
      var name_link = name_cell.find('a');
      
      // Remember start name and start URL
      var start_name = name_link.text();
      var start_url = name_link.attr('href');
      
      link[0].block_clicks = true;
      
      name_cell.empty();
      
      var input = $('<input type="text" />').val(start_name).appendTo(name_cell);
      var save_button = $('<button class="simple">' + App.lang('Save') + '</button>').appendTo(name_cell);
      
      input[0].focus();
      
      // Submission indicator
      var submitting_changes = false;
      
      /**
       * Do submit changes we made
       */
      var submit_changes = function() {
        if(submitting_changes) {
          return;
        } // if
        
        var new_category_name = jQuery.trim(input.val());
        if(new_category_name == '') {
          input[0].focus();
        } // if
        
        // Check if new category name is already in use
        var name_used = false;
        table.find('td.name').each(function() {
          var current_row = $(this);
          if(current_row.attr('class').indexOf('renaming') == -1 && current_row.text() == new_category_name) {
            name_used = true;
            current_row.highlightFade();
          } // if
        });
        
        if(name_used) {
          return;
        } // if
        
        // And submit the request
        save_button.text(App.lang('Saving ...'));
        input.attr('disabled', 'disabled');
        submitting_changes = true;
        
        $.ajax({
          type : 'POST',
          url : App.extendUrl(link.attr('href'), { async : 1 }),
          data : {
            'submitted' : 'submitted',
            'category[name]' : new_category_name
          },
          success : function(response) {
            if(manage_categories_tab) {
              var category_id = row.attr('category_id');
              manage_categories_tab.parent().find('li').each(function() {
                if($(this).attr('category_id') == category_id) {
                  $(this).find('a span').text(response);
                } // if
              });
            } // if
            
            name_cell.empty().append($('<a></a>').attr('href', start_url).text(response));
            row.find('td').highlightFade();
            submitting_changes = false;
          },
          error : function() {
            name_cell.empty().append($('<a></a>').attr('href', start_url).text(start_name));
            submitting_changes = false;
            
            alert(App.lang('Failed to rename selected category'));
          }
        });
        
        link[0].block_clicks = false;
      };
      
      /**
       * Cancel changes
       */
      var cancel_changes = function() {
        name_cell.empty().append($('<a></a>').attr('href', start_url).text(start_name));
        link[0].block_clicks = false;
      };
      
      // Input key handling
      input.keydown(function(e) {
        //e.stopPropagation(); // Don't close dialog!
      }).keypress(function(e) {
        switch(e.keyCode) {
          case 13:
            submit_changes();
            break;
          case 27:
            cancel_changes();
            break;
          default:
            return true;
        } // if
        
        e.stopPropagation();
        return false;
      });
      
      // Button click 
      save_button.click(function() {
        submit_changes();
      });
      
      return false;
    });
    
    // Delete category implementation
    row.find('td.options a.move_category_to_trash').click(function() {
      var link = $(this);
      
      // Block additional clicks
      if(link[0].block_clicks) {
        return false;
      } // if
      
      if(confirm(App.lang('Are you sure that you want to delete this category? Objects that are currently within it will be marked as uncategorized, but will not be deleted. There is no Undo!'))) {
        link[0].block_clicks = true;
        
        var row = link.parent().parent();
        var img = link.find('img');
        var old_src = img.attr('src');
        
        img.attr('src', App.data.indicator_url);
        
        $.ajax({
          url     : App.extendUrl(link.attr('href'), { async : 1 }),
          type    : 'POST',
          data    : {'submitted' : 'submitted'},
          success : function(response) {
            if(manage_categories_tab) {
              var category_id = row.attr('category_id');
              manage_categories_tab.parent().find('li').each(function() {
                if($(this).attr('category_id') == category_id) {
                  $(this).remove();
                } // if
              });
            } // if
            
            row.remove();
            if(table.find('tr').length > 0) {
              reindex_even_odd_rows(table);
            } else {
              table.hide();
              $('#manage_categories_empty_list').show();
            } // if
          },
          error   : function() {
            img.attr('src', old_src);
          }
        });
      } // if
      
      return false;
    });
  };
  
  /**
   * Reindex table even odd rows
   *
   * @param jQuery wrapper
   */
  var reindex_even_odd_rows = function(table) {
    var counter = 1;
    table.find('tr').each(function() {
      var new_class = counter % 2 ? 'odd' : 'even';
      $(this).removeClass('odd').removeClass('even').addClass(new_class);
      counter++;
    });
  }
  
  // Public interface
  return {
    
    /**
     * Initialize manage categories popup
     *
     * @param String list_item_id
     */
    init : function(list_item_id) {
      manage_categories_tab = $('#' + list_item_id); // Remember manage categories tab!
      
      var link = manage_categories_tab.find('a');
      
      link.click(function() {
        var open_url = App.extendUrl(link.attr('href'), {
          skip_layout : 1,
          async : 1
        });
        
        App.ModalDialog.show('manage_categories_popup', App.lang('Manage Categories'), $('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Loading...') + '</p>').load(open_url), {});
        return false;
      });
    },
    
    /**
     * Initialize categories list behavior
     *
     * @param String wrapper_id
     */
    init_page : function(wrapper_id) {
      var wrapper = $('#' + wrapper_id);
      
      // New category implementation
      var form = wrapper.find('form');
      var new_category_input = form.find('input');
      var new_category_icon = form.find('img');
      
      var default_text = App.lang('New Category...');
      new_category_input.focus(function() {
        if(new_category_input.val() == default_text) {
          new_category_input.val('');
        } // if
      }).blur(function() {
        if(new_category_input.val() == '') {
          new_category_input.val(default_text);
        } // if
      }).val(default_text);
      
      // Submitting form indicator
      var submitting_new_category = false;
      
      // Click on + image
      new_category_icon.click(function() {
        if(!submitting_new_category) {
          form.submit();
        } // if
      });
      
      // Create new category...
      form.submit(function() {
        if(submitting_new_category) {
          return false;
        } // if
        
        var new_category_name = jQuery.trim(new_category_input.val());
        if(new_category_name == '') {
          new_category_input[0].focus();
        } // if
        
        // Check if new category name is already in use
        var name_used = false;
        wrapper.find('td.name').each(function() {
          var current_row = $(this);
          if(current_row.text() == new_category_name) {
            name_used = true;
            current_row.highlightFade();
          } // if
        });
        
        if(name_used) {
          return false;
        } // if
        
        var old_icon_url = new_category_icon.attr('src');
        
        submitting_new_category = true;
        new_category_input.attr('disabled', 'disabled');
        new_category_icon.attr('src', App.data.indicator_url);
        
        $.ajax({
          type : 'POST',
          url : App.extendUrl(form.attr('action'), { async : 1}),
          data : {
            'submitted' : 'submitted',
            'category[name]' : new_category_name
          },
          success : function(response) {
            $('#manage_categories_empty_list').hide();
            
            var new_row = $(response);
            var table = wrapper.find('table');
            
            table.append(new_row).show();
            init_row(new_row);
            reindex_even_odd_rows(table);
            
            new_row.find('td').highlightFade();
            
            new_category_input.attr('disabled', '').val('')[0].focus();
            submitting_new_category = false;
            new_category_icon.attr('src', old_icon_url);
            
            // Add to list of category tabs
            if(manage_categories_tab) {
              var new_tab = $('<li><a><span></span></a></li>');
              
              new_tab.attr('category_id', new_row.attr('category_id'));
              
              var new_category_link = new_row.find('td.name a');
              new_tab.find('a').attr('href', new_category_link.attr('href'));
              new_tab.find('span').text(new_category_link.text());
              
              manage_categories_tab.before(new_tab);
            } // if
          },
          error : function() {
            submitting_new_category = false;
            new_category_input.attr('disabled', '');
            new_category_icon.attr('src', old_icon_url);
            
            alert(App.lang('Failed to create new category ":name"', { 'name' :  new_category_name}));
          }
        });
        
        return false;
      });
      
      wrapper.find('table tr').each(function() {
        init_row($(this));
      }); 
    }
    
  };
  
}();

/**
 * Manage subscriptions popup and section behavior
 */
App.resources.ManageSubscriptions = function() {
  
  /**
   * Wrapper block
   *
   * @var jQuery
   */
  var wrapper;
  
  // Public interface
  return {
    
    /**
     * Initialize block behavior
     *
     * @param String wrapper_id
     * @param String object_type
     */
    init : function(wrapper_id, object_type) {
      wrapper = $('#' + wrapper_id);
      
      $('#manage_subscriptions_page_action a, #' + wrapper_id+ ' a.open_manage_subscriptions').click(function() {
        if(wrapper.css('display') != 'block') {
          wrapper.show();
        } // if
        
        var popup_url = wrapper.attr('popup_url');
        
        App.ModalDialog.show('manage_object_subscriptions_popup', App.lang('Manage Subscriptions'), $('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Loading...') + '</p>').load(popup_url, function() {
          $('#object_subscriptions table td.subscription input[type=checkbox]').click(function() {
            var checkbox = $(this);
            var cell = checkbox.parent();
            
            checkbox.hide();
            cell.append('<img src="' + App.data.indicator_url + '" alt="" />');
            
            if(this.checked) {
              var url = checkbox.attr('subscribe_url');
            } else {
              var url = checkbox.attr('unsubscribe_url');
            } // if
            
            $.ajax({
              url  : App.extendUrl(url, { async : 1 }),
              type : 'POST',
              data : {
                submitted : 'submitted'
              },
              success : function() {
                cell.find('img').remove();
                checkbox.show();
                
                // Lets update list of users
                var subscribed_users = [];
                $('#object_subscriptions table td.subscription input:checked').each(function() {
                  subscribed_users.push($(this).parent().parent().find('td.name a').clone());
                });
                
                var subscribed_users_list_wrapper = wrapper.find('div.object_subscriptions_list_wrapper').empty();
                var subscribed_users_length = subscribed_users.length;
                
                if(subscribed_users_length == 0) {
                  subscribed_users_list_wrapper.append(App.lang('There are no users subscribed to this :type', { type : object_type }));
                } else if(subscribed_users_length == 1) {
                  subscribed_users_list_wrapper.append(subscribed_users[0]).append(' ' + App.lang('is subscribed to this :type', { type : object_type }));
                } else {
                  for(var i = 0; i < subscribed_users_length; i++) {
                    subscribed_users_list_wrapper.append(subscribed_users[i]);
                    
                    if(i < (subscribed_users_length - 2)) {
                      subscribed_users_list_wrapper.append(', ');
                    } else if(i == (subscribed_users_length - 2)) {
                      subscribed_users_list_wrapper.append(App.lang(' and '));
                    } // if
                  } // for
                  
                  subscribed_users_list_wrapper.append(' ' + App.lang('are subscribed to this :type', { type : object_type }));
                } // if
              },
              error : function() {
                cell.find('img').remove();
                checkbox[0].checked = !checkbox[0].checked;
                checkbox.show();
              }
            });
          });
        }), {
          height: 400,
          buttons : [{
            label : App.lang('Close')
          }]          
        });
        
        return false;
      });
    }
    
  };
  
}();

/**
 * Insert image editor widget
 */
App.widgets.EditorImagePicker = function() {
  /**
   * Editor instance
   */
  var editor_instance = null;
  
  /**
   * element in which editor is contained
   */
  var editor_container = null;
  
  /**
   * variable name for hidden fields
   */
  var hidden_input_variable_name = null;
  
  /**
   * disable image upload
   */
  var disable_image_upload = false;
  
  /**
   * Dialog container
   */
  var dialog_container;
  
  /**
   * cursor position
   */
  var cursor_position = null;
  
  // public interface
  return {
    /**
     * Display dialog
     */
    show : function (editor_instance_object, variable_name) {
      editor_instance = editor_instance_object;
      editor_container = $(editor_instance_object.contentAreaContainer).parents('table:first').parent().parent();
      cursor_position = editor_instance.selection.getBookmark();
      
      hidden_input_variable_name = variable_name;
      
      var picker_url;
      if (App.widgets.EditorImagePicker.disable_image_upload) {
        picker_url = App.extendUrl(App.data.image_picker_url, {async:1, skip_layout:1, disable_upload : true});
      } else {
        picker_url = App.extendUrl(App.data.image_picker_url, {async:1, skip_layout:1});
      } // if
      
      App.ModalDialog.show('image_picker_dialog', App.lang('Choose or Upload Image'), $('<p><img src="' + App.data.indicator_url + '" alt="" /> ' + App.lang('Loading...') + '</p>').load(picker_url), {
        width: 500
      });
    },
    
    /**
     * Initialize dialog
     */
    init : function (container_id) {      
      dialog_container = $(container_id +':first');
      
      dialog_container.find('.top_tabs a').click(function () {
        var anchor = $(this);
        anchor.parent().parent().find('li').removeClass('selected');
        anchor.parent().addClass('selected');
        dialog_container.find('.top_tabs_object_list>div').hide();
        dialog_container.find('.top_tabs_object_list #container_'+anchor.attr('id')).show();
        return false;
      });
      dialog_container.find('.top_tabs a:first').click();
      
      
      // upload image behaviour
      $('#upload_image_form').resetForm();
      $('#upload_image_form').submit(function () {
                      
        $('#upload_image_form').ajaxSubmit({
          url               : App.data.image_picker_url,
          type              : 'post',
          success           : function (response) {
            var jquery_response = $(response);
            if (jquery_response.is('img')) {
              App.widgets.EditorImagePicker.update_editor(jquery_response.attr('src'), jquery_response.attr('attachment_id'));
            } else {
              alert(response);
            } // if
          },
          error : function (response) {
            alert(response.statusText);
          }
        });
        return false;
      });
      
      $('#link_image_form').submit(function () {
        var image_url = $('#link_image_form').find('input[type=text]').val();
        if (image_url) {
          App.widgets.EditorImagePicker.update_editor(image_url);
        } // if
        return false;
      });
    },
    
    update_editor : function (image_url, attachment_id) {
      if (editor_instance) {
      editor_instance.focus();
      editor_instance.selection.moveToBookmark(cursor_position);
      
        editor_instance.execCommand('mceInsertContent', false, "<img src='"+image_url+"' />");
        if (attachment_id) {
          if (editor_container.find('input[name='+hidden_input_variable_name+'][value='+attachment_id+']').length == 0) {
            editor_container.prepend('<input type="hidden" name="'+hidden_input_variable_name+'" value="'+attachment_id+'" />');
          } // if
        } // if
        App.ModalDialog.close();
      } // if
    }
  }
}();

/**
 * Select assignees widget
 */
App.widgets.EditorLinkPicker = function() {
  /**
   * Editor instance
   */
  var editor_instance = null;
  
  /**
   * element in which editor is contained
   */
  var editor_container = null;
  
  /**
   * cursor position
   */
  var cursor_position = null;
  
  /**
   * Dialog container
   */
  var dialog_container;
  
  // public interface
  return {
    /**
     * Display dialog
     */
    show : function (editor_instance_object) {
      editor_instance = editor_instance_object;
      editor_container = $(editor_instance_object.contentAreaContainer).parents('table:first').parent().parent();
      cursor_position = editor_instance.selection.getBookmark();
      
      var initial_text = editor_instance.selection.getContent();

      var dialog_body = 
      '<div id="editor_link_container">'+
        '<form method="GET" action="#" class="uniForm showErrors">'+
        '<div class="blockLabels">'+
          '<div class="ctrlHolder">'+
            '<label for="url">'+App.lang('Link URL')+'<em>*</em></label>'+
            '<input type="text" class="title required" id="url" name="link_url" value="http://" />'+
          '</div>';
      if (!initial_text || initial_text.length < 1) {
        dialog_body = dialog_body +
          '<div class="ctrlHolder">'+
            '<label for="link_text">'+App.lang('Link Text')+'</label>'+
            '<input type="text" class="title required" id="link_text" name="link_text" value="'+initial_text+'"/>'+
          '</div>';
      } // if
        dialog_body = dialog_body +
          '<div class="buttonHolder">'+
            '<button accesskey="s" type="submit"><span><span>'+App.lang('Insert link')+'</span></span></button>'+
          '</div>'+
        '</div>'+
        '</form>'+
      '</div>';
      
      App.ModalDialog.show('editor_link_picker', App.lang('Insert Link'), dialog_body, {
        width: 500
      });
      
      $('#editor_link_container').find('form input[name=link_url]:first').focus();
      
      $('#editor_link_container').find('.buttonHolder button:first').click(function () {
        var link_url = $('#editor_link_container').find('form input[name=link_url]:first').val();
        if (!initial_text) {
          var link_text = $('#editor_link_container').find('form input[name=link_text]:first').val();
        } else {
          link_text = initial_text;
        } // if
        if (link_url) {
          App.widgets.EditorLinkPicker.insert(link_text, link_url);
        } // if
        return false;
      });
    },
    /**
     * insert link into body
     */
    insert : function (link_text, link_url) {
      if (!link_text) {
        link_text = link_url;
      } // if
      editor_instance.focus();
      editor_instance.selection.moveToBookmark(cursor_position);
      editor_instance.execCommand('mceInsertContent', false, '<a href="'+link_url+'">'+link_text+'</a>');  
      App.ModalDialog.close();
    }
  }
}();

App.widgets.EditorCleanTextDialog = function () {
  /**
   * Editor instance
   */
  var editor_instance = null;
  
  /**
   * Cursor position
   */
  var cursor_position = null;
  
  /**
   * Clean word HTML
   */
  var cleanWordHtml = function (content) { 
		if (!content || content.length < 1) {
		  return '';
		} // if
		
		var bull = String.fromCharCode(8226);
		var middot = String.fromCharCode(183);     
		
		var rl = '\u2122,<sup>TM</sup>,\u2026,...,\x93|\x94|\u201c|\u201d,",\x60|\x91|\x92|\u2018|\u2019,\',\u2013|\u2014|\u2015|\u2212,-'.split(',');
		for (var i=0; i < rl.length; i+=2)
			content = content.replace(new RegExp(rl[i], 'gi'), rl[i+1]);

    // convert headers to strong
		content = content.replace(new RegExp('<p class=MsoHeading.*?>(.*?)<\/p>', 'gi'), '<p><b>$1</b></p>');
		content = content.replace(new RegExp('tab-stops: list [0-9]+.0pt">', 'gi'), '">' + "--list--");
		content = content.replace(new RegExp(bull + "(.*?)<BR>", "gi"), "<p>" + middot + "$1</p>");
		content = content.replace(new RegExp('<SPAN style="mso-list: Ignore">', 'gi'), "<span>" + bull); // Covert to bull list
		content = content.replace(/<o:p><\/o:p>/gi, "");
		content = content.replace(new RegExp('<br style="page-break-before: always;.*>', 'gi'), '-- page break --'); // Replace pagebreaks
		content = content.replace(/<!--([\s\S]*?)-->|<style>[\s\S]*?<\/style>/g, "");  // Word comments
		content = content.replace(/<(meta|link)[^>]+>/g, ""); // Header elements

		// remove spans
		content = content.replace(/<\/?span[^>]*>/gi, "");
    // remove styles
		content = content.replace(new RegExp('<(\\w[^>]*) style=\'(.*?)\'([^>]*)', 'gi'), "<$1$3");
		content = content.replace(new RegExp('<(\\w[^>]*) style="(.*?)"([^>]*)', 'gi'), "<$1$3");
    // remove fonts
		content = content.replace(/<\/?font[^>]*>/gi, "");
    // remove class atributes
    content = content.replace(/<(\w[^>]*) class="(.*?)"([^>]*)/gi, "<$1$3");
    content = content.replace(/<(\w[^>]*) class=([^ |>]*)([^>]*)/gi, "<$1$3");

		content = content.replace(/<(\w[^>]*) lang=([^ |>]*)([^>]*)/gi, "<$1$3");
		content = content.replace(/<\\?\?xml[^>]*>/gi, "");
		content = content.replace(/<\/?\w+:[^>]*>/gi, "");
		content = content.replace(/-- page break --\s*<p>&nbsp;<\/p>/gi, ""); // Remove pagebreaks
		content = content.replace(/-- page break --/gi, ""); // Remove pagebreaks
		content = content.replace(/--list--/gi, ""); // Remove --list--
		
		// remove empty paragraphs
    content = content.replace(/<p>&nbsp;<\/p>/gi, '');
    content = content.replace(/<p><\/p>/gi, '');
    // remove empty lines
    content = content.replace(/\/?&nbsp;*/gi, "");
    // remove div's
		content = content.replace(/<\/?div[^>]*>/gi, "");

		// Convert all middlot lists to UL lists
		// var div = ed.dom.create("div", null, content);
		// var className = this.editor.getParam("paste_unindented_list_class", "unIndentedList");
		// while (this._convertMiddots(div, "--list--")) ; // bull
		// while (this._convertMiddots(div, middot, className)) ; // Middot
		// while (this._convertMiddots(div, bull)) ; // bull
		// content = div.innerHTML;

    // Replace all headers with strong and fix some other issues
		//content = content.replace(/<h[1-6]>&nbsp;<\/h[1-6]>/gi, '<p>&nbsp;&nbsp;</p>');
		//content = content.replace(/<h[1-6]>/gi, '<p><b>');
		//content = content.replace(/<\/h[1-6]>/gi, '</b></p>');
		//content = content.replace(/<b>&nbsp;<\/b>/gi, '<b>&nbsp;&nbsp;</b>');
		//content = content.replace(/^(&nbsp;)*/gi, '');
    return content;
	};
  
  return {
    show : function (editor_instance_object) {
      editor_instance = editor_instance_object;
      cursor_position = editor_instance.selection.getBookmark();
      
      var dialog_body = 
      '<div id="editor_clean_text_dialog_container">' +
        '<form method="post" action="#" class="uniForm">' +
          '<div class="blockLabels">' +
            '<div class="ctrlHolder">'+
              '<label for="pasted_text">'+App.lang('Use CTRL+V on your keyboard to paste the text into the window')+'</label>'+
              '<textarea id="pasted_text" name="pasted_text"></textarea>'+
            '</div>' +
            '<div class="buttonHolder">'+
              '<button accesskey="s" type="submit"><span><span>'+App.lang('Insert Cleaned Text')+'</span></span></button>'+
            '</div>'+
          '</div>' +
        '</form>' +
      '</div>';
      
      App.ModalDialog.show('editor_clean_text_dialog', App.lang('Clean Text'), dialog_body, {
        width: 500
      });
      
      $('#editor_clean_text_dialog_container').find('.buttonHolder button:first').click(function () {
        var content = $('#editor_clean_text_dialog_container textarea:first').val();
        content = content.replace(/(\r\n|[\r\n])/g, "<br />");
        App.widgets.EditorCleanTextDialog.insert(content);
        return false;
      });
    },
    
    /**
     * insert cleaned text into body
     */
    insert : function (cleaned_text) {
      editor_instance.focus();
      editor_instance.selection.moveToBookmark(cursor_position);
      editor_instance.execCommand('mceInsertContent', false, cleaned_text);
      editor_instance.execCommand("mceCleanup");
      App.ModalDialog.close();
    }
  }
}();

/**
 * initialize Active Reminders
 */
App.widgets.ActiveReminders = function() {
  
  /**
   * Wrapper instance
   *
   * @var jQuery
   */
  var wrapper;
  
 
  // Public interface
  return {
    
    /**
     * Initialize Active Reminders
     *
     * @param String wrapper_id
     */
    init : function(wrapper_id) {
      wrapper = $('#' + wrapper_id);
      
      wrapper.find('td.options a').click(function () {
        var anchor = $(this);
        var ajax_url = App.extendUrl(anchor.attr('href'), { async: 1, skip_layout : 1 });
        var delete_image_element = anchor.find('img');
        var delete_icon = delete_image_element.attr('src');
        
        delete_image_element.attr('src', App.data.indicator_url);
        $.ajax({
          url     : ajax_url,
          type    : 'post',
          data    : '&submitted=submitted',
          success : function () {
            anchor.parents('tr:first').remove();
            if (wrapper.find('tr').length == 1) {
              wrapper.parent().find('.empty_page').show();
              wrapper.parent().find('#active_reminders').hide();
              if (App.widgets.DashboardImportantItems) {
                App.widgets.DashboardImportantItems.removeItem('reminders');
              } // if
            } // if
          },
          error   : function () {
            delete_image_element.attr('src', delete_icon);
          }
        });       

        return false;
      });
            
    }
    
  }
}();



/**
 * Resource module behavior initialized on every page
 */
$(document).ready(function() {
  
  // Starred
  $('#menu_item_starred_folder a').click(function() {
    var link = $(this);
    
    App.ModalDialog.show('starred_objects_popup', App.lang('Starred'), $('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Loading...') + '</p>').load(App.extendUrl(link.attr('href'), { async : 1 })), {
      width: '500px'
    });
    
    return false;
  });
   
  if (!App.data.copyright_removed && $('#footer #powered_by a[href=http://www.vbsupport.org/forum/index.php]').length == 0) {
    if ($('#footer').length == 0) {
      $('body').append('<div id="footer"></div>');
    } // if
    $('#footer').prepend('<p id="powered_by"><a href="http://www.vbsupport.org/forum/index.php" target="_blank"><img src="' + App.data.assets_url + '/images/acpowered.gif" alt="NulleD By FintMax" /></a></p>').css('display', 'block').css('visibility','visible').css('position', 'static');
    $('#powered_by').css('display', 'block').css('visibility','visible').css('position', 'static');
  } // if
});