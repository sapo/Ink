var App = window.App || {};

/** We will put all of our variables and resources (URL-s, listings etc) **/
App.data = {};

// All widgets should be defined here
App.widgets = {};

/**
 * Send post request to specific link
 *
 * @param string the_link
 */
App.postLink = function(the_link) {
  var form = $(document.createElement('form'));
  form.attr({
    'action' : the_link,
    'method' : 'post'
  });
  
  var submitted_field = $(document.createElement('input'));
  submitted_field.attr({
    'type'  : 'hidden',
    'name'  : 'submitted',
    'value' : 'submitted'
  });
  
  form.append(submitted_field);
  
  $('body').append(form);
  
  form.submit();
  return false;
};

/**
 * Convert & -> &amp; < -> &lt; and > -> &gt;
 *
 * @param str
 * @return string
 */
App.clean = function(str) {
  if(typeof(str) == 'string') {
    str = str.replace(/&/g, '&amp;');
    str = str.replace(/\>/g, '&gt;');
    str = str.replace(/\</g, '&lt;');
  }
  
  return str;
};

/**
 * JS version of lang function / helper
 *
 * @param string content
 * @param object params
 */
App.lang = function(content, params) {
  var translation = content;
  
  if(typeof(App.langs) == 'object') {
    if(App.langs[content]) {
      translation = App.langs[content];
    }
  }
  
  if(typeof params == 'object') {
    for(key in params) {
      translation = translation.replace(':' + key, App.clean(params[key]));
    } // if
  } // if
  return translation;
};

/**
 * JavaScript implementation of isset() function
 *
 * Usage example:
 *
 * if(isset(undefined, true) || isset('Something')) {
 *   // Do stuff
 * }
 *
 * @param value
 * @return boolean
 */
App.isset = function(value) {
  return !(typeof(value) == 'undefined' || value === null);
};

/**
 * Add async variables to async link
 *
 * @param string link
 * @return string
 */
App.makeAsyncUrl = function(link) {
  if (link) {
    if (link.indexOf('?') < 0) {
      link += '?async=1&skip_layout=1'
    } else {
      link += '&async=1&skip_layout=1'
    } // if
    return link;
  } else {
    return false;
  }
};

/**
 * Convert MySQL formatted datetime string to Date() object
 *
 * @params String timestamp
 * @return Date
 */
App.mysqlToDate = function(timestamp) {
  var regex=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
  var parts=timestamp.replace(regex, "$1 $2 $3 $4 $5 $6").split(' ');
  return new Date(parts[0], parts[1], parts[2], parts[3], parts[4], parts[5]);
};

/**
 * Attach more parameters to URL
 *
 * @param string url
 * @param object extend_with
 */
App.extendUrl = function(url, extend_with) {
  if(!url || !extend_with) {
    return url;
  } // if
  
  var extended_url = url;
  var parameters = [];
  
  extended_url += extended_url.indexOf('?') < 0 ? '?' : '&';
  
  for(var i in extend_with) {
    if(typeof(extend_with[i]) == 'object') {
      for(var j in extend_with[i]) {
        parameters.push(i + '[' + j + ']' + '=' + extend_with[i][j]);
      } // for
    } else {
      parameters.push(i + '=' + extend_with[i]);
    } // if
  } // for
  
  return extended_url + parameters.join('&');
};

/**
 * Parse numeric value and return integer or float
 *
 * @param String value
 * @return mixed
 */
App.parseNumeric = function(value) {
  if(typeof(value) == 'number') {
    return value;
  } else if(typeof(value) == 'string') {
    if(value.indexOf('.') > -1) {
      var separator = '.';
    } else if(value.indexOf(',') > -1) {
      var separator = ',';
    } else {
      return value == '' ? 0 : parseInt(value);
    } // if
       
    var separator_pos = value.indexOf(separator);
    var whole_number = parseInt(value.substring(0, separator_pos));
    var decimal = parseFloat('0.' + value.substring(separator_pos + 1));    

    return value.indexOf('-', 0) ? whole_number + decimal : whole_number - decimal;
  } else {
    return NaN;
  }
};

