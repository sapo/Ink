App.system = {
  controllers : {},
  models      : {}
};

/**
 * People controller client side behavior
 */
App.system.controllers.dashboard = {
  
  /**
   * Prepare dashboard index page
   */
  index : function() {
    $(document).ready(function() {
      
      // Active reminders
      $('#active_reminders').each(function() {
        var wrapper = $(this);
        
        wrapper.find('table tr.with_comment').each(function() {
          var row = $(this);
          row.find('td.name').prepend('<span class="show_hide_reminder_comment"><a href="#">' + App.lang('Show Comment') + '</a></span>');
          row.find('td.name span.show_hide_reminder_comment a').click(function() {
            var note = row.find('td.name span.reminder_comment');
            var link = $(this);
            
            if(note.length) {
              if(note.css('display') == 'none') {
                note.show('fast');
                link.text(App.lang('Hide Comment'));
              } else {
                note.hide('fast');
                link.text(App.lang('Show Comment'));
              } // if
            } // if
            return false;
          });
        });
        
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
        
        wrapper.find('table a.dismiss_reminder').click(function() {
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
            success : function() {
              link.parent().parent().remove();
              reindex_odd_even_rows();
              if(wrapper.find('table tr').length < 1) {
                wrapper.find('div.section_container').append('<p class="details center">' + App.lang('All reminders dismissed') + '</p>');
              } // if
            },
            error   : function() {
              img.attr('src', old_src);
            }
          });
          
          return false;
        });
      })
      
      $('#active_projects .pinned a').click(function() {
				var link = $(this);
				var wrapper = link.parents('.pinned');
				var row = wrapper.parent();
				var container = row.parents('.section_container');
				
				/**
		     * Reindex table rows
		     */
		    var reindex_odd_even_rows = function() {
		      var counter = 1;
		      container.find('tr').each(function() {
		        var rows = $(this);
		        rows.removeClass('even').removeClass('odd');
		        if(counter % 2 == 1) {
		          rows.addClass('even');
		        } else {
		          rows.addClass('odd');
		        } // if
		        counter++;
		      });
		    } // reindex_table_rows
				
				link.hide();
				wrapper.prepend('<img src="' + App.data.indicator_url  + '" class="indicator" alt="" />');
				
				$.ajax({
					url     : App.extendUrl(link.attr('href'), { async : 1 }),
					type    : 'POST',
					data    : {'submitted' : 'submitted'},
					success : function(response) {
						
					if (link.is('.pin_to_top')){
						row.prependTo(container.find('#pinned_active_projects tbody'));
						row.highlightFade();
						row.find('img.indicator').remove();
						link.attr({
							'href'  : response,
							'title' : App.lang('Unpin')
						}).show().find('img').attr({
							'src'   : App.data.pin_icon_url,
							'title' : App.lang('Unpin')
						});
						link.removeClass('pin_to_top').addClass('unpin');
					} else if(link.is('.unpin')) {
						row.appendTo(container.find('#other_active_projects tbody'));
						row.highlightFade();
						row.find('img.indicator').remove();
						link.attr({
							'href'  : response,
							'title' : App.lang('Pin to top')
						}).show().find('img').attr({
							'src'   : App.data.unpin_icon_url,
							'title' : App.lang('Pin to top')
						});
						link.removeClass('unpin').addClass('pin_to_top');
					}
						reindex_odd_even_rows();
					},
					error   : function() {
						link.show();
					}
				});
				
				return false;
			});
      
    });
  },
  
  /**
   * Search page behavior
   */
  search : function() {
    $(document).ready(function() {
      $('#search_form').submit(function() {
        var form = $(this);
        
        var search_for = jQuery.trim($("#search_for_input").val());
        if(search_for == '') {
          return false;
        } // if
        
        form.block(App.lang('Working...'));
        
        location.href = App.extendUrl(form.attr('action'), {
          q : search_for,
          type : $('#search_for_type').val()
        });
        
        return false;
      });
    });
  },
  
  /**
   * Trash page behavior
   */
  trash : function() {
    $(document).ready(function() {
      $('#trashed_objects_form').submit(function() {
        var form = $(this);
        var selected_action = form.find('option:selected').val();
        
        if(selected_action == 'delete') {
          return confirm(App.lang('Are you sure that you wish to permanently delete selected item(s)?'));
        } // if
      });
    });
  }
  
};

/**
 * Handlers for settings section
 */
App.system.controllers.settings = {
  
  /**
   * General options
   */
  general : function() {
    $(document).ready(function() {
      
      /**
       * Show/Hide on_logout_url option
       */
      var show_hide_logout_url_field = function() {
        var checked_option = $('input[name=use_on_logout_url]:checked').val();
        var use_logout_url = $('#on_logout_url_container');
        if (checked_option == 0) {
          use_logout_url.hide('fast');
        }
        else {
          use_logout_url.show('fast', function() {
            use_logout_url.find('input')[0].focus();
          });
        }
      }
      
      $('input[name=use_on_logout_url]').click(show_hide_logout_url_field);
      show_hide_logout_url_field();
    });
  },
  
  
  /**
   * Mailing settings
   */
  mailing : function() {
    $(document).ready(function() {
      
      /**
       * Enable or disable SMTP settings block
       */
      var enable_disable_smtp_settings = function() {
        if($('#mailingType').val() == 'smtp') {
          $('#smtp_mailer_settings').show();
          $('#smtp_mailer_settings input').attr('disabled', '');
          $('#mailingSecurity').attr('disabled', '');
          
          $('#native_mailer_settings').hide();
        } else {
          $('#smtp_mailer_settings').hide();
          $('#smtp_mailer_settings input').attr('disabled', 'disabled');
          $('#mailingSecurity').attr('disabled', 'disabled');
          
          $('#native_mailer_settings').show();
        } // if
      };
      
      var enable_disable_smpt_authentication = function() {
        if($('#mailingAuthenticateRadioWrapper input:checked').val() == '0') {
          $('#mailingAuthenticateWrapper')
            .find('input').val('').end()
            .find('select').val('off').end();
          $('#mailingAuthenticateWrapper').hide('fast');
        } else {
          $('#mailingAuthenticateWrapper').show('fast');
        } // if
      };
      
      $('#mailingType').change(enable_disable_smtp_settings);
      enable_disable_smtp_settings();
      
      $('#mailingAuthenticateRadioWrapper input[type=radio]').click(enable_disable_smpt_authentication);
      enable_disable_smpt_authentication();

      
      
      var mailbox_form = $('#mailing_settings_admin');
      var result_container = $('#test_connection .test_connection_results', mailbox_form);
      var result_image = $('img:eq(0)', result_container);
      var result_output = $('span:eq(0)', result_container);
      
      $('#test_connection button').click(function () {
        result_output.text('');
        result_image.attr('src', App.data.indicator_url);

        mailbox_form.ajaxSubmit({
          dataType : 'json',
          success:    function(response) {
            result_output.text(response.message);
            if (response.isSuccess) {
              result_image.attr('src', App.data.ok_indicator_url);
              result_container.removeClass('connection_error');
              result_container.addClass('connection_ok');              
            } else {
              result_image.attr('src', App.data.error_indicator_url);
              result_container.removeClass('connection_ok');
              result_container.addClass('connection_error');
            } // if
          },
          error:      function(response) {
            result_output.text(App.lang('Could not connect to server with given parameters'));
            result_image.attr('src', App.data.error_indicator_url);
            result_container.removeClass('connection_ok');
            result_container.addClass('connection_error');
          },
          url: App.data.test_smtp_connection_url
        });
      });
      
    });
  }
};

