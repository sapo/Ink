App.invoicing = {
  controllers : {},
  models      : {}
};

/**
 * Invoicing controller
 */
App.invoicing.controllers.invoices = {
  /**
   * Behavior for send page
   */
  issue : function() {
    $(document).ready(function() {
      if($('#issue_invoice table input[type=radio]:checked').length < 1) {
        $('#issue_invoice table input[type=radio]:first')[0].checked = true; // Select first user if nobody is selected
      } // if
      
      $('#select_invoice_recipients table tr').click(function() {
        $(this).find('input[type=radio]')[0].checked = true;
      });
      
      $('#issue_invoice p input[type=radio]').click(function() {
        if($('#issueFormSendEmailsYes')[0].checked) {
          $('#select_invoice_recipients').show('fast');
        } else {
          $('#select_invoice_recipients').hide('fast');
        } // if
      });
      
      if($('#issueFormSendEmailsYes')[0].checked) {
        $('#select_invoice_recipients').show();
      } // if
    });
  },
  
  notify : function() {
    $(document).ready(function() {
      if($('#issue_invoice table input[type=radio]:checked').length < 1) {
        $('#issue_invoice table input[type=radio]:first')[0].checked = true; // Select first user if nobody is selected
      } // if
      
      $('#select_invoice_recipients table tr').click(function() {
        $(this).find('input[type=radio]')[0].checked = true;
      });
      
      $('#issue_invoice p input[type=radio]').click(function() {
        if($('#issueFormSendEmailsYes')[0].checked) {
          $('#select_invoice_recipients').show('fast');
        } else {
          $('#select_invoice_recipients').hide('fast');
        } // if
      });
      
      if($('#issueFormSendEmailsYes')[0].checked) {
        $('#select_invoice_recipients').show();
      } // if
    });
  }
  
};

/**
 * Currencies administration
 */