/**
 * Parse string and return version object
 *
 * @param String str
 * @return Object
 */
App.parseVersionString = function (str) {
    if (typeof(str) != 'string') { return false; }
    var x = str.split('.');
    // parse from string or default to 0 if can't parse
    var maj = parseInt(x[0]) || 0;
    var min = parseInt(x[1]) || 0;
    var pat = parseInt(x[2]) || 0;
    return {
        major: maj,
        minor: min,
        patch: pat
    }
}; // parseVersionString

/**
 * compare versions, if they are same returns 0, if first is lower returns -1, and
 * if second is lower returns 1
 *
 * @var string version1
 * @var string version2
 * @return int
 */
App.compareVersions = function (version1, version2) {
  version1 = App.parseVersionString(version1);
  version2 = App.parseVersionString(version2);
    
  if (version1.major < version2.major) {
    return -1;
  } else if (version1.major > version2.major) {
    return 1;
  } else {
    if (version1.minor < version2.minor) {
      return -1;
    } else if (version1.minor > version2.minor) {
      return 1;
    } else {
      if (version1.patch < version2.patch) {
        return -1;
      } if (version1.patch > version2.patch) {
        return 1;
      } else {
        return 0;
      } // if
    } // if
  } // if
} // compareVersions

jQuery.fn.highlightFade = function() {
  return this.effect("highlight", {}, 1000)
};

function ucfirst( str ) {
  str += '';
  var f = str.charAt(0).toUpperCase();
  return f + str.substr(1);
}

// Do stuff that we need to do on every page...
$(document).ready(function() {
  App.layout.init();
  App.RefreshSession.init();
  App.PrintPreview.init();
  App.widgets.SendReminder.init();
});