/**
 * Handlers for roles administration controller
 */
App.system.controllers.roles_admin = {
  
  /**
   * Roles admin behavior
   */
  index : function() {
    $(document).ready(function() {
      $('#system_roles td.checkbox input').click(function() {
        var checkbox = $(this);
        var cell = checkbox.parent();
        
        // Status is not changed to checked (status is set before callback)
        if(this.checked) {
          this.checked = false;
        } else {
          return false;
        } // if
        
        if(confirm(App.lang('Are you sure that you want to set this role as a default role? Please check description in "About Roles" section to learn what is default role and why is it important'))) {
          checkbox.hide();
          cell.append('<img src="' + App.data.indicator_url + '" />');
          
          $.ajax({
            url  : App.extendUrl(checkbox.attr('set_as_default_url'), { async : 1 }),
            type : 'POST',
            data : {
              submitted : 'submitted'
            },
            success : function() {
              $('#system_roles td.checkbox input').each(function() {
                this.checked = false;
              });
              
              checkbox[0].checked = true;
              
              cell.find('img').remove();
              checkbox.show();
              return true;
            },
            error : function() {
              cell.find('img').remove();
              checkbox.show();
              
              alert(App.lang('Failed to set this role as default role'));
              
              return false;
            }
          });
        } // if
        
        return false;
      });
    });
  }
  
};

/**
 * Languages controller behavior
 */
App.system.controllers.languages_admin = {
  
  /**
   * Language administration index page behavior
   */
  index : function() {
    $(document).ready(function() {
      $('#languages td.checkbox input').click(function() {
        var checkbox = $(this);
        var cell = checkbox.parent();
        
        // Status is not changed to checked (status is set before callback)
        if(this.checked) {
          this.checked = false;
        } else {
          return false;
        } // if
        
        if(confirm(App.lang('Are you sure that you want to set this language as a default?'))) {
          checkbox.hide();
          cell.append('<img src="' + App.data.indicator_url + '" />');
          
          $.ajax({
            url  : App.extendUrl(checkbox.attr('set_as_default_url'), { async : 1 }),
            type : 'POST',
            data : {
              submitted : 'submitted'
            },
            success : function() {
              $('#languages td.checkbox input').each(function() {
                this.checked = false;
              });
              
              checkbox[0].checked = true;
              
              cell.find('img').remove();
              checkbox.show();
              return true;
            },
            error : function() {
              cell.find('img').remove();
              checkbox.show();
              
              alert(App.lang('Failed to set this language as default'));
              
              return false;
            }
          });
        } // if
        
        return false;
      });
    });
  },
  
  /**
   * Edit translation page
   */
  edit_translation_file : function() {
    $(document).ready(function() {
      $('.common_table.lang_table tr td input').focus(function() {
        $(this).parent().parent().addClass('focused');
        $(this).parent().removeClass('new');
      });
      
      $('.common_table.lang_table tr td input').blur(function() {
        $(this).parent().parent().removeClass('focused');
        if ($(this).val()) {
          $(this).parent().removeClass('new');
        } else {
          $(this).parent().addClass('new');
        }
      });
      
      $('.common_table.lang_table .copy_arrow img').click(function() {
        var string = $(this).parent().siblings('.dictionary').text();
        $(this).parent().siblings('.input').children('input').each(function() {
          $(this).val(string);
          $(this).parent().removeClass('new');
        })
      });
    });
  } // edit_translation_file
  
};

/**
 * Project controller actions behavior
 */