App.invoicing.controllers.currencies_admin = {
  
  /**
   * Currencies administration index behavior
   */
  index : function() {
    $(document).ready(function() {
      $('#currencies td.checkbox input').click(function() {
        var checkbox = $(this);
        var cell = checkbox.parent();
        
        // Status is not changed to checked (status is set before callback)
        if(this.checked) {
          this.checked = false;
        } else {
          return false;
        } // if
        
        if(confirm(App.lang('Are you sure that you want to set this currency as a default?'))) {
          checkbox.hide();
          cell.append('<img src="' + App.data.indicator_url + '" />');
          
          $.ajax({
            url  : App.extendUrl(checkbox.attr('set_as_default_url'), { async : 1 }),
            type : 'POST',
            data : {
              submitted : 'submitted'
            },
            success : function() {
              $('#currencies td.checkbox input').each(function() {
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
              
              alert(App.lang('Failed to set this currencies as default'));
              
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
 * PDF settings
 */
App.invoicing.controllers.pdf_settings_admin = {
  /**
   * Behavior for send page
   */
  index : function() {
    $(document).ready(function() {      
      $('.color_selector').each(function () {
        var picker = $(this);
        var input_control = $('input:eq(0)', picker.parent())
        var initial_color = '#' + input_control.val();
        
    		$('div', picker).css('backgroundColor', initial_color);
        picker.ColorPicker({
        	color: initial_color,
        	onShow: function (colpkr) {
        		$(colpkr).fadeIn(500);
        		return false;
        	},
        	onHide: function (colpkr) {
        		$(colpkr).fadeOut(500);
        		return false;
        	},
        	onSubmit: function (hsb, hex, rgb) {
        		$('div', picker).css('backgroundColor', '#' + hex);
        		input_control.val(hex);
        	}
        });
        
        input_control.change(function() {
      		$('div', picker).css('backgroundColor', '#' + $(this).val());
      		picker.ColorPickerSetColor($(this).val());
        });
      });
    });
  }
  
};

/**
 * Invoice form behavior
 */
App.invoicing.InvoiceForm = function() {
  
  /**
   * Instances reused in the code below
   *
   * @var jQuery
   */
  var form, items_table;
  
  /**
   * Registered tax rates
   *
   * @var Object
   */
  var tax_rates = {};
  
  /**
   * Reindex items rows
   */
  var reindex_items_rows = function() {
    var counter = 1;
    items_table.find('tr.item').each(function() {
      var row = $(this);
      row.removeClass('odd').removeClass('even').addClass((counter % 2 ? 'odd' : 'even')).find('td.num span').text('#' + counter).show();
      row.find('.move_handle').hide();
      counter++;
    });
    
    items_table.find('tr.item td.num').each(function () {
      var cell = $(this);
      var move_handle = cell.find('.move_handle');
      var num_span = cell.find('span');
     
      cell.hover(function () {
        move_handle.show();
        num_span.hide();
      }, function () {
        var row = $(this);
        move_handle.hide();
        num_span.show();        
      });
    });
    
    $('#invoice_items table').sortable('destroy');
    $('#invoice_items table').sortable({
      axis : 'y',
      items : 'tr.item',
      handle : '.move_handle',
      start : function () {
        $('#invoice_items table tr.item').removeClass('even').removeClass('odd');
      },
      stop : function () {
        reindex_items_rows();
      }
    });
  }; // reindex_items_rows 
  
  /**
   * Initialize item row
   *
   * @param jQuery row
   */
  var initialize_item_row = function(row) {
    row.find('td.tax_rate input[type=hidden]').each(function() {
      var input = $(this);
      var cell = input.parent();
      
      var select = $('<select><option value="0" rate="0">' + App.lang('No Tax') + '</option></select>').attr('name', input.attr('name'));
      for(var i in tax_rates) {
        var option = $('<option></option>').text(tax_rates[i]['name']).attr({
          'value'    : i,
          'rate'     : tax_rates[i]['rate']
        }).appendTo(select);
      } // for
      
      var tax_value = input.val();
      if(tax_value) {
        select.val(tax_value);
      } // if
      
      input.remove();
      cell.append(select);
    });
    
    /**
     * function that handles changes of item fields
     *
     * @param void
     * @return null
     */
    var handle_keyup = function() {
      var input_element = $(this);
      var input_element_cell = input_element.parents('td:first');     
      
      if (input_element_cell.is('.total')) {
        input_element.parents('tr').addClass('recalculate_unit_cost');
      } else {
        input_element.parents('tr').addClass('recalculate_total');
      } // if
      recalculate_total();
    } // handle_keyup
    
    /**
     * filter keypresses that are not allowed
     *
     * @param mixed e
     * @return boolean
     */
    var handle_keypress = function (e) {
      if (handle_special_keypress(e, this) == false) {
        return false;
      } // if
      if ((e.which >= 48) && (e.which <= 57) || (e.which == 46) || (e.which = 43) || (e.altKey || e.metaKey || e.ctrlKey || (e.keyCode > 0))) {
        // check if dot is already present
        if (e.which == 46) {
          var value = $(this).val();
          if (value.indexOf('.') > 0) {
            return false;
          } // if
        } // if
        return true;
      } // if
      return false;
    } // handle_keypress
    
    /**
     * filter enter keypress
     *
     * @param mixed e
     * @return boolean
     */
    var handle_special_keypress = function (e, input_element) {
      if (e.which == 13) {
        //if key is enter add new row
        if (input_element) {
          var next_row = $(input_element).parents('tr.item').next();
        } else {
          var next_row = $(this).parents('tr.item').next();
        } // if
        if (next_row.is('.item')) {
          next_row.find('td.description input').focus();
        } else {
          App.invoicing.InvoiceForm.add_row(true);  
        } // if
        return false;
      } else if (e.keyCode == 27 && e.charCode == 0) {
        if (input_element) {
          var this_row = $(input_element).parents('tr.item');
        } else {
          var this_row = $(this).parents('tr.item');
        } // if
        
        var delete_button = this_row.find('td.options .button_remove');
        var next_row_input = this_row.next().find('td.description input');
        var previous_row_input = this_row.prev().find('td.description input');
        
        delete_button.click();
        
        if (previous_row_input.length > 0) {
          previous_row_input.focus();
        } else if (next_row_input.length > 0) {
          next_row_input.focus();
        } // if
        return false;        
      } // if
      return true;
    } // handle_special_keypress
    
    /**
     * handle blur events
     *
     * @param mixed e
     * @return boolean
     */
    var handle_blur = function (e) {
      var input_element = $(this);
      input_element.val(App.parseNumeric(input_element.val()).toFixed(2));
    } // handle_blur
    
    row.find('td.quantity input').keyup(handle_keyup);
    row.find('td.unit_cost input').keyup(handle_keyup);
    row.find('td.total input').keyup(handle_keyup); 
    row.find('td.tax_rate select').change(handle_keyup);
    
    row.find('td.quantity input').keypress(handle_keypress);
    row.find('td.unit_cost input').keypress(handle_keypress);
    row.find('td.total input').keypress(handle_keypress);
    
    row.find('td.quantity input').blur(handle_blur);
    row.find('td.unit_cost input').blur(handle_blur);
    row.find('td.total input').blur(handle_blur);
    
    row.find('td.description input').keypress(handle_special_keypress);
    
    row.find('td.options img.button_remove').click(function() {
      if(items_table.find('tr.item').length > 1) {
        row.remove();
        recalculate_total();
        reindex_items_rows();
      } // if
    });
    
    row.find('input, select').focus(function() {
      UniForm.focus_field(form, $(this));
    });
  }; // initialize_item_row
    
  /**
   * Recalculate only total of totals
   */
  var recalculate_total = function() {
    var total_subtotal = 0;
    var total_of_totals = 0;
    
    // recalculate rows which are modified
    items_table.find('tr.item.recalculate_total, tr.item.recalculate_unit_cost').each(function() {
      var row = $(this);
      if (row.is('.recalculate_total')) {
        // recalculate total
        var quantity = App.parseNumeric(row.find('td.quantity input').val());
        var unit_cost = App.parseNumeric(row.find('td.unit_cost input').val());
        var tax_rate = App.parseNumeric(row.find('td.tax_rate select option:selected').attr('rate'));
        var total = 0;
        var subtotal = 0;
        
        if(isNaN(quantity) || isNaN(unit_cost) || isNaN(tax_rate)) {
          row.find('td.subtotal input').val(subtotal.toFixed(2));
          row.find('td.total input').val(total.toFixed(2));
        } else {
          var subtotal = quantity * unit_cost;
          var total = subtotal * (1 + tax_rate / 100);
          
          row.find('td.subtotal input').val(subtotal.toFixed(2));
          row.find('td.total input').val(total.toFixed(2));
        } // if
        row.removeClass('recalculate_total');
      } else {
        // recalculate unit cost
        var quantity = App.parseNumeric(row.find('td.quantity input').val());
        var unit_cost = 0;
        var tax_rate = App.parseNumeric(row.find('td.tax_rate select option:selected').attr('rate'));
        var total = App.parseNumeric(row.find('td.total input').val());
        var subtotal = 0;
        if(isNaN(quantity) || isNaN(total) || isNaN(tax_rate)) {
          row.find('td.subtotal input').val(subtotal.toFixed(2));
          row.find('td.unit_cost input').val(unit_cost.toFixed(2));
        } else {
          var unit_cost = total / (quantity * (1 + tax_rate / 100));
          var subtotal = quantity * unit_cost;
          
          row.find('td.subtotal input').val(subtotal.toFixed(2));
          row.find('td.unit_cost input').val(unit_cost.toFixed(2));
        } // if
        row.removeClass('recalculate_unit_cost');  
      } // if
    });
    
    // recalculate total subtotal
    items_table.find('tr.item td.subtotal input').each(function() {
      total_subtotal += App.parseNumeric($(this).val());
    });
    
    // recalculate total of totals
    items_table.find('tr.item td.total input').each(function() {
      var value = $(this).val();
      total_of_totals += App.parseNumeric($(this).val());
    });
    
    var currency_code = form.find('#currencyId option:selected').attr('code');
    var invoice_total_text = '<div>'+App.lang('Subtotal: :total :currency', {
      'total' : total_subtotal.toFixed(2),
      'currency' : currency_code
    })+ '</div>';
    invoice_total_text+= '<div><strong>'+App.lang('Total Due: :total :currency', {
      'total' : total_of_totals.toFixed(2),
      'currency' : currency_code
    })+ '</strong></div>';
    
    $('#invoice_sub_total').val(total_subtotal.toFixed(2));
    $('#invoice_total').val(total_of_totals.toFixed(2));
    items_table.find('tr.invoice_totals td.total').html(invoice_total_text);
  }; // recalculate_total
  
  /**
   * Validate invoice items
   *
   * @param jQuery field
   * @param String caption
   */
  window.validate_invoice_items = function(field, caption) {
    var error_message = false;
    var error_messages = new Array();
    
    var item_count = items_table.find('tr.item').length;
    
    items_table.find('tr.item').each(function() {
      var row = $(this);
      var description = jQuery.trim(row.find('td.description input').val());
      var quantity = App.parseNumeric(row.find('td.quantity input').val());
      var unit_cost = App.parseNumeric(row.find('td.unit_cost input').val());
      var total = App.parseNumeric(row.find('td.total input').val());
      
      if (!description && !total && !unit_cost) {
        // check if there are empty rows
        if (item_count > 1) {
          row.remove();
        } // if
      } else if (!description && (total && unit_cost)) {
        // check if there are missing descriptions
        error_messages[0] = App.lang('All descriptions are required.');
      } // if
    });
    
    var invoice_total = App.parseNumeric(items_table.find('#invoice_total').val());
    if (invoice_total <= 0) {
      error_messages[1] = App.lang('Invoce total is invalid. Invoice total must be greater than zero');
    } // if    
    
    if (error_messages.length > 0) {
      error_message = error_messages.join('<br />');
    } // if
    return error_message ? error_message : true;
  };
  
  // Public interface
  return {
    
    /**
     * Initialize invoice form
     *
     * @param String form_id
     */
    init : function(form_id, mode) {
      form = $('#' + form_id);
      
      var ID_autogenerate = $('#autogenerateID');
      var ID_manually = $('#manuallyID');
      
      ID_autogenerate.find('a').click(function () {
        ID_autogenerate.hide();
        ID_manually.show();
        return false;
      });
      
      ID_manually.find('a').click(function () {
        ID_autogenerate.show();
        ID_manually.hide();
        ID_manually.find('input').val('');
        return false;
      });
      
      // currency handler
      form.find('#currencyId').change(function() {
        recalculate_total();
      });

      var company_id = $('#companyId');
      var company_address = $('#companyAddress');
      
      var ajax_request;
      company_id.change(function () {
        var ajax_url = App.extendUrl(App.data.company_details_url, {
          company_id  : company_id.val(),
          async       : 1,
          skip_layout : 1
        });
        
        // abort request if already exists and it's active
        if ((ajax_request) && (ajax_request.readyState !=4)) {
          ajax_request.abort();
        } // if
        
        if (!company_address.is('loading')) {
          company_address.addClass('loading');
        } // if
        
        company_address.attr("disabled","disabled");
        company_id.attr("disabled","disabled");
        
        ajax_request = $.ajax({
          url         : ajax_url,
          success     : function (response) {
            company_address.val(response);
            company_address.removeClass('loading');
            company_address.removeAttr("disabled","disabled");
            company_id.removeAttr("disabled","disabled");
          }
        });
      });
      if (mode == 'add') {
        company_id.change();
      } // if
      
      items_table = form.find('#invoice_items table');
      items_table.find('a.button_add#add_new').click(function() {
        App.invoicing.InvoiceForm.add_row(true);
        return false;
      });
      
      items_table.find('span.button_dropdown#add_from_template a').click(function() {
        var link = $(this);
        link.parents('.dropdown_container').fadeOut(100);
        App.invoicing.InvoiceForm.add_from_template(link.attr('href'));
        return false;
      });
            
      // initialze preloaded rows     
      var item_rows = items_table.find('tr.item');
      if(item_rows.length < 1) {
        App.invoicing.InvoiceForm.add_row(false);
      } else {
        item_rows.each(function() {
          var row = $(this);
          initialize_item_row(row);
        });
      } // if
                 
      // predefined notes
      form.find('#show_invoice_note_link').click(function() {
        $(this).parent().remove();
        form.find('#invoice_note_wrapper').show('fast', function() {
          $(this).find('textarea')[0].focus();
        });
        return false;
      });
      
      var select_predefined = $('#predefined_notes');
      var note_field = $('#invoice_note');
      select_predefined.change(function () {
        var selected_id = select_predefined.attr('value');
        if (App.data.invoice_notes[selected_id] !== undefined) {
          note_field.val(App.data.invoice_notes[selected_id]);
          note_field.show();
        } else if (selected_id == 'empty') {
          note_field.val('');
          note_field.hide();
        } else if (selected_id == 'original') {
          note_field.val(App.data.original_note);
          note_field.show();
        } else if (selected_id == 'custom') {
          note_field.val('');
          note_field.show();
        } // if
      });
      
      select_predefined.change();
      
      recalculate_total();
      reindex_items_rows();
    },
    
    /**
     * Create a new row
     *
     * @param boolea auto_focus
     */
    add_row : function(auto_focus) {
      var row_number = 0;
      items_table.find('tr.item').each(function () {
        var loop_row = $(this);
        var loop_row_id = loop_row.attr('id');
        if (loop_row_id) {
          loop_row_id = parseInt(loop_row_id.substr(10));
          if (loop_row_id > row_number) {
            row_number = loop_row_id;
          } // if
        } // if
      });
      row_number++;
      var next_row_name = 'invoice[items][' + row_number + ']';
      var zero = 0;
      
      var row = $('<tr class="item" id="items_row_' + row_number + '">' +
        '<td class="num"><span></span><img src="' + App.data.move_icon_url + '" class="move_handle" /></td>' + 
        '<td class="description"><input type="text" name="' + next_row_name + '[description]" value="" /></td>' + 
        '<td class="unit_cost"><input type="text" name="' + next_row_name + '[unit_cost]" class="short" value="'+zero.toFixed(2)+'" /></td>' + 
        '<td class="quantity"><input type="text" name="' + next_row_name + '[quantity]" class="short" value="1" /></td>' + 
        '<td class="tax_rate"><input type="hidden" name="' + next_row_name + '[tax_rate_id]" value="" /></td>' + 
        '<td class="subtotal" style="display: none"><input type="hidden" name="' + next_row_name + '[subtotal]" value="" /></td>' +
        '<td class="total"><input type="text" name="invoice' + next_row_name + '[total]" value="'+zero.toFixed(2)+'"/></td>' + 
        '<td class="options"><img src="' + App.data.assets_url + '/images/gray-delete.gif" class="button_remove" /></td>' + 
      '</tr>');
      
      var last_item_row = items_table.find('tr.item:last');
      if(last_item_row.length > 0) {
        last_item_row.after(row);
      } else {
        items_table.find('tr.header').after(row);
      } // if
      reindex_items_rows();
      initialize_item_row(row);
      recalculate_total();
      
      if(auto_focus) {
        row.find('td.description input')[0].focus();
      } // if
    },
    
    /**
     * Create a new row
     *
     * @param boolea auto_focus
     */
    add_from_template : function(template_id, auto_focus) {
      var row_number = 0;
      items_table.find('tr.item').each(function () {
        var loop_row = $(this);
        var loop_row_id = loop_row.attr('id');
        if (loop_row_id) {
          loop_row_id = parseInt(loop_row_id.substr(10));
          if (loop_row_id > row_number) {
            row_number = loop_row_id;
          } // if
        } // if
      });
      row_number++;
      
      last_row = items_table.find('tr.item:last');
      last_row_description = last_row.find('td.description input:[type=text]:first').val();
      last_row_unit_cost = last_row.find('td.unit_cost input:[type=text]:first').val();
      last_row_unit_total = last_row.find('td.total input:[type=text]:first').val();
      
      if (!last_row_description && !parseInt(last_row_unit_cost) && !parseInt(last_row_unit_total)) {
        row_number--
        last_row.remove();
      } // if
            
      var next_row_name = 'invoice[items][' + row_number + ']';
      var zero = 0;
      
      var row = $('<tr class="item recalculate_total" id="items_row_' + row_number + '">' +
        '<td class="num"><span></span><img src="' + App.data.move_icon_url + '" class="move_handle" /></td>' + 
        '<td class="description"><input type="text" name="' + next_row_name + '[description]" value="'+App.data.invoice_item_templates[template_id].description+'" /></td>' + 
        '<td class="unit_cost"><input type="text" name="' + next_row_name + '[unit_cost]" class="short" value="'+App.data.invoice_item_templates[template_id].unit_cost+'" /></td>' + 
        '<td class="quantity"><input type="text" name="' + next_row_name + '[quantity]" class="short" value="'+App.data.invoice_item_templates[template_id].quantity+'" /></td>' + 
        '<td class="tax_rate"><input type="hidden" name="' + next_row_name + '[tax_rate_id]" value="'+App.data.invoice_item_templates[template_id].tax_rate_id+'" /></td>' + 
        '<td class="subtotal" style="display: none"><input type="hidden" name="' + next_row_name + '[subtotal]" value="" /></td>' +
        '<td class="total"><input type="text" name="invoice' + next_row_name + '[total]" value="'+zero.toFixed(2)+'"/></td>' + 
        '<td class="options"><img src="' + App.data.assets_url + '/images/gray-delete.gif" class="button_remove" /></td>' + 
      '</tr>');
      
      var last_item_row = items_table.find('tr.item:last');
      if(last_item_row.length > 0) {
        last_item_row.after(row);
      } else {
        items_table.find('tr.header').after(row);
      } // if
      reindex_items_rows();
      initialize_item_row(row);
      recalculate_total();
      
      if(auto_focus) {
        row.find('td.description input')[0].focus();
      } // if
    },
    
    /**
     * Register a new tax rate
     *
     * @param String name
     * @param float rate
     */
    register_tax_rate : function(id, name, rate) {
      tax_rates[id] = {
        'name' : name,
        'rate' : rate
      };
    }
    
  };
  
}();

/**
 * Invoice item templates
 */
App.invoicing.controllers.invoice_item_templates_admin = {
  /**
   * Behavior for send page
   */
  index : function() {
    $(document).ready(function() {      
      $('#invoice_item_templates_list table').sortable({
        axis : 'y',
        items : 'tr.template',
        handle : '.move_handle img',
        forcePlaceholderSize : true,
        start : function () {
          $('#invoice_item_templates_list table tr').each(function () {
            var row = $(this);
            row.removeClass('even').removeClass('odd');
          });
        },
        stop : function () {
          var counter = 0;
          $('#invoice_item_templates_list table tr').each(function () {
            var row = $(this);
            row.removeClass('even').removeClass('odd');
            if (counter % 2 == 0) {
              row.addClass('even');
            } else {
              row.addClass('odd');
            } // if
            counter++;
          });
        },
        update : function () {
          $('#invoice_item_templates_list form').ajaxSubmit({
            type : 'post'
          })
        }
      });
    });
  }
};

/**
 * Invoice note templates
 */
App.invoicing.controllers.invoice_note_templates_admin = {
  /**
   * Behavior for send page
   */
  index : function() {
    $(document).ready(function() {      
      $('#invoice_item_templates_list table').sortable({
        axis : 'y',
        items : 'tr.template',
        handle : '.move_handle img',
        forcePlaceholderSize : true,
        start : function () {
          $('#invoice_item_templates_list table tr').each(function () {
            var row = $(this);
            row.removeClass('even').removeClass('odd');
          });
        },
        stop : function () {
          var counter = 0;
          $('#invoice_item_templates_list table tr').each(function () {
            var row = $(this);
            row.removeClass('even').removeClass('odd');
            if (counter % 2 == 0) {
              row.addClass('even');
            } else {
              row.addClass('odd');
            } // if
            counter++;
          });
        },
        update : function () {
          $('#invoice_item_templates_list form').ajaxSubmit({
            type : 'post'
          })
        }
      });
    });
  }
};

/**
 * Invoice number generator
 */
App.invoicing.controllers.invoice_number_generator_admin = {
  /**
   * Behavior for send page
   */
  index : function() {
    $(document).ready(function() {
      var pattern_input = $('.invoice_generator_pattern_input:first');
      var preview_input = $('.invoice_generator_preview_input:first');
      $('.generator_patterns_and_counters .invoice_generator_variables a').click(function () {
        pattern_input.insertAtCursor($(this).text()).change();
      });
      
      var do_the_preview = function () {
        var preview_string = $(this).val();
        $.each(App.data.pattern_variables, function (key, value) {
          var regexp = new RegExp(key, "g");
          preview_string = preview_string.replace(regexp, value);
        });
        preview_input.val(preview_string);
      } // do_the_preview
      
      pattern_input.change(do_the_preview).keyup(do_the_preview);
      pattern_input.change();
    });
  }
};

