App.files = {
  controllers : {},
  models      : {}
};

/**
 * Main files JS file
 */
App.files.controllers.files = {
  
  /**
   * Index page behavior
   */
  index : function() {
    $(document).ready(function() {
      $('#file_list').checkboxes();
    });
  },
  
  /**
   * Initial file details page behavior
   */
  view : function() {
    $(document).ready(function() {
      $('div.file_revisions').each(function() {
        var wrapper = $(this);
        
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
        
        wrapper.find('td.options a').click(function() {
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
                wrapper.find('div.body').append('<p class="details center files_moved_to_trash">' + App.lang('All revision moved to Trash') + '</p>');
              } // if
            },
            error   : function() {
              img.attr('src', old_src);
            }
          });
          
          return false;
        });
      });
    });
  },
  
  /**
   * Upload files behavior
   */
  upload : function() {
    var rows_for_upload = new Array();
    
    var current_row_id = 0;
    var current_row;
    
    var main_form;
    var upload_form;
    var upload_table;
    
    var summary_table;
    
    var uploads_ok = 0;
    var uploads_failed = 0;
    
    /**
     * function to call to submit multiupload form
     */
    var submit_multiupload_form = function () {
      file_id = 0;
      start_upload();
    };
    
    /**
     * Reindex table rows (set odd,even classes and add row #)
     */
    var reindex_table_rows = function () {
      var counter = 0;
      $('tr', upload_table).each(function () {
        row = $(this);
        if ((counter % 2) == 0 ) {
          row.attr('class', 'odd');
        } else {
          row.attr('class', 'even');
        } // if
        $('.number', row).text('#'+counter);
        counter++;
      });
    } // reindex_table_rows
    
    /**
    * Init row in upload table (add remove button functionality)
    */
    var init_multiupload_row = function (row) {
      if (row) {
        $('.button_remove', row).click(function () {
          if ($('tr', upload_table).length > 2) {
            $(this).parent().parent().remove();
            reindex_table_rows();
          } // if
        });
        $('td.description input:eq(0)' ,row).keydown(function(e) {
          if (e.keyCode == 13) {
            submit_multiupload_form();
            return false;
          } // if
        });
      } // if
    } // init_multiupload_row
    
    /**
     * set up hidden form for upload (based on curently selected row from upload table)
     * and do the upload
     */
    var upload_single_file = function () {
      var current_row = rows_for_upload[current_row_id];
      
      // if no more uploads are left, then do some stuff like showing upload statistics
      if (!current_row) {
        var params = {
          files_uploaded : uploads_ok,
          files_failed : uploads_failed
        }
        
        // form result message dependable of number failed and number of succeeded uploads
        if (uploads_ok && uploads_failed) {
          var upload_message = App.lang("Done, :files_uploaded files uploaded and :files_failed uploads failed<br />", params);
        } else if (uploads_ok) {
          var upload_message = App.lang("Done, :files_uploaded files uploaded<br />", params);
        } else {
          var upload_message = App.lang("Done, :files_failed uploads failed<br />", params);
        } // if
        
        // generates links for view files and for multiupload files
        var category_id = $("#multiupload_parent_id").val();
        var upload_more_files_url = main_form.attr('action');
        if (category_id) {
          var files_section_url = App.extendUrl(App.data.files_section_url, { 
            'category_id' :  category_id
          });
          upload_more_files_url = App.extendUrl(upload_more_files_url, { 
            'category_id' :  category_id
          });
        } else {
          var files_section_url = App.data.files_section_url;
        } // if

        upload_message += App.lang('Upload <a href=":upload_url">more files</a> or go back to <a href=":files_url">Files</a> section', {
          files_url : files_section_url,
          upload_url : upload_more_files_url
        });
  
        $('#page_content').append("<div class='important_block'>" + upload_message + "</div>");
        return true;
      } // if
      
      // remove input field from hidden form
      var previous_input = $("input[name=attachment]", upload_form);
      if (previous_input) {
        previous_input.remove();
      } // if
      
      // select current input
      var this_file_file = $("input:eq(0)", current_row);
      var this_file_body = $("input:eq(1)", current_row);
      
      // set progress indicator
      $("tr:eq(" + (current_row_id + 1) + ") img:eq(0)", summary_table).attr('src', App.data.indicator_url);
      
      // move file input from old form to new form (we cannot use clone() function because of jquery explorer bug)
      this_file_file.prependTo(upload_form);
      $('#multiupload_body').val(this_file_body.val());
            
      // submit current form
      upload_form.submit();
      current_row_id++;
      
      return false;
    } // upload_single_file
    
    /**
     * Start upload (copy selected common parameters from main form to hidden
     * upload form, and call upload function for first row from upload table
     */
    var start_upload = function () {
      main_form.hide();
      
      // create upload summary table      
      $('#page_content').append('<table id="upload_table_result" class="common_table"><tr><th></th><th colspan="2">' + App.lang('Uploading files') + '</th></tr></table>');
      summary_table = $('#upload_table_result');
      $('tr', upload_table).each(function () {
        var row = $(this);
        if ($('input',row).val()) {
          rows_for_upload.push(row);
          summary_table.append(
            '<tr>' +
              '<td class="indicator"><img alt="status" src="' + App.data.pending_indicator_url + '"</td>' +
              '<td class="filename">' + $('input',row).val() + '</td>' +
              '<td class="log"></div>' +
            '</tr>'
          );
        } // if
      });
      
      // set category and other stuff      
      $("#multiupload_parent_id").val($("#main_form #fileParent").val());
      $("#multiupload_milestone_id").val($("#main_form #fileMilestone").val());
      $("#multiupload_tags").val($("#main_form #fileTags").val());
      
      $("#multiupload_visibility").val(1);
      
      var visiblity_field_1 = $("#main_form #fileVisibility_1");
      
      // Checkbox?
      if(visiblity_field_1.attr('type') == 'radio') {
        if (visiblity_field_1[0].checked) {
          $("#multiupload_visibility").val(1);
        } else {
          $("#multiupload_visibility").val(0);
        } // if
        
      // Nope. Hidden
      } else {
        $("#multiupload_visibility").val(visiblity_field_1.val());
      } // if
      
      $('.people', upload_form).remove();
      
      $("#main_form .select_asignees_inline_widget .company_user input:checked").each(function () {
        upload_form.append("<input type='hidden' name='notify_users[]' class='people' value='" + $(this).val() + "' />");
      });
      upload_single_file();
    } // start_upload
    
    /**
     * set up basic stuff when page finishes loading
     */
    $(document).ready(function() {
      main_form = $('#main_form');
      upload_form = $('#multiupload_form');
      upload_table = $('.multiupload_table');
      
      // init table rows
      $('tr', upload_table).each(function () {
        init_multiupload_row($(this));
      });
      
      // add "new file" button handler
      $('.button_add', main_form).click(function () {
        image_src = $('tr:eq(1) .button_column img:eq(0)', upload_table).attr('src');
        upload_table.append(''+
        '<tr>' +
          '<td class="number"></td>' +
          '<td class="input"><input type="file" value="" name="attachment"/></td>' +
          '<td class="description"><input type="text" name="file[body]" /></td>' +
          '<td class="button_column"><img src="' + image_src + '" class="button_remove" /></td>' +
        '</tr>');
        init_multiupload_row(row.next());
        reindex_table_rows();
        return false;
      });
      
      // reindex table rows
      reindex_table_rows();
      
      // define upload_form behaviour
      upload_form.ajaxForm({ 
        success:    function(response) {
          /*
            because of wodoo magic with ajaxForm and file uploads, we can't use 
            error and success callbacks as we used to use them. In this special case
            error callback is called only when request is failed, not when server
            returns some of error headers, so we need to set up our serverside script
            to return strings 'error' when there is some http error, and string
            'success' if return is http ok.
          */
          if (response!=='success') {
            $("tr:eq(" + (current_row_id) + ") img:eq(0)", summary_table).attr('src', App.data.error_indicator_url);
            uploads_failed++;
          } else {
            $("tr:eq(" + (current_row_id) + ") img:eq(0)", summary_table).attr('src', App.data.ok_indicator_url);
            uploads_ok++;
          } // if
          upload_single_file();
        } ,
        error:      function() {
          $("tr:eq(" + (current_row_id) + ") img:eq(0)", summary_table).attr('src', App.data.error_indicator_url);
          uploads_failed++;
          upload_single_file();
        }
      });
      
      // upload button functionality
      $('#upload_files').click(function () {
        submit_multiupload_form();
      });
    });
    
  }
  
}