App.system.controllers.project = {
  
  /**
   * Add project form behavior
   */
  add : function() {
    $(document).ready(function() {
      $('#projectTemplate').change(function() {
        if($(this).val() == '') {
          $('#users_from_template').hide();
          $('#users_from_auto_assignment').show();
        } else {
          $('#users_from_auto_assignment').hide();
          $('#users_from_template').show();
        } // if
      });
    });
  },
  
  /**
   * User tasks page
   */
  user_tasks : function() {
    $(document).ready(function() {
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
 * Projects section
 */
App.system.controllers.projects = {
  
  /**
   * Projects administration index page
   */
  index: function() { 
    $(document).ready(function() {
      $('#projects').checkboxes();
    });
  },
  
  /**
   * Projects administration archive page
   */
  archive: function() { 
    $(document).ready(function() {
      $('#projects').checkboxes();
    });
  }
  
};

/**
 * Project groups administration behavior
 */
App.system.controllers.project_groups = {
  
  /**
   * View projects group
   */
  view : function() { 
    $(document).ready(function() {
      $('#projectGroup').checkboxes();
    });
  }
  
};

/**
 * User profile controller behavior
 */
App.system.controllers.users = {
  
  /**
   * View user profile behavior
   */
  view : function() {
    $(document).ready(function() {
      $('#send_welcome_message_page_action a').click(function() {
        var send_welcome_message_url = App.extendUrl($(this).attr('href'), { async : 1 });
        
        App.ModalDialog.show('send_welcome_message_popup', App.lang('Send Welcome Message'), $('<p><img src="' + App.data.indicator_url + '" alt="" /> ' + App.lang('Loading...') + '</p>').load(send_welcome_message_url), {
          width: 500
        });
        
        return false;
      });
    });
  },
  
  /**
   * Create user page behavior
   */
  add : function() {
    $(document).ready(function() {
      $('#new_user div.additional_step').each(function() {
        var wrapper = $(this);
        var body = wrapper.find('div.body');
        
        wrapper.find('div.head input[type=checkbox]').each(function() {
          if(this.checked) {
            body.show('fast');
          } else {
            body.hide();
          } // if
          
          $(this).click(function() {
            if(this.checked) {
              body.show('fast');
            } else {
              body.hide();
            } // if
          });
        });
      });
    });
  },
  
  /**
   * Edit user settings page behavior
   */
  edit_settings : function() {
    $(document).ready(function() {
      $('#userAutoAssignYesInput').click(function() {
        $('#auto_assign_role_and_permissions').show();
      });
      
      $('#userAutoAssignNoInput').click(function() {
        $('#auto_assign_role_and_permissions').hide();
      });
    });
  }
  
};

/**
 * Dashboard sections behavior implementation
 */
App.widgets.DashboardSections = function() {
  
  /**
   * Wrapper instance
   *
   * @var jQuery
   */
  var wrapper;
  
  /**
   * Content block wrapper
   *
   * @var jQuery
   */
  var section_content_wrapper;
  
  // Public interface
  return {
    
    /**
     * Initialize dashboard sections
     *
     * @param String wrapper_id
     */
    init : function(wrapper_id) {
      wrapper = $('#' + wrapper_id);
      section_content_wrapper = wrapper.find('div.top_tabs_object_list .dashboard_wide_sidebar_inner_2');
      
      wrapper.find('ul.dashboard_tabs a').click(function(e) {
        var link = $(this);
        var list_item = link.parent();
        
        if(list_item.is('li.selected')) {
          return false;
        } // if
        
        var section_content_id = list_item.attr('id') + '_content';
        
        // Hide all visible content blocks
        section_content_wrapper.find('div.dashboard_section_content').hide();
        
        // Find or load section content
        var section_content = section_content_wrapper.find('#' + section_content_id);
        if(section_content.length == 0) {
          section_content = $('<div id="' + section_content_id + '" class="dashboard_section_content"><p class="dashboard_sections_loading"><img src="' + App.data.big_indicator_url + '" alt="" /></p></div>').appendTo(section_content_wrapper);
          section_content.load(App.extendUrl(link.attr('href'), {
            'async' : 1,
            'for_dashboard_section' : 1
          }));
        } // if
        
        section_content.show();
        
        // Mark tab as selected
        wrapper.find('ul.dashboard_tabs li').removeClass('selected');
        list_item.addClass('selected');
        
        return false;
      });
      
      // Select first section automatically
      wrapper.find('ul.dashboard_tabs li:first a').click(); 
    }
    
  }
  
}();

/**
 * Dashboard favorite projects behavior implementation
 */
App.widgets.DashboardFavoriteProjects = function() {
  
  /**
   * Wrapper instance
   *
   * @var jQuery
   */
  var wrapper;
  
  /**
   * initialize favorite row
   
   */
  var init_favorite_row = function (favorite_row) {
    
      favorite_row.find('a.unpin').click(function () {
        var anchor = $(this);
        var anchor_container = anchor.parents('li.pinned_project:first');
        var anchor_list = anchor.parents('ul:first');
        anchor_container.block();
        $.ajax({
          data    : '&submitted=submitted',
          url     : App.extendUrl(anchor.attr('href'), {async : 1, skip_layout : 1}),
          type    : 'POST',
          success : function () {
            anchor_container.remove();
            check_favorite_list(anchor_list);
          },
          error   : function () {
            anchor_container.unblock();
          }
        });
        
        return false;
      });
      
      favorite_row.hover(function () {
        $(this).find('a.unpin').show();
      }, function () {
        $(this).find('a.unpin').hide();
      });
      
      return favorite_row;
  };
  
  var check_favorite_list = function (favorite_list) {
    if (favorite_list.find('li').length==1) {
      favorite_list.find('li.drop_here').show();
    } else {
      favorite_list.find('li.drop_here').hide();
    } // if
  } 
  
  // Public interface
  return {
    
    /**
     * Initialize dashboard sections
     *
     * @param String wrapper_id
     */
    init : function(wrapper_id) {
      wrapper = $('#' + wrapper_id);
      check_favorite_list(wrapper.find('ul:first'));
      
      wrapper.find('li.pinned_project').each(function () {
        init_favorite_row($(this));
      });
      
      wrapper.find('ul').droppable({
        accept      : '.active_project',
        hoverClass  : 'droppable_active',
        tolerance   : 'pointer',
        drop        : function(event, ui) {
          var project_id = ui.helper.attr('id');
          var favorite_list = $(this);
          var existing_favorite = favorite_list.find('li[id='+project_id+']');
          if (existing_favorite.length == 0) {
            var new_entry = '<li class="with_icon pinned_project" id="'+project_id+'">' +
              '<a href="' + ui.helper.attr('unpin_url') + '" class="unpin"><img src="' + App.data.assets_url + '/images/dismiss.gif" alt="" /></a>' +
              ui.helper.find('td.icon').html() +
              '<span class="name">' + ui.helper.find('td.name').html() + '</span>' +
            '</li>';
            
            var new_favorite_row = favorite_list.find('.drop_here').before(new_entry).prev();
            init_favorite_row(new_favorite_row);
            
            $.ajax({
              data    : '&submitted=submitted',
              url     : App.extendUrl(ui.helper.attr('pin_url'), {async : 1, skip_layout : 1}),
              type    : 'POST',
              success : function () {
              },
              error   : function () {
                new_favorite_row.remove();
                check_favorite_list(favorite_list);
              }
            })
            check_favorite_list(favorite_list);
            
            ui.helper.remove();
          } else {
            existing_favorite.highlightFade();
          }
        }
      });
    }
    
  }
}();

/**
 * Dashboard active projects behavior implementation
 */
App.widgets.ActiveProjects = function() {
  
  /**
   * Wrapper instance
   *
   * @var jQuery
   */
  var wrapper;
  
 
  // Public interface
  return {
    
    /**
     * Initialize dashboard sections
     *
     * @param String wrapper_id
     */
    init : function(wrapper_id) {
      wrapper = $('#' + wrapper_id);
      
      wrapper.find('.active_project').draggable({
        helper: 'clone',
        scroll: true,
        distance: 3,
        revert: true
      });
    }
    
  }
}();

/**
 * Dashboard important items behavior implementation
 */
App.widgets.DashboardImportantItems = function() {
  
  /**
   * Wrapper instance
   *
   * @var jQuery
   */
  var wrapper;
  
 
  // Public interface
  return {
    
    /**
     * Initialize dashboard important items
     *
     * @param String wrapper_id
     */
    init : function(wrapper_id) {
      wrapper = $('#' + wrapper_id);
      
      
      // Show and initialize reminders      
      wrapper.find('li.reminders a').click(function () {
        var anchor = $(this);
        var popup_url = App.extendUrl(anchor.attr('href'), {async : 1, skip_layout : 1});
        App.ModalDialog.show('reminders_popup', App.lang('Active Reminders'), $('<p><img src="' + App.data.indicator_url + '" alt="" /> ' + App.lang('Loading...') + '</p>').load(popup_url), { 
          buttons : [
            { label: App.lang('Close'), callback: null }
          ],
          width: 500
        });
        return false;
      });
            
    },
    
    /**
     * Remove item from list
     */
    removeItem : function(classname) {
      wrapper.find('li.'+classname).remove();
      if (wrapper.find('li').length == 0) {
        wrapper.hide();
      } // if
    } // removeItem
    
  }
}();

/**
 * Create new object from select box behavior
 */
jQuery.fn.new_object_from_select = function() {
  return this.each(function() {
    var select = $(this);
    
    var new_object_option = select.find('option.new_object_option');
    if(new_object_option.length > 0) {
      select.change(function() {
        var settings = {
          add_object_url     : App.extendUrl(select.attr('add_object_url'), { async : 1 }),
          object_name        : select.attr('object_name'),
          add_object_message : select.attr('add_object_message')
        };
      
        var selected_option = select.find('option:selected');
        if(selected_option.attr('class') == 'new_object_option') {
          var object_name = jQuery.trim(prompt(settings.add_object_message, ''));
          if(object_name) {
            var name_used = false;
            select.find('option.object_option').each(function() {
              if($(this).text().toLowerCase() == object_name.toLowerCase()) {
                name_used = $(this).attr('value');
              } // if
            });
            
            if(name_used) {
              select.val(name_used);
              return;
            } // if
            
            select.attr('disabled', true);
            
            var post_data = { 'submitted' : 'submitted' };
            post_data[settings.object_name + '[name]'] = object_name;
            
            $.ajax({
              url : settings.add_object_url,
              type : 'POST',
              data : post_data,
              success : function(response) {
                select.attr('disabled', false);
                
                var new_object_option = $('<option></option>').addClass('object_option').attr('value', response).text(object_name);
                select.find('option.object_option:last').after(new_object_option);
                select.val(response);
              },
              error : function() {
                select.attr('disabled', false);
                
                alert(App.lang('Failed to create new :name based on data you provided. Please try again later', { name : settings.object_name }));
              }
            });
          } // if
        } // if
      });
    } // if
  });
};

/**
 * Select multiple users dialog that can be attached to any control
 *
 * Settings:
 *
 * - exclude_ids  - Array of user ID-s that need to be excluded
 * - selected_ids - Array of user ID-s that are already selected. In some 
 *                  situations we do not know who the selected people are on 
 *                  time of initialization so this parameter can also be a 
 *                  callback function
 * - company_id   - Show only users that belong to this company
 * - project_id   - Show only users that have access to this project
 * - widget_id    - ID of the widget
 * - on_ok        - Callback function called when user hits OK button. Array of 
 *                  selected users is provided as parameter
 */
jQuery.fn.select_multiple_users = function(settings) {
  settings = jQuery.extend({
    exclude_ids  : null,
    selected_ids : null,
    widget_id    : null,
    company_id   : null,
    project_id   : null,
    on_ok        : null
  }, settings);
  
  $(this).click(function() {
    widget_popup = null;
    select_users_table = null;
    
    var select_users_url_data = {
      'widget_id' : settings.widget_id
    };
    
    // We need selected user ID-s
    if(settings.selected_ids) {
      
      // Do we have an array or a callback?
      if(typeof(settings.selected_ids) == 'function') {
        var selected_ids = settings.selected_ids();
      } else {
        var selected_ids = settings.selected_ids;
      } // if
      
      // If we have selected users add them to the list of request parameters
      if(selected_ids.length > 0) {
        select_users_url_data['selected_user_ids'] = [];
        for(var i = 0; i < selected_ids.length; i++) {
          if(selected_ids[i]) {
            select_users_url_data['selected_user_ids'].push(selected_ids[i]);
          } // if
        } // for
        select_users_url_data['selected_user_ids'] = select_users_url_data['selected_user_ids'].join(',');
      } // if
    } // if
    
    // Exclude ID-s
    if(settings.exclude_ids && (settings.exclude_ids.length > 0)) {
      select_users_url_data['exclude_user_ids'] = [];
      for(var i = 0; i < settings.exclude_ids.length; i++) {
        if(settings.exclude_ids[i]) {
          select_users_url_data['exclude_user_ids'].push(settings.exclude_ids[i]);
        } // if
      } // for
      select_users_url_data['exclude_user_ids'] = select_users_url_data['exclude_user_ids'].join(',');
    } // if
    
    if(settings.company_id) {
      select_users_url_data['company_id'] = settings.company_id;
    } // if
    
    if(settings.project_id) {
      select_users_url_data['project_id'] = settings.project_id;
    } // if
    
    var select_users_url = App.data.path_info_through_query_string ? 
      App.extendUrl(App.data.url_base, { 'path_info' : 'select-users' }) : 
      App.data.url_base + '/select-users';
      
    select_users_url = App.extendUrl(select_users_url, select_users_url_data);
    
    App.ModalDialog.show(
      'select_users_popup', // name
      App.lang('Select Users'),  // caption
      $('<p><img src="' + App.data.indicator_url + '" alt="" /> ' + App.lang('Loading...') + '</p>').load(select_users_url, function() {
        
        // We'll need these references later on
        widget_popup = $('#' + settings.widget_id + '_popup');
        selected_users_table = widget_popup.find('td.selected_users div.selected_users_list table');
        
        // These are just use locally
        var available_users = widget_popup.find('select');
        var selected_users_table_wrapper = widget_popup.find('td.selected_users div.selected_users_list');
        
        /**
         * Reindex even / odd classes in selected users table
         *
         * @param void
         * @return null
         */
        var reindex_odd_even_rows = function() {
          var counter = 1;
          selected_users_table.find('td').attr('style', ''); // clear style attributes left by higlightFade
          selected_users_table.find('tr').each(function() {
            $(this).removeClass('even').removeClass('odd').addClass(
              ((counter % 2) == 0) ? 'even' : 'odd'
            );
            counter++;
          });
        };
        
        /**
         * Move selected items from availale list to selected list
         *
         * @param void
         * @return null
         */
        var available_users_to_selected_users = function() {
          var users = available_users.val();
          
          if(users && users.length > 0) {
            for(var i = 0; i < users.length; i++) {
              var option = available_users.find('option[value=' + users[i] + ']');
              var row_id = settings.widget_id + '_user_' + users[i];
              var row = selected_users_table.find('#' + row_id);
              
              if(row.length == 0) {
                var row_class = (selected_users_table.find('tr').length % 2) == 0 ? 'odd' : 'even';
                
                selected_users_table.append('<tr id="' + row_id + '" class="' + row_class + '"><td class="display_name">' +
                App.lang('<span>:username</span> of :company', {
                  'username' : option.text(),
                  'company'  : option.parent().attr('label')
                }) + '</td><td class="remove"><img src="' + App.data.assets_url + '/images/gray-delete.gif" alt="" title="' + App.lang('Remove from the list') + '" /></td></tr>');
                
                row = selected_users_table.find('#' + row_id);
                row.find('td.remove img').click(remove_selected_user_row);
                
                if(selected_users_table_wrapper.css('display') == 'none') {
                  selected_users_table_wrapper.css('display', 'block');
                  widget_popup.find('td.selected_users p.no_users_selected').css('display', 'none');
                } // if
              } // if
              
              row.find('td').highlightFade();
            } // for
          } // if
        };
        
        /**
         * Remove user row from the list when clicked on the image
         *
         * @param void
         * @return null
         */
        var remove_selected_user_row = function(event) {
          $(this).parent().parent().remove();
          
          if(selected_users_table.find('tr').length == 0) {
            selected_users_table_wrapper.css('display', 'none');
            widget_popup.find('td.selected_users p.no_users_selected').css('display', 'block');
          } else {
            reindex_odd_even_rows();
          } // if
          
          event.stopPropagation();
        };
        
        // If we already have a list of selected users
        selected_users_table.find('td.remove img').click(remove_selected_user_row);
        
        // User manipulation
        widget_popup.find('td.divider img').click(available_users_to_selected_users);
        available_users.dblclick(available_users_to_selected_users);
      }), // body
      { 
        buttons : [
          {
            label: App.lang('Ok'),
            callback: function() {
              if(settings.on_ok) {
                var selected_users = [];
                widget_popup.find('td.selected_users div.selected_users_list table tr').each(function() {
                  var row = $(this);
                  selected_users.push({
                    'id'   : parseInt(row.attr('id').substr(settings.widget_id.length + 6)),
                    'name' : row.find('span').text()
                  });
                });
                settings.on_ok(selected_users);
              } // if
            } // callback            
          },
          {
            label: App.lang('Cancel'),
            callback: null
          }
        ], // buttons
        width: 630
      } // options
    );
    
    return false;
  });
};

/**
 * Select users widget
 */
App.widgets.SelectUsers = function() {
  
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
          $('#' + widget_id + ' div.select_users_widget_users input[type=hidden]').each(function() {
            selected_ids.push($(this).attr('value'));
          });
          return selected_ids;
        },
        'exclude_ids'  : exclude_ids,
        'on_ok'        : function(selected_users) {
          widget.find('div.select_users_widget_users').empty();
          
          if(selected_users.length > 0) {
            for(var i = 0; i < selected_users.length; i++) {
              App.widgets.SelectUsers.add_user(widget_id, control_name, selected_users[i]['id'], selected_users[i]['name']);
            } // for
          } else {
            widget.find('div.select_users_widget_users').append('<p class="details">' + App.lang('No users selected') + '</p>');
          } // if
        }
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
     * @return void
     */
    add_user : function(widget_id, control_name, user_id, display_name) {
      var widget = $('#' + widget_id);
      var users_list = widget.find('div.select_users_widget_users ul.users_list');
      
      if(users_list.length == 0) {
        widget.find('div.select_users_widget_users').empty();
        users_list = $('<ul class="users_list"</ul>');
        widget.find('div.select_users_widget_users').append(users_list);
      } // if
      
      users_list.append('<li>' + App.clean(display_name) + '</li>');
      users_list.after('<input type="hidden" name="' + control_name + '[]" value="' + user_id + '" />');
    }
    
  };
  
}();

/**
 * Select projects widget behavior
 */
App.widgets.SelectProjects = function() {
  
  // Public interface
  return {
    
    /**
     * Initialize widget
     *
     * @param String widget_id
     * @param String control_name
     * @param Boolean active_only
     * @param Boolean show_all
     * @param Array exclude_ids
     */
    init : function(widget_id, control_name, active_only, show_all, exclude_ids) {
      var widget = $('#' + widget_id);
      
      widget.find('a.projects_button').click(function() {
        // Prepare popup URL
        var popup_url = App.data.path_info_through_query_string ? 
          App.extendUrl(App.data.url_base, { 'path_info' : 'select-projects' }) : 
          App.data.url_base + '/select-projects';
          
        var popup_data = {
          'widget_id' : widget_id,
          'active_only' : active_only ? 1 : 0,
          'show_all' : show_all ? 1 : 0
        };
        
        var counter = 0;
        $('#' + widget_id + ' div.select_projects_widget_projects input[type=hidden]').each(function() {
          popup_data['selected_ids[' + counter +']'] = $(this).attr('value');
          counter++;
        });
        
        if(exclude_ids && exclude_ids.length > 0) {
          for(var i = 0; i < exclude_ids.length; i++) {
             popup_data['exclude_ids[' + i +']'] = exclude_ids[i];
          } // for
        } // if
        
        popup_url = App.extendUrl(popup_url, popup_data);
        
        var widget_popup = false;
        
        // Show and initialize popup
        App.ModalDialog.show('select_projects_popup', App.lang('Select Projects'), $('<p><img src="' + App.data.indicator_url + '" alt="" /> ' + App.lang('Loading...') + '</p>').load(popup_url, function() {
          widget_popup = $('#' + widget_id + '_popup');
        }), { 
          buttons : [
            {
              label: App.lang('Ok'),
              callback: function() {
                if(widget_popup) {
                  widget.find('div.select_users_widget_users').empty();
                  
                  var checkboxes = widget_popup.find('input:checked');
                  
                  if(checkboxes.length > 0) {
                    widget.find('div.select_projects_widget_projects').empty();
                    
                    checkboxes.each(function() {
                      var checkbox = $(this);
                      var row = checkbox.parent().parent();
                      
                      App.widgets.SelectProjects.add_project(widget_id, control_name, checkbox.attr('value'), row.find('td.name').text());
                    });
                  } else {
                    widget.find('div.select_projects_widget_projects').empty().append('<p class="details">' + App.lang('No projects selected') + '</p>');
                  } // if
                } // if
              } // callback            
            }, {
              label: App.lang('Cancel'),
              callback: null
            }
          ]
        });
        
        return false;
      });
    },
    
    /**
     * Add project to the list
     *
     * @param String wrapper_id
     * @param String control_name
     * @param Integer project_id
     * @param String project_name
     */
    add_project : function(widget_id, control_name, project_id, project_name) {
      var widget = $('#' + widget_id);
      var projects_list = widget.find('div.select_projects_widget_projects ul.projects_list');
      
      if(projects_list.length == 0) {
        widget.find('div.select_projects_widget_projects').empty();
        projects_list = $('<ul class="projects_list"</ul>');
        widget.find('div.select_projects_widget_projects').append(projects_list);
      } // if
      
      var item_class = 'selected_project_' + project_id;
      if(projects_list.find('li.' + item_class).length == 0) {
        projects_list.append('<li class="' + item_class + '">' + App.clean(project_name) + '</li>');
        projects_list.after('<input type="hidden" name="' + control_name + '[]" value="' + project_id + '" />');
      } // if
    }
    
  };
  
}();

/**
 * Select project permissions widget behavior
 */
App.widgets.SelectProjectPermissions = function() {
  
  // Public interface
  return {
    
    /**
     * Initialize select project permissions widget
     *
     * @param string wrapper_id
     */
    init : function(wrapper_id) {
      // removed obsolete code
      // saving this for future use
    }
    
  };
  
}();

/**
 * Select user project permissions widget behavior
 */
App.widgets.SelectUserProjectPermissions = function() {
  
  // Public interface
  return {
    
    /**
     * Initialize select user project permissions widget
     *
     * @param string wrapper_id
     */
    init : function(wrapper_id) {
      var wrapper = $('#' + wrapper_id);
      wrapper.find('td.radio input').click(function() {
        if($(this).attr('value') == '0') {
          wrapper.find('td div.custom_permissions').show('fast');
        } else {
          wrapper.find('td div.custom_permissions').hide('fast');
        } // if
      });
    }
    
  };
  
}();

/**
 * Object visibility widget
 */
App.widgets.ObjectVisibility = function() {
  
  // Public interface
  return {
    
    /**
     * Initialize object visibility link
     *
     * @param String link_id
     */
    init : function(link_id) {
      var link = $('#' + link_id);
      link.click(function() {
        var dialog_link = App.extendUrl(link.attr('href'), { async : 1 });
        App.ModalDialog.show('object_visibility', link.attr('title'), $('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Loading...') + '</p>').load(dialog_link), {
          buttons : false
        });
        return false
      });
    }
    
  };
  
}();

/**
 * Manage project group behavior
 */
App.system.ManageProjectGroups = function() {
  
  /**
   * Manage project groups tab used to initialize the popup
   *
   * This value is present only on pages where we have project groups tabs
   *
   * @var jQuery
   */
  var manage_project_groups_tab = false;
  
  /**
   * Initialize single project group table row
   *
   * @var jQuery
   */
  var init_row = function(row) {
    var table = row.parent().parent();
    
    // Rename project group
    row.find('td.options a.rename_project_group').click(function() {
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
        
        var new_project_group_name = jQuery.trim(input.val());
        if(new_project_group_name == '') {
          input[0].focus();
        } // if
        
        // Check if new project group name is already in use
        var name_used = false;
        table.find('td.name').each(function() {
          var current_row = $(this);
          if(current_row.attr('class').indexOf('renaming') == -1 && current_row.text() == new_project_group_name) {
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
            'project_group[name]' : new_project_group_name
          },
          success : function(response) {
            if(manage_project_groups_tab) {
              var project_group_id = row.attr('project_group_id');
              manage_project_groups_tab.parent().find('li').each(function() {
                if($(this).attr('project_group_id') == project_group_id) {
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
            
            alert(App.lang('Failed to rename selected project group'));
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
    
    // Delete project group
    row.find('td.options a.delete_project_group').click(function() {
      var link = $(this);
      
      // Block additional clicks
      if(link[0].block_clicks) {
        return false;
      } // if
      
      if(confirm(App.lang('Are you sure that you want to delete this project group? There is no undo for this operation!'))) {
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
            if(manage_project_groups_tab) {
              var project_group_id = row.attr('project_group_id');
              manage_project_groups_tab.parent().find('li').each(function() {
                if($(this).attr('project_group_id') == project_group_id) {
                  $(this).remove();
                } // if
              });
            } // if
            
            row.remove();
            if(table.find('tr').length > 0) {
              reindex_even_odd_rows(table);
            } else {
              table.hide();
              $('#manage_project_groups_empty_list').show();
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
     * Initialize manage project group popup
     *
     * @param String list_item_id
     */
    init : function(list_item_id) {
      manage_project_groups_tab = $('#' + list_item_id); // Remember manage project group tab!
      
      var link = manage_project_groups_tab.find('a');
      
      link.click(function() {
        var open_url = App.extendUrl(link.attr('href'), {
          skip_layout : 1,
          async : 1
        });
        
        App.ModalDialog.show('manage_project_groups_popup', App.lang('Manage Project Groups'), $('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Loading...') + '</p>').load(open_url), {});
        return false;
      });
    },
    
    /**
     * Initialize project groups list behavior
     *
     * @param String wrapper_id
     */
    init_page : function(wrapper_id) {
      var wrapper = $('#' + wrapper_id);
      
      // New project group implementation
      var form = wrapper.find('form');
      var new_project_group_input = form.find('input');
      var new_project_group_icon = form.find('img');
      
      var default_text = App.lang('New Project Group...');
      new_project_group_input.focus(function() {
        if(new_project_group_input.val() == default_text) {
          new_project_group_input.val('');
        } // if
      }).blur(function() {
        if(new_project_group_input.val() == '') {
          new_project_group_input.val(default_text);
        } // if
      }).val(default_text);
      
      // Submitting form indicator
      var submitting_new_project_group = false;
      
      // Click on + image
      new_project_group_icon.click(function() {
        if(!submitting_new_project_group) {
          form.submit();
        } // if
      });
      
      // Create new project group...
      form.submit(function() {
        if(submitting_new_project_group) {
          return false;
        } // if
        
        var new_project_group_name = jQuery.trim(new_project_group_input.val());
        if(new_project_group_name == '') {
          new_project_group_input[0].focus();
        } // if
        
        // Check if new project group name is already in use
        var name_used = false;
        wrapper.find('td.name').each(function() {
          var current_row = $(this);
          if(current_row.text() == new_project_group_name) {
            name_used = true;
            current_row.highlightFade();
          } // if
        });
        
        if(name_used) {
          return false;
        } // if
        
        var old_icon_url = new_project_group_icon.attr('src');
        
        submitting_new_project_group = true;
        new_project_group_input.attr('disabled', 'disabled');
        new_project_group_icon.attr('src', App.data.indicator_url);
        
        $.ajax({
          type : 'POST',
          url : App.extendUrl(form.attr('action'), { async : 1}),
          data : {
            'submitted' : 'submitted',
            'project_group[name]' : new_project_group_name
          },
          success : function(response) {
            $('#manage_project_groups_empty_list').hide();
            
            var new_row = $(response);
            var table = wrapper.find('table');
            
            table.append(new_row).show();
            init_row(new_row);
            reindex_even_odd_rows(table);
            
            new_row.find('td').highlightFade();
            
            new_project_group_input.attr('disabled', '').val('')[0].focus();
            submitting_new_project_group = false;
            new_project_group_icon.attr('src', old_icon_url);
            
            // Add to list of project group tabs
            if(manage_project_groups_tab) {
              var new_tab = $('<li><a><span></span></a></li>');
              
              new_tab.attr('project_group_id', new_row.attr('project_group_id'));
              
              var new_project_group_link = new_row.find('td.name a');
              new_tab.find('a').attr('href', new_project_group_link.attr('href'));
              new_tab.find('span').text(new_project_group_link.text());
              
              manage_project_groups_tab.before(new_tab);
            } // if
          },
          error : function() {
            submitting_new_project_group = false;
            new_project_group_input.attr('disabled', '');
            new_project_group_icon.attr('src', old_icon_url);
            
            alert(App.lang('Failed to create new project group ":name"', { 'name' :  new_project_group_name}));
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
 * Quick add module
 */
App.system.QuickAdd = function() {
  
  /**
   * wrapper
   */
  var wrapper;
  
  /**
   * Wizzard step 1
   *
   * var jQuery
   */
  var step_1;
  
  /**
   * Wizzard step 2
   *
   * var jQuery
   */
  var step_2;
  
  /**
   * Variable that contains preloader string
   *
   * var String
   */
  var preloader_string;
  
  /**
   * Current object type
   *
   * var String
   */
  var current_object_type;
  
  /**
   * Current project id
   *
   * var integer
   */
  var current_project_id;
  
  /**
   * Tells if step 1 was already initialied
   *
   * var boolean
   */
  var step_1_initialized;

  
  // Public interface
  return {
    
    /**
     * initial initialize
     *
     * @param void
     * @return null
     */
    init : function () {
      wrapper = $('#quick_add');
      step_1 = wrapper.find('#quick_add_step_1');
      step_2 = wrapper.find('#quick_add_step_2');
      step_3 = wrapper.find('#quick_add_step_3');
      preloader_string = '<p class="quick_add_loading"><img src="' + App.data.big_indicator_url + '" alt="" /></p>';
      step_1_initialized = false;
      App.system.QuickAdd.init_step_1();
    },
    
    /**
     * Initialize Step 1 - project chooser
     *
     * @param boolean skip_initial
     * @return null
     */
    init_step_1 : function(skip_initial) {
      step_1.show();
      step_2.hide();
            
      if (App.ModalDialog.isOpen) {
        App.ModalDialog.setWidth(560);
        App.ModalDialog.setTitle(App.lang('Quick Add'));
      } // if
      
      if (!step_1_initialized) {
        if (App.ModalDialog.isOpen) {
          step_1.find('.wizzard_back').click(function () {
            App.ModalDialog.close();
            return false;
          });
        } else {
          step_1.find('.wizzard_back').hide();
        } // if
              
        step_1.find('.continue').click(function () {
          App.system.QuickAdd.init_step_2(
            project_chooser.find('input[checked=true]').val(), // project id
            object_chooser.find('input[checked=true]').val(), // object type
            object_chooser.find('input[checked=true]').attr('quick_add_url'), // quick_add url
            project_chooser.find('input[checked=true]').parent().text(), // project name
            object_chooser.find('input[checked=true]').parent().text() // translated object type
          );
          return false;
        });
        
        var project_chooser = step_1.find('#project_id:first');
        var object_chooser = step_1.find('#object_chooser:first');
        
        project_chooser.find('label').hover(function () {
          $(this).addClass('hover');
        }, function () {
          $(this).removeClass('hover');
        });
               
        project_chooser.find('input').click(function () {
          var previous_object_type = object_chooser.find('input[checked=true]:first');
          if (previous_object_type.length > 0) {
            var previous_object_type = previous_object_type.attr('value');
          } else {
            var cookie_previous_object_type = $.cookie('quick_add_object_type');
            if (cookie_previous_object_type) {
              previous_object_type = cookie_previous_object_type;
            } else {
              previous_object_type = previous_object_type;
            } // if
          } // if
          
          var radio_button = $(this);
          if(radio_button.attr('checked') == true) {
            project_chooser.find('label').removeClass('selected');
            radio_button.parent().addClass('selected');
          } // if
          
          var project_objects = App.data.quick_add_map[radio_button.val()]['permissions'];
          
          object_chooser.find('label').remove();
          for (var counter = 0; counter < project_objects.length; counter++) {
            var type_name =  project_objects[counter]['name'];
            var type_title =  project_objects[counter]['title'];
            
            var label = $('<label></label>').attr('for', 'type_' + type_name);
            var radio = $('<input type="radio" name="object_type" class="input_radio" />').attr({
              'name'          : 'object_type',
              'class'         : 'input_radio',
              'id'            : 'type_' + type_name,
              'value'         : type_name,
              'quick_add_url' : App.data.quick_add_urls[type_name]
            }).appendTo(label);
            
            label.append(ucfirst(type_title)).appendTo(object_chooser);
          } // for
          
          object_chooser.find('label').hover(function () {
            $(this).addClass('hover');
          }, function () {
            $(this).removeClass('hover');
          });
          
          object_chooser.find('input').click(function () {
            var radio_button = $(this);
            if (radio_button.attr('checked') == true) {
              object_chooser.find('label').removeClass('selected');
              radio_button.parent().addClass('selected');
            } // if       
          }).keydown(function(event) {
            if ((event.keyCode == 13) || (event.keyCode == 32)) {
              App.system.QuickAdd.init_step_2(
                project_chooser.find('input[checked=true]').val(), // project id
                object_chooser.find('input[checked=true]').val(), // object type
                object_chooser.find('input[checked=true]').attr('quick_add_url'), // quick_add url
                project_chooser.find('input[checked=true]').parent().text(), // project name
                object_chooser.find('input[checked=true]').parent().text() // translated object type
              );
            } // if
            if (event.keyCode == 9) {
              step_1.find('.wizzard_continue').focus();
            } // if
          });
          
          if (previous_object_type) {
            previous_object_type = object_chooser.find('input[value='+previous_object_type+']:first');
            if (previous_object_type.length > 0) {
              previous_object_type.attr('checked', 'checked').click();
            } else {
              object_chooser.find('input:first').attr('checked', 'checked').click();
            } // if
          } else {
            object_chooser.find('input:first').attr('checked', 'checked').click();
          } // if
          
        }).keydown(function(event) {
          if ((event.keyCode == 13) || (event.keyCode == 32)) {
            object_chooser.find('input[checked=true]').focus();
          } // if
        });
        
        if (!skip_initial) {
          var initial_project;
          if (App.data.active_project_id && (App.data.active_project_id > 0)) {
            initial_project = project_chooser.find('#quickadd_project_'+App.data.active_project_id);
          } else {
            initial_project = project_chooser.find('input:first');
          } // if
          initial_project.attr('checked', 'checked').focus().click();
        } // if
        step_1_initialized = true;
      } // if
    },
    
    /**
     * Initialize Step 2 - quick add form
     *
     * @param integer project_id
     * @param string object_type
     * @return null
     */
    init_step_2 : function (project_id, object_type, object_add_url, project_name, object_translated_type) {
      step_1.hide();
      step_2.show();
      
      current_project_id = project_id;
      current_object_type = object_type;
      
      if (!object_translated_type) {
        object_translated_type = object_type
      } // if
      
      $.cookie('quick_add_object_type', object_type);
      
      object_add_url = object_add_url.replace(/\-PROJECT\-ID\-/, project_id);
           
      step_2.html(preloader_string);
      var preloader = step_2.find('p.quick_add_loading');
      
      $.ajax({
        url : object_add_url,
        success : function (response) {
          step_2.html(response);
          if (App.ModalDialog.isOpen) {
            App.ModalDialog.setTitle(App.lang('Quick Add :object_type in :project_name', {'object_type' : ucfirst(object_translated_type), 'project_name' : project_name}));
          } // if
          App.system.QuickAdd.init_object_form();
        },
        error : function (response) {
          App.system.QuickAdd.init_step_1(true);
        }
      });
    },
    
    init_object_form : function () {
      var object_form = step_2.find('form');
      object_form.find('input:first').focus();
      
      // flush behaviour
      step_2.find('.flash').click(function () {
        $(this).hide('fast');
      });
      
      // back button
      step_2.find('.wizzard_back').click(function () {
        App.system.QuickAdd.init_step_1(true);
        return false;
      });
      
      object_form.submit(function () {
        step_2.find('.flash').hide();
        step_2.prepend(preloader_string);
        var preloader = wrapper.find('.quick_add_loading');
        object_form.hide();

        object_form.ajaxSubmit({
          success : function (response) {
            step_2.html(response);
            App.system.QuickAdd.init_object_form();
          },
          error : function (response) {
            object_form.show();
            preloader.remove();
            alert(response.responseText);
          }
        });

        return false;
      });
    }
  };
  
}();

/**
 * String list behavior
 */
App.system.StringList = function() {
  
  /**
   * Init specific row
   *
   * @param jQuery row
   * @return void
   */
  var init_row = function(row) {
    var wrapper = row.parent().parent();
    
    row.find('td.remove a').click(function() {
      row.remove();
      reindex_row_data(wrapper);
    });
  };
  
  /**
   * Reindex row numbers
   *
   * @param jQuery wrapper
   * @return void
   */
  var reindex_row_data = function(wrapper) {
    var counter = 1;
    wrapper.find('tr.item').each(function() {
      var row = $(this);
      row.removeClass('even').removeClass('odd');
      if((counter % 2) > 0) {
        row.addClass('odd');
      } else {
        row.addClass('even');
      } // if
      row.find('td.num').text('#' + counter);
      
      counter++;
    });
  };
  
  /**
   * Create a new list item
   *
   * @param jQuery wrapper
   * @param string item_title
   * @param string input_name
   * @return void
   */
  var add_item_to_the_list = function(wrapper, item_title, input_name) {
    if(item_title == '') {
      return false;
    } else if(jQuery.trim(item_title).length < 3) {
      alert(App.lang('Project group name should be at least 3 characters long'));
      return false;
    } // if
    
    var exists = false;
    
    // Check if value already exists
    wrapper.find('tr.item').each(function() {
      var row = $(this);
      
      if(row.find('input[type=hidden]').val().toLowerCase() == item_title.toLowerCase()) {
        exists = true;
        row.find('td.value').highlightFade();
      }
    });
    
    // Add an item
    if(exists) {
      return false;
    } else {
      var row = $('<tr class="item">' +
        '<td class="num">#</td>' +
        '<td class="value"><span></span> <sup>' + App.lang('Unsaved') + '</sup> <input type="hidden" /></td>' +
        '<td class="remove"><a href="javascript: return false;"><img src="' + App.data.assets_url + '/images/gray-delete.gif" alt="" /></a></td>' +
      '</tr>');
      
      row.find('td.value span').text(item_title);
      row.find('input[type=hidden]').val(item_title).attr('name', input_name + '[]');
      
      wrapper.find('table').append(row);
      
      init_row(row);
      reindex_row_data(wrapper);
      
      row.find('td').highlightFade();
      
      return true;
    } // if
  };
  
  // Public interface
  return {
    
    /**
     * Initialize string list
     *
     * @param string wrapper_id
     * @return void
     */
    init : function(wrapper_id, name) {
      var wrapper = $('#' + wrapper_id);
      
      var add_item_input = wrapper.find('input.add_list_item_name');
      
      var default_add_item_input_text = add_item_input.val();
      
      var handle_submission = function() {
        add_item_to_the_list(wrapper, add_item_input.val(), name);
        add_item_input.val('');
        return false;
      };
      
      wrapper.find('input.add_list_item_button').click(function(e) {
        if(add_item_input.val() == default_add_item_input_text) {
          add_item_input[0].focus();
        } else {
          handle_submission();
        } // if
        e.preventDefault();
      });
      
      add_item_input.keypress(function(e) {
        if(e.which == 13) {
          handle_submission();
          e.preventDefault();
        } // if
      });
      
      // Focus / blur behavior
      add_item_input.focus(function() {
        if(add_item_input.val() == default_add_item_input_text) {
          add_item_input.val('');
        }
      }).blur(function() {
        if(add_item_input.val() == '') {
          add_item_input.val(default_add_item_input_text);
        }
      });
      
      // Init rows
      wrapper.find('tr.item').each(function() {
        init_row($(this));
      });
    }
    
  };
  
}();

/**
 * Dashboard sections behavior implementation
 */
App.widgets.IconPicker = function() {
  
  /**
   * icon_container
   *
   * @var jQuery
   */
  var icon_container;
  
  /**
   * icon_container
   *
   * @var jQuery
   */
  var picker_url;
  
  // Public interface
  return {
    
    /**
     * Initialize icon picker button
     *
     */
    init : function (icon_picker, icon_to_update, icon_title) {
      icon_container = $('#' + icon_to_update);
      picker_url = App.extendUrl(icon_container.attr('href'), {async : 1, skip_layout : 1});
            
      icon_container.click(function () {
        App.ModalDialog.show('icon_picker', icon_title, $('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Loading...') + '</p>').load(picker_url, function () {
          App.widgets.IconPicker.upload_form(icon_picker, icon_to_update);
        }), {
          buttons : false
        });
        return false;
      });
    },
    
    /**
     *  initialize upload icon form
     *
     */
    upload_form : function (container_id, icon_to_update) {
      var upload_container = $('#'+container_id);
      var upload_container_form = upload_container.find('form');
      
      if (icon_to_update) {
        $('#'+icon_to_update+' img').attr('src', upload_container.find('#updated_icon img').attr('src'));
      } // if
      
      // submit upload form assync
      upload_container_form.submit(function () {
        upload_container.block();
        upload_container_form.ajaxSubmit({
          url : App.extendUrl(upload_container_form.attr('action'), {async : 1, skip_layout : 1}),
          type : 'post',
          success : function(response) {
            $('#'+icon_to_update+' img').attr('src', $(response).find('#updated_icon img').attr('src'));
            App.ModalDialog.close();
          },
          error : function (response) {
            upload_container.unblock();
            alert(response.statusText);
          }
        })
        return false;
      });
          
      // delete icon
      upload_container_form.find('.details a.delete_current:eq(0)').click(function () {
        var delete_url = App.extendUrl($(this).attr('href'), {async : 1, skip_layout : 1});
        upload_container.block();
        $.ajax({
          url: delete_url,
          type: 'post',
          data: 'submitted=submitted',
          success : function (response) {
            eval ('response = ' + response);
            if (typeof response == 'object') {
              $('#'+icon_to_update+' img').attr('src', response.icon);
              App.ModalDialog.close();
            } // if
          }
        });
        return false;
      });
    }
    
  }
}();

/**
 * Mark all new objects as read
 */
App.system.MarkAllAsRead = function() {
  
  // Public interface
  return {
    
    /**
     * Mark all new object as read
     *
     * @param String wrapper_id
     */
    init : function(wrapper_id) {
      var wrapper = $('#' + wrapper_id);
      
      wrapper.find('#mark_all_read_link').click(function () {
        var anchor = $(this);
        
        wrapper.hide();
        wrapper.before('<p class="dashboard_sections_loading" id="new_since_last_visit_preloader"><img src="' + App.data.big_indicator_url + '" alt="" /></p>')
        
        $.ajax({
          type: 'post',
          data: { 
            'submitted' : 'submitted' 
          },
          url: App.extendUrl(anchor.attr('href'), {
            async : 1
          }),
          success : function () {
            $('#dashboard_section_recent_activities a:first').click();
            $('#dashboard_section_new_updated').remove();
            wrapper.remove();
          },
          error : function () {
            $('#new_since_last_visit_preloader').remove();
            wrapper.show();
          }
        })
        return false;
      });
    }
  }
  
}();

/**
 * Send welcome message behavior
 */
App.system.SendWelcomeMessage = function() {
  
  // Public interface
  return {
    
    /**
     * Initialize welcome message form
     *
     * @param String wrapper_id
     */
    init : function(wrapper_id) {
      var wrapper = $('#send_welcome_message');
      var form = wrapper.find('form');
      
      form.submit(function() {
        form.block('Sending...');
      
        $.ajax({
          url : App.extendUrl(form.attr('action'), { async : 1 }),
          type : 'post',
          data : {
            'submitted' : 'submitted',
            'welcome_message[message]' : form.find('textarea').val()
          },
          success : function(response) {
            form.unblock();
            wrapper.empty().append($('<p></p>').text(response));
          },
          error : function(response) {
            form.unblock();
            wrapper.empty().append($('<p></p>').text(App.lang('Failed to send welcome message. Please try again later')));
          }
        });
        
        return false;
      });
    }
    
  };
  
}();

/**
 * Role permission value behavior
 */
App.system.RolePermissionValue = function() {
  
  // Public interface
  return {
    
    /**
     * Initialize role permission value behavior for a single checkbox
     *
     * @param String checkbox_id
     */
    init : function(checkbox_id) {
      var checkbox = $('#' + checkbox_id);
      var cell = checkbox.parent();
      
      checkbox.click(function() {
        checkbox.hide();
        cell.append('<img src="' + App.data.indicator_url + '" />');
        
        $.ajax({
          url  : App.extendUrl(checkbox.attr('set_permission_value_url'), { async : 1 }),
          type : 'POST',
          data : {
            submitted : 'submitted',
            value : this.checked ? 1 : 0
          },
          success : function() {
            cell.find('img').remove();
            checkbox.show();
            return true;
          },
          error : function() {
            cell.find('img').remove();
            checkbox.show();
            
            alert(App.lang('Failed to set value of this role'));
            
            return false;
          }
        });
      });
    }
    
  };
  
}();