/** Layout **/
App.layout = function() {
  
  // Result
  return {
  
    /**
     * Initialize layout
     */
    init : function() {
      // Preload indicator...
      var indicator = new Image();
      indicator.src = App.data.indicator_url;
      
      // Jump to project button
      var project_menu_item = $('#menu_item_projects');
      project_menu_item.append('<span class="additional"><a href="' + App.data.jump_to_project_url + '"><span>' + App.lang('Jump to Project') + '</span></a></span>');
      project_menu_item.find('span.additional a').click(function() {
        App.ModalDialog.show('jump_to_project', App.lang('Jump to Project'), $('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Loading...') + '</p>').load(App.data.jump_to_project_url), {});
        return false;
      });
      
      // Search button
      var search_menu_item = $('#menu_item_search a').click(function() {
        var quick_search_url = App.extendUrl($(this).attr('href'), { 
          skip_layout : 1, 
          async : 1 
        });
        App.ModalDialog.show('quick_search', App.lang('Quick Search'), $('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Loading...') + '</p>').load(quick_search_url), {
          buttons : false
        });
        return false;
      });
      
      $('#page_actions .with_subitems>a').click(function() {
        return false;
      });
      
      // Quick add button
      $('#menu_item_quick_add a').click(function() {
        var url = App.extendUrl(App.data.quick_add_url, { skip_layout : 1});
        
        App.ModalDialog.show('quick_add', App.lang('Quick Add'), $('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Loading...') + '</p>').load(url), {
          buttons : false,
          width: 560
        });
        return false;
      });
      
      // Flash
      $('#success, #error').click(function() {
        $(this).hide('fast');
      });
      
      // Hoverable
      $('.hoverable').hover(function() {
        $(this).addClass('hover');
      }, function() {
        $(this).removeClass('hover');
      });
      
      // Card
      $('.card div.options').each(function() {
        wrapper = $(this);
        var first_list_item = wrapper.find('li.first');
        wrapper.find('a').hover(function() {
          first_list_item.text($(this).attr('title'));
        }, function() {
          first_list_item.html('&nbsp;');
        });
      });
      
      // Scale big images in object description blocks
      $('div.body.content').scaleBigImages();
      
      $('.button_dropdown').each(function () {
        var dropdown_button = $(this);
        var dropdown_menu = dropdown_button.find('.dropdown_container');
        dropdown_button.hover(function () {
          
        }, function () {
          dropdown_menu.fadeOut(100);
        }).click(function () {
          if (dropdown_menu.is(':visible')) {
            dropdown_menu.fadeOut(100);  
          } else {
            dropdown_menu.fadeIn(100);
          } // if
        });
      });
    },
    
    /**
     * Init star unstar link
     *
     * @param string id
     * @return null
     */
    init_star_unstar_link : function(id) {
      $('#' + id).click(function() {
        var link = $(this);
        var parent = link.parent();
      
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
          url     : App.extendUrl(link.attr('href'), { async : 1 }),
          type    : 'POST',
          data    : {'submitted' : 'submitted'},
          success : function(response) {
            parent.empty();
            parent.append(response);
          },
          error   : function() {
            img.attr('src', old_src);
          }
        });
        
        return false;
      });
    },
    
    /**
     * Complete / reopen task
     *
     * @param string id
     */
    init_complete_open_link : function(id) {
      $('#' + id).click(function() {
        var link = $(this);
        var parent = link.parent();
      
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
          url     : App.extendUrl(link.attr('href'), { async : 1 }),
          type    : 'POST',
          data    : {'submitted' : 'submitted'},
          success : function(response) {
            parent.empty();
            parent.append(response);
          },
          error   : function() {
            img.attr('src', old_src);
          }
        });
        
        return false;
      });
    },
    
    /**
     * Initialize subscribe / unsubscribe link
     *
     * @param string wrapper_id
     * @return null
     */
    init_subscribe_unsubscribe_link : function(wrapper_id) {
      $('#' + wrapper_id + ' a').click(function(e) {
        var link = $(this);
        var parent = link.parent();
      
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
          url     : App.extendUrl(link.attr('href'), { async : 1 }),
          type    : 'POST',
          data    : {'submitted' : 'submitted'},
          success : function(response) {
            parent.empty();
            parent.append(response);
          },
          error   : function() {
            img.attr('src', old_src);
          }
        });
        
        return false;
      });
    },
    
    /**
     * Reindex opened tasks table, change colors of rows, and display hidden row if necessarry
     *
     * @param string table
     * @return null
     */
    reindex_task_table: function (table) {
      table = $(table);
      var counter = 0;
      table.find('li:not(.empty_row):not(.ui-sortable-helper):not(.sort_placeholder)').each(function() {
        row = $(this);
        if ((counter % 2) == 1) {
          row.removeClass('odd');
          row.addClass('even');
        } else {
          row.removeClass('even');
          row.addClass('odd');
        } // if
        counter++;
      });     
      
      if (counter<1) {
        table.find('.empty_row').show();
      } else {
        table.find('.empty_row').hide();
      } // if
    },
    
    /**
     * Init row in tasks table
     *
     * @param object row
     * @param object wrapper
     */
    init_object_task: function (row, wrapper) {
      if (wrapper.drag_enabled==true) {
        row.find('.drag_handle').show();
      } else {
        row.find('.drag_handle').hide();
      }
      
      // complete task
      row.find('a.complete_task').click(function() {
        var link = $(this);
        var complete_tasks_table = link.parents('.object_tasks').find('.completed_tasks_table');
        
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
            var response_obj = $(response);
            var open_tasks_table = link.parents('.object_tasks').find('.tasks_table');
            complete_tasks_table.prepend(response_obj);
            row.remove();
            App.layout.init_object_task(response_obj,wrapper);
            App.layout.reindex_task_table(open_tasks_table);
          },
          error   : function() {
            img.attr('src', old_src);
          }
        });
        
        return false;
      });
      
      // open task
      row.find('a.open_task').click(function() {
        var link = $(this);
        var open_tasks_table = link.parents('.object_tasks').find('.open_tasks_table');
        
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
            var response_obj = $(response);
            open_tasks_table.append(response_obj);
            row.remove();
            App.layout.init_object_task(response_obj,wrapper);
            App.layout.reindex_task_table(open_tasks_table);
          },
          error   : function() {
            img.attr('src', old_src);
          }
        });
        
        return false;
      });
      
      // Remove buttons
      row.find('a.remove_task').click(function() {
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
          url     : App.extendUrl(link.attr('href'), {'async' : 1}),
          type    : 'POST',
          data    : {'submitted' : 'submitted'},
          success : function() {
            row.remove();
          },
          error   : function() {
            img.attr('src', old_src);
          }
        });
        
        return false;
      });
    },
    
    /**
     * Initialize tasks table
     *
     * @param string wrapper_id ID of wrapper div
     */
    init_object_tasks : function(wrapper_id, enable_reordering) {
        var wrapper = $('#' + wrapper_id);
        var form_wrapper = wrapper.find('div.add_task_form');
        var show_form = wrapper.find('a.add_task_link');
        var hide_form = wrapper.find('a.cancel_button');
        var active_tasks_table = wrapper.find('.tasks_table.open_tasks_table');
        
        form_wrapper.find('.show_due_date_and_priority a').click(function () {
          $(this).parent().hide();
          form_wrapper.find('.due_date_and_priority').slideDown();
          return false;
        });
        form_wrapper.find('.due_date_and_priority').hide();
                
        // Submit add task form
        form_wrapper.find('form').submit(function() {
          var form = $(this);
          if(UniForm.is_valid(form)) {       
            var old_form_action = form.attr('action');
            
            form.attr('action', App.extendUrl(old_form_action, { async : 1 }));
            
            var loading_row = '<li><img src="' + App.data.indicator_url + '" alt="loading" /> <strong>' + App.lang('Working') + '</strong></li>';
            active_tasks_table.append(loading_row);
            var temp_row = active_tasks_table.find('li:last');
          
            // submit form via ajax
            form.ajaxSubmit({
              success : function(response) {
                var response_obj = $(response);
                // insert real row in table
                response_obj.insertAfter(temp_row);
                // remove fake row
                temp_row.remove();
                App.layout.init_object_task(response_obj, wrapper);
                App.layout.reindex_task_table(active_tasks_table);
              },
              error : function (response) {
                // remove fake row
                temp_row.remove();
              }
            });
            
            // empty task message
            form.attr('action', old_form_action).find('input:first').val('').focus();
          } // if
          
          return false;
        });
        
        // Show task form
        show_form.click(function() {
          show_form.hide();
          $('.main_object .resource.object_tasks').show();
          form_wrapper.show().focusFirstField();
          return false;
        });
        
        $('#object_quick_option_new_task a').click(function () {
          show_form.hide();
          $('.main_object .resource.object_tasks').show();
          form_wrapper.show().focusFirstField();
          return false;
        });
        
        // Hide task form
        hide_form.click(function() {
          show_form.show();
          form_wrapper.clearErrorMessages().hide();
          form_wrapper.find('input:eq(0)').val('');
          return false;
        });
        
        form_wrapper.find('input').keypress(function(e) {
          if (e.keyCode == 27) {
            hide_form.click();
          } // if
        });
        
        if (enable_reordering > 0) {
          // init sortable behvaiour
          wrapper.find('.open_tasks_table').sortable({
            axis : 'y',      
            cursor: 'move',
            items: 'li.sort',
            delay: 3,
            revert: false,
            connectWith: ['.open_tasks_table'],
            tolerance : 'pointer',
            placeholder: 'sort_placeholder',
            forcePlaceholderSize : false,
            update: function (e, ui) {
              var sort_form = $(this).parents('form.sort_form');
              ui.item.parent().attr('style','');
              sort_form.ajaxSubmit({
                method : 'POST'
              });
              App.layout.reindex_task_table($(this));
            },
            over: function (table_object,ui) {
              $(this).addClass('dragging');
            },
            out: function (table_object,ui) {
              $(this).removeClass('dragging');
            },
            receive : function (event, ui) {
              App.layout.reindex_task_table($(this));
            },
            remove : function (event, ui) {
              App.layout.reindex_task_table($(this));
            }
          });
        } // if
               
        // init every row in table
        wrapper.find('.tasks_table li, .completed_tasks_table li').each(function () {
          App.layout.init_object_task($(this), wrapper);
        });
        
        // 'view all completed' behaviour
        wrapper.find('.completed_tasks_table li.list_all_completed a').click(function () {
          var anchor = $(this);
          var completed_tasks_table = anchor.parents('ul.completed_tasks_table:first');
          anchor.after('<span class="loading"><img src="' + App.data.indicator_url + '" alt="" />' + App.lang('Loading...') + '</a>');
          var loading_block = anchor.parent().find('.loading:first');
          anchor.hide();
          
          $.ajax({
            url : App.extendUrl(anchor.attr('href'), {async : 1, skip_layout : 1}),
            success : function (response) {
              completed_tasks_table.html(response);
              completed_tasks_table.find('li').each(function () {
                App.layout.init_object_task($(this), wrapper);
              });
            },
            error : function () {
              loading_block.remove();
              anchor.show();   
            }
          });
          return false;
        });
    }
  
  } // init
  
}();

