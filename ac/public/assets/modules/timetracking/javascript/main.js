App.timetracking = {
  controllers : {},
  models      : {}
};

/**
 * Timetracking behavior
 */
App.timetracking.controllers.timetracking = {
  
  /**
   * Timetracking index action
   */
  index : function() {
    $(document).ready(function() {      
      // mass edit functionality
      var mass_edit = $('#mass_edit');
      mass_edit.enable = function () {
        mass_edit.find('select').attr('disabled','');
        if ((mass_edit.find('select').val() != '') && (form.find('.time_record input:checked').length > 0)) {
          mass_edit.find('button').attr('disabled','');
        } else {
          mass_edit.find('button').attr('disabled','disabled');  
        } // if
      };
      mass_edit.disable = function () {
        mass_edit.find('select').attr('disabled','disabled');
        mass_edit.find('button').attr('disabled','disabled');
      };
      mass_edit.disable();
      
      var mass_edit_change = function () {
        if ((mass_edit.find('select').val != '')) {
          mass_edit.enable();
        } else {
          mass_edit.disable();
        } // if       
      } // mass_edit_change
      
      mass_edit.change(mass_edit_change).click(mass_edit_change);
      
      var form = $('#add_time_record_form');
//      form.attr('action', App.extendUrl(form.attr('action'), {async : 1}));
      
      // Add time record form
      form.submit(function() {
        var add_form = $(this);
        var add_form_url = App.extendUrl(add_form.attr('action'), {async : 1});
       
        if(UniForm.validate(add_form, true)) {
          $('#new_record td.actions').prepend('<img src="' + App.data.indicator_url + '" alt="" />').find('button').hide();
          
          $(this).ajaxSubmit({
            url    : add_form_url,
            success: function(responseText) {
              $('#no_records').hide();
              $('#mass_edit').show();
              
              $('#timerecords table tbody tr:eq(0)').after(responseText);
              
              $('#new_record td.actions').find('img').remove();
              $('#new_record td.actions').find('button').show();
              
              $('#time_summary').val('');
              $('#time_hours').val('')[0].focus();
              
              var new_row = $('#timerecords table tbody tr:eq(1)');
              App.timetracking.records.init_mark_as_billed_link(new_row);
              App.timetracking.records.init_time_record_row(new_row, mass_edit);
              new_row.find('td').highlightFade();
              
              App.timetracking.records.rebuild_even_odd_classes();
            },
            error : function() {
              $('#new_record td.actions').find('img').remove();
              $('#new_record td.actions').find('button').show();
              
              $('#time_summary').val('');
              $('#time_hours').val('')[0].focus();
            }
          });
        } // if
        
        return false;
      });
      
      App.timetracking.records.init_mark_as_billed_link($('#timerecords table.timerecords'));
      App.timetracking.records.recalculate_total();
      
      // initialize time records table
      form.find('.time_record').each(function () {
        App.timetracking.records.init_time_record_row($(this), mass_edit);
      });      
      
      $('#records_submit').click(function () {
        hidden_form = $('<form method=post action="'+App.data.mass_update_url+'" style="display:none" id="hidden_mass_update"><input type="hidden" name="submitted" value="submitted" /><input type="hidden" value="'+mass_edit.find('select').val()+'" name="action" /></form>');
        hidden_form.prepend(form.find('.time_record input:checked').clone().attr('checked','checked'));
        $('body').append(hidden_form)
        $('#hidden_mass_update').submit();
      });
      
    });
  },
  
  /**
   * Prepare behavior for reports page
   *
   * @param void
   * @return null
   */
  reports : function() { 
    $(document).ready(function() {
      if($('#report_type').val() == 'custom') {
        $('#generate_report .select_date').show();
        $('#generate_report .date_separator').show();
        
      } else {
        $('#generate_report .select_date').hide();
        $('#generate_report .date_separator').hide();
      } // if
      
      $('#report_type').change( function() {
        if($(this).val() == 'custom') {
          $('#generate_report .select_date').show();
          $('#generate_report .date_separator').show();
        } else {
          $('#generate_report .select_date').hide();
          $('#generate_report .date_separator').hide();
        } // if
      });
      
      $('#generate_report').submit(function() {
        if($('#report_type').val() != 'custom') {
          $(this).find('.select_date').remove();
        } // if
        return true;
      });
      
      App.timetracking.records.init_mark_as_billed_link($('#records table'));
      App.timetracking.records.recalculate_total();
    });
  }
  
};

