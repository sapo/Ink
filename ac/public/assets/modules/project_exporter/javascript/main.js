App.project_exporter = {
  controllers : {},
  models      : {}
};

/**
 * Project Exporter client side behaviour
 */
App.project_exporter.controllers.project_exporter = {
  
  /**
   * Index page bahaviour
   *
   */ 
  index : function () {
    $(document).ready(function() {
      var global_container = $('#project_exporter_container');
      var additional_controls = global_container.find('#additional_controls');
      var button_holder = global_container.find('.buttonHolder');
      var main_table = global_container.find('#main_table');
      
      button_holder.find('button:first').click(function () {
        additional_controls.hide();
        button_holder.hide();
 
        main_table.find('input:not(:checked)').each(function () {
          $(this).parents('tr:first').hide();
        });
 
        var modules = new Array();
        var loop = 0;
        main_table.find('input:checked').each(function () {
          var current_checkbox = $(this);
          var current_row = current_checkbox.parents('tr:first');
 
          if ((loop % 2) == 1) {
          current_row.removeClass('odd');
          current_row.addClass('even');
          } else {
          current_row.removeClass('even');
          current_row.addClass('odd');            
          } // if
 
          modules.push({
            module : current_row.attr('module'),
            url : current_row.attr('export_url')
          });
 
          current_checkbox.before('<img src="' + App.data.pending_indicator_url + '" alt="." />');
          current_checkbox.remove();
 
          loop++;
        });
 
        export_project(modules, additional_controls.find('#visibility input:checked').val(), additional_controls.find('#compress_container input:checked').length);
        return false;
      });
      
      /**
       * Start exporting project
       *
       * @param array modules
       * @param integer visibility
       * @param boolean compress_output
       * @return null
       */
      var export_project = function (modules, visibility, compress_output) {
      var response_obj;
      var imploded_modules = new Array();
      for (var counter=0; counter < modules.length; counter++) {
        var current_module = modules[counter].module;
        if (current_module != 'finalize') {
        imploded_modules.push(current_module);
        } // if
      } // for
      imploded_modules = imploded_modules.join(',');
      modules = modules.reverse();
 
      /**
       * Check if there is warning
       *
       * @param object execution_log
       * @return null
       */
      var warning_exists = function (execution_log) {
        if (execution_log == undefined) return true;
        for (x=0; x<execution_log.length; x++) {
        if (execution_log[x].status == 1) {
          return true
        } // if
        } // for
        return false;
      } // warning_exists
 
      /**
       * Export single module and ping next one
       *
       * @param object module
       * @return null
       */
      var export_module = function (module, visibility, compress_output) {
        if (typeof module != 'object') {
        if (compress_output && !warning_exists(response_obj.log)) {
          var message_block = 
          '<div id="download_link_block" style="display:block">' +
          App.lang('Download project archive using following link') + 
          ':<br /><div id="download_link">' + '<a href="'+App.data.download_url+'">'+App.data.download_url+'</a></div>' +
          '</div>';
        } else {
          var message_block = 
          '<div id="download_link_block" style="display:block">' +
          App.lang('Exported project is located') + 
          ':<br /><div id="download_link"><strong>' + App.data.download_ftp_url + '</strong></a></div>' +
          '</div>';              
        }
        main_table.after(message_block);
        return false;
        } // if
 
        row_status_indicator = main_table.find('#module_'+module.module+' .status_indicator img');
        row_log_field = main_table.find('#module_'+module.module+' .module_log');
 
        // indicate progress
        row_status_indicator.attr('src', App.data.indicator_url);
 
        var ping_url = App.extendUrl(module.url, {visibility : visibility, modules : imploded_modules, compress : compress_output})
 
        // do the magic
        $.ajax({
        url: ping_url,
        type: "GET",
        success: function (response) {
          eval('response_obj = '+response);
          if (response_obj.error_count > 0) {
          row_status_indicator.attr('src', App.data.error_indicator_url);
          } else if ((response_obj.log.length > 0) && (warning_exists(response_obj.log))) {
          row_status_indicator.attr('src', App.data.warning_indicator_url);
          } else {
          row_status_indicator.attr('src', App.data.ok_indicator_url);
          } // if
 
          if (response_obj.log.length > 0) {
          status_message = new Array();
          for (x=0; x<response_obj.log.length; x++) {
            message = response_obj.log[x].message;
            message_status = response_obj.log[x].status;
            if (message_status == 1) {
            message = '<span class="status_warning">'+message+'</span>';
            } else if (message_status == 2) {
            message = '<span class="status_ok">'+message+'</span>';
            } else {
            message = '<span class="status_error">'+message+'</span>';
            }
            status_message.push(message);
          }
          row_log_field.html(status_message.join("<br />"));
          } // if
          export_module(modules.pop(), visibility, compress_output);         
        }
        })
 
      } // if
 
      export_module(modules.pop(), visibility, compress_output);
      } // if
    }) // document ready
  }
};