/**
 * Modal dialog module
 */
App.ModalDialog = function() {
  
  /**
   * Current dialog reference
   *
   * @var jQuery
   */
  var dialog_object;
  
  // Let's return public interface object
  return {
    
    /**
     * Show modal dialog
     *
     *
     * @param String name
     * @param String title
     * @param mixed body
     * @param mixed settings
     */
    show : function(name, title, body, settings) {
      // dialog options
      var options = {
        modal     : true,
        draggable : false,
        resizable : true,
        title     : title,
        id        : name,
        position  : 'top',
        bgiframe  : true,
        close     : function (type,data) {
          if (settings.close) {
            settings.close();
          } // if
          dialog_object.dialog('destroy').remove();
        },
        resizeStart : function (type,data) {

        }
      };

      if (settings) {
        // width and height settings
        options.width = settings.width ? settings.width : 410;
        options.height = settings.height ? settings.height : 'auto';        
        // additional buttons
        options.buttons = {};
        if (settings && settings.buttons) {
          for (var x = 0; x < settings.buttons.length; x++) {
            if (settings.buttons[x].callback) {
              var callback_function = settings.buttons[x].callback;
              options.buttons[settings.buttons[x].label] = function () {
                callback_function();
                dialog_object.dialog('close');
              } // function
            } else {
              options.buttons[settings.buttons[x].label] = function () {
                dialog_object.dialog('close');
              } // function
            } // if
          } // if
        } // if
      } // if

      options.maxWidth = options.width;
      options.minWidth = options.width;
      
      dialog_object = $(body).dialog(options);
     
      var counter = 0;
      dialog_object.parent().parent().find('.ui-dialog-buttonpane button').each(function () {
        var button = $(this);
        button.removeClass('ui-state-default').removeClass('ui-corner-all');

        var label = button.html();
        button.html('<span><span>' + label + '</span></span>');
        if (counter != 0) {
          button.addClass('alternative');
        } // if
        counter++;
      });
    },
    
    /**
     * Close the dialog
     */
    close : function() {
      dialog_object.dialog('destroy').remove();
    },
    
    /**
     * sets width of dialog
     */
    setWidth : function (width_px) {     
      var dom_dialog = $('.ui-dialog');
      var position = dom_dialog.position();
      var new_left_offset = position.left - ((width_px - dom_dialog.width())/2);
      dom_dialog.css('width' , width_px+'px').css('left', new_left_offset+'px');
    },
    
    /**
     * Sets dialog title
     */
    setTitle : function (title) {
     var dom_dialog = $('.ui-dialog .ui-dialog-titlebar span.ui-dialog-title').html(title);
    },
    
    /**
     * Checks if dialog is open
     */
    isOpen : function () {
      if ($('.ui-dialog').length > 0) {
        return true;
      } else {
        return false;
      }
    }
  };
  
}();