/**
 * Records management module
 */
App.timetracking.records = function() {
  
  /**
   * Public interface
   */
  return {
    
    /**
     * Make sure that even - odd classes are rebuilt when we add / remove 
     * records
     *
     * @param void
     * @return void
     */
    rebuild_even_odd_classes : function() {
      var counter = 0;
      $('table.timerecords tr.time_record').each(function() {
        var row = $(this);
        
        if(row.attr('id') != 'records_summary') {
          counter++;
        
          row.removeClass('even').removeClass('odd');
          if((counter % 2) > 0) {
            row.addClass('odd');
          } else {
            row.addClass('even');
          } // if
        } // if
      });
    }, // rebuild_even_odd_classes
    
    /**
     * Recalculate total time
     *
     * @param void
     * @return void
     */
    recalculate_total : function() {
      $('table.timerecords').each(function() {
        var wrapper = $(this);
        
        var reports_cell = wrapper.find('td.total');
        if(reports_cell.length > 0) {
          var total = 0;
          wrapper.find('td.hours').each(function() {
            total += parseFloat($(this).text());
          });
          
          reports_cell.text(App.lang('Total ') + total);
        } // if
      });
    }, // recalculate_total
    
    /**
     * Init mark as (not) billed link
     *
     * @param jQuery wrapper
     * @return void
     */
    init_mark_as_billed_link : function(wrapper) {
      wrapper.find('a.mark_time_record_as_billed').click(function() {
        var link = $(this);
        var parent_cell = link.parent();
        
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
          success : function(response) {
            link.remove();
            parent_cell.prepend(response);
            App.timetracking.records.init_mark_as_billed_link(parent_cell);
            
            var link_href = link.attr('href');
            if(link_href.substr(link_href.indexOf('to=') + 3) == '1') {
              parent_cell.parent().addClass('billed');
            } else {
              parent_cell.parent().removeClass('billed');
            } // if
          },
          error   : function() {
            img.attr('src', old_src);
          }
        });
        
        return false;
      });
    }, // init_mark_as_billed_link
    
    /**
     * Initialize time record row
     */
    init_time_record_row : function (time_record_row, mass_edit) {
      var checkbox = time_record_row.find('input');
      checkbox.click(function () {
        if ($('table.timerecords').find('.time_record input:checked').length > 0) {
          mass_edit.enable();
        } else {
          mass_edit.disable();
        } // if
      });
    } // init_time_record_row
    
  };
  
}();

/**
 * Time popup behaviro
 */
App.TimePopup = function() {
  
  // Public interface
  return {
    
    /**
     * Initialize timetracking widget
     *
     * @param string wrapper_id
     * @return void
     */
    init : function(wrapper_id) {
      var object_id = parseInt(wrapper_id.substr(19));
      
      $('#' + wrapper_id + ' a').click(function(e) {
        var link = $(this);
        var current_openner = link.parent();
        
        var indicator_image_src =  App.data.assets_url + '/images/indicator.gif';
        var time_popup_url = App.extendUrl(link.attr('href'), { 
          for_popup_dialog : 1 
        });
        
        App.ModalDialog.show('object_time', App.lang('Time'), $('<div><img src="' + indicator_image_src + '" alt="" /> ' + App.lang('Loading...') + '</div>').load(time_popup_url, function() {
          
          /**
           * Initialize popup behavior
           *
           * @param String popup_wrapper_id
           */
          var init_popup = function(popup_wrapper_id) {
            var wrapper = $('#' + popup_wrapper_id);
          
            wrapper.find('p.object_time_add_link a').click(function() {
              $(this).parent().hide();
              wrapper.find('div.object_time_add').show('fast', function() {
                wrapper.find('div.time_popup_hours_wrapper input')[0].focus();
              });
              
              return false;
            });
            
            wrapper.find('button.object_time_cancel_button').click(function() {
              wrapper.find('p.object_time_add_link').show();
              wrapper.find('div.object_time_add').hide();
              
              return false;
            });
            
            wrapper.find('form').submit(function() {
              var form = $(this);
              var popup_wrapper = form.parent().parent();
              
              form.block(App.lang('Working...'));
              
              form.ajaxSubmit({
                success : function(response) {
                  popup_wrapper.empty();
                  popup_wrapper.append(response);
                  popup_wrapper.find('div.object_time_popup_details dl span.time').highlightFade();
                  
                  if(current_openner.attr('class').indexOf('with_text') && popup_wrapper.find('div.object_time_popup_details dl dd.object_time').length > 0 && popup_wrapper.find('div.object_time_popup_details dl dd.tasks_time').length > 0) {
                    var object_time = App.parseNumeric(popup_wrapper.find('div.object_time_popup_details dl dd.object_time span.time').text());
                    var tasks_time = App.parseNumeric(popup_wrapper.find('div.object_time_popup_details dl dd.tasks_time span.time').text());
                    
                    if(object_time > 0 && tasks_time > 0) {
                      var total_time = object_time + tasks_time;
                      
                      ':total hours logged - :object_time for the ticket and :tasks_time for tasks'
                      current_openner.find('span.time_widget_text').text(App.lang(':total hours logged - :object_time for the ticket and :tasks_time for tasks', {
                        'total'       : parseFloat(total_time.toFixed(2)),
                        'object_time' : parseFloat(object_time.toFixed(2)),
                        'tasks_time'  : parseFloat(tasks_time.toFixed(2))
                      }));
                    } else {
                      current_openner.find('span.time_widget_text').text(App.lang(':total hours logged', {
                        'total' : parseFloat(object_time.toFixed(2))
                      }));
                    } // if
                  } // if
                  
                  current_openner.find('img').attr('src', App.data.assets_url + '/images/clock-small.gif');
                  
                  init_popup('object_time_popup_' + object_id);
                }
              });
              
              return false;
            });
          };
          
          init_popup('object_time_popup_' + object_id);
        }), {
          buttons : false,
          width: 450
        });
        return false;
      });
    }
    
  };
  
}();

App.timetracking.TimeReport = function() {
  
  // Public interface
  return {
    
    /**
     * Initialize report page
     */
    init : function() {
      $('#time_report_select select').change(function() {
        var report_url = $(this).val();
        if(report_url != location.href) {
          $(this).after(' ' + App.lang('Loading ...')).attr('disabled', 'disabled');
          location.href = report_url;
        } // if
      });
      
      $('#time_report_options a').hover(function() {
        $('#time_report_options span.tooltip').text($(this).attr('title'));
      }, function() {
        $('#time_report_options span.tooltip').text('');
      }); 
      
      $('#toggle_report_details').click(function() {
        $('#time_report_details').toggle('fast');
        return false;
      });
    }
    
  };
  
}();

/**
 * Add / Edit time report form behavior
 */
App.timetracking.TimeReportForm = function() {
  
  /**
   * Form instance
   *
   * @var jQuery
   */
  var form;
  
  // Public interface
  return {
    
    /**
     * Initialize time report form
     *
     * @param string form_id
     * @param string partial_generator_url
     * @return void
     */
    init : function(form_id, partial_generator_url) {
      form = $('#' + form_id);
      
      form.find('select.report_async_select').change(function() {
        var select = $(this);
        var row = select.parent().parent();
        var cell_additional = row.find('td.report_select_additional');
        var option = select.find('option[value=' + select.val() + ']');
        
        if(option.attr('class') == 'report_async_option') {
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