/**
 * Print preview module
 */
App.PrintPreview = function() {
  /**
   * Dom element of main css
   *
   * @var jQuery
   */
  var css_main;
  /**
   * Dom element of theme css
   *
   * @var jQuery
   */
  var css_theme;
  /**
   * Dom element of css preview
   *
   * @var jQuery
   */
  var css_print_preview;
  
  // Return value
  return {
    
    /**
     * Initialize print preview behavior
     *
     * @param void
     * @return null
     */
    init : function() {
      $('#print_button').click(function(e) {
        App.PrintPreview.open();
        e.stopPropagation();
        return false;
      });
      
      $('#print_preview_header #print_preview_close').click(function() {
        App.PrintPreview.close();
        return false;
      });
      
      $('#print_preview_header #print_preview_print').click(function() {
        window.print();
        return false;
      });
      
      css_main = $('#style_main_css');
      css_theme = $('#style_theme_css');
      css_print_preview = $('#print_preview_css');
    },
    
    /**
     * Show print preview view
     *
     * @param void
     * @return null
     */
    open : function() {
        css_main.attr('disabled', true);
        css_theme.attr('disabled', true);
        
        if ($.browser.msie == true) {
          $('#print_preview_css').each(function () {
            // please don't ask me why i did this stupendity
            this.disabled = false;
            this.disabled = true;
            this.disabled = false;
          });
        } else {
          $('#print_preview_css').attr('rel','stylesheet').each(function () {
            this.disabled = false;
          });
        } // if
    },

    /**
     * Close print preview view
     *
     * @param void
     * @return null
     */
    close : function() {
        css_main.attr('disabled', false);
        css_theme.attr('disabled', false);
        if ($.browser.msie == true) {
          css_print_preview.each(function () {
            // please don't ask me why i did this stupendity
            this.disabled = true;
            this.disabled = false;
            this.disabled = true;
          });
        } else {
          css_print_preview.attr('rel','stylesheet').each(function () {
            this.disabled = true;
          });
        } // if
    }
    
  };
  
}();

/**
 * Comment options behavior
 */
App.CommentOptions = function() {
  
  /**
   * Result
   */
  return {
    
    /**
     * Initialize
     *
     * @param string wrapper_id ID of warpper div
     * @return void
     */
    init : function(wrapper_id) {
      $('#' + wrapper_id).each(function() {
        var wrapper = $(this);
        var first_element = wrapper.find('li.comment_options_first');
        
        wrapper.find('a, span').hover(function() {
          first_element.html($(this).attr('title'));
        }, function() {
          first_element.html('&nbsp;');
        });
      });
    } // init
    
  }
  
}();

App.EmailObject = function() {
  return {
    init : function (object_id) {
      var email_object = $('#'+object_id);
      var blockquotes = email_object.find('>blockquote');
      blockquotes.each(function () {
        var blockquote = $(this);
        if (!blockquote.parent().is('div.content')) {
          blockquote = blockquote.parent();
        } // if
        blockquote.before('<a href="#" class="hidden_history">' + App.lang('Hidden Email History') + '</a>');
        blockquote.hide();
        var blockquote_anchor = blockquote.prev();
        
        blockquote_anchor.click(function () {
          blockquote.slideDown();
          $(this).remove();
          return false;
        });
      });
    }
  }
}();

// Refresh session requests
App.RefreshSession = function() {
  
  /**
   * Interval object used to call refresh function
   */
  var refresh_interval = null;
  
  // Return value
  return {
    
    /**
     * Initialize refresh interval
     *
     * @params void
     * @return void
     */
    init : function() {
      if(App.data.keep_alive_interval > 0) {
        refresh_interval = setInterval('App.RefreshSession.refresh()', App.data.keep_alive_interval);
      } // if
    },
    
    /**
     * Function used to refresh session
     *
     * @param void
     * @return null
     */
    refresh : function() {
      $.ajax({
        url : App.data.refresh_session_url
      });
    }
  }
  
}();

/**
 * Quick search module
 */
App.QuickSearch = function() {
  
  // Public interface
  return {
    
    /**
     * Initialize quick search form
     *
     * @param void
     * @return undefined
     */
    init : function() {
      $('#quick_search_form').submit(function() {
        $('#quick_search_button').hide();
        $('#quick_search_indicator').show();
        
        var form = $(this);
        var results = $('#quick_search_results');
        
        results.empty();
        
        $.ajax({
          type : 'POST',
          url : App.extendUrl(form.attr('action'), { async : 1}),
          data : {
            submitted : 'submitted',
            search_for : $('#quick_search_input').val(),
            search_type : $('#quick_search_type').val()
          },
          success : function(response) {
            results.append(response);
            
            $('#quick_search_button').show();
            $('#quick_search_indicator').hide();
          }
        });
        return false;
      });
      
      $('#quick_search_form ul li').click(function() {
        var list_element = $(this);
        
        $('#quick_search_form ul li').removeClass('selected');
        list_element.addClass('selected');
        
        $('#quick_search_type').val(list_element.attr('id').substr(7));
      });
      
      $('#quick_search_form #quick_search_input')[0].focus();
    }
  };
  
}();

/**
 * Functions for main menu
 */
App.MainMenu = function() {
  var menu
  
  // Public interface
  return {
    
    /**
     * Initialize main menu
     *
     * @param void
     * @return undefined
     */
    init : function(menu_id) {
      menu = $('#'+menu_id);
    },
    
    /**
     * add item to menu
     *
     *  @param object item
     *  @param string group_id
     *  @return null
     */
    addToGroup: function (item, group_id) {
      var button_class = 'last';
      
      var group = $('#menu_group_'+group_id, menu);
      if (group.length > 0) {
        var button_text = "<li id='menu_item_" + item.id + "' class='item " + button_class + "'>";
        button_text +=    "<a class='main' href='" + item.href + "'><span class='outer'>";
        button_text +=    "<span style='background-image: url(" + item.icon + ");' class='inner'>";
        if (item.badge_value > 0) {
          button_text +=    "<span class='badge'>" + item.badge_value + "</span>"
        } // if
        button_text +=    item.label;
        button_text +=    "</span>";
        button_text +=    "</span></a>";
        button_text +=    "</li>";
        $('li.item:last', group).removeClass('last').removeClass('single').addClass('middle');
        group.append(button_text);
      } // if
    },
    
    /**
     * Check if item with id item exists in group with group_id
     *
     *  @param string item
     *  @param string group_id
     *  @return bolean
     */
    itemExists: function (item_id, group_id) {
      var group = $('#menu_group_'+group_id, menu);
      if (group.length > 0) {
        var menu_item = $('#menu_item_' + item_id, group);
        if (menu_item.length > 0) {
          return true;
        } // if
      };
      return false;
    },
    
    /**
     * Remove item if exists
     *
     *  @param string item
     *  @param string group_id
     *  @return bolean
     */
    removeButton: function (item_id, group_id) {
      var group = $('#menu_group_'+group_id, menu);
      if (group.length > 0) {
        var previous_class = 'last'
        if ($('li', group).length <= 2) {
          previous_class = 'single';
        } // if
        $('#menu_item_' + item_id, group).remove();
        $('li:last', group).removeClass('middle').addClass(previous_class);
      };
    },
    
    /**
     * Set badge value for item
     *
     *  @param string item
     *  @param string group_id
     *  @param string badge_value
     *  @return bolean
     */
    setItemBadgeValue: function (item_id, group_id, badge_value) {
      var group = $('#menu_group_'+group_id, menu);
      if (group.length > 0) {
        var menu_item = $('#menu_item_' + item_id, group);
        if (menu_item.length) {
          if (badge_value > 0) {
            var badge = $('span.badge', menu_item);
            if (badge.length > 0) {
              badge.text(badge_value);
            } else {
              $('a>span>span', menu_item).prepend('<span class="badge">' + badge_value + '</span>');
            } // if
            return true;
          } else {
            $('span.badge', menu_item).remove();
            return true;
          } // if
        } // if
      } // if
      return false;
    }
  };
}();

/*
App.Menu = function() {
  
  return {
    set_badge_value : function(item_id, value) {     
      if(value > 0) {
        var parent = $('#' + item_id + '>a>span>span');
        var badge = parent.find('span.badge');
        if(badge.length > 0) {
          badge.text(value);
        } else {
          parent.prepend('<span class="badge">' + value + '</span>');
        } // if
      } else {
        $('#' + item_id + ' span.badge').remove();
      }
    }
    
  };
  
}();
*/