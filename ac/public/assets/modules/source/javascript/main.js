App.source = {
  controllers : {},
  models      : {}
};

App.source.controllers.repository = {

  /**
  * Add repository
  */
  add : function() {
    $(document).ready(function() {
      App.source.AddEditForm.init();
    });
  },


  /**
  * Edit repository
  */
  edit : function() {
    $(document).ready(function() {
      App.source.AddEditForm.init();
    });
  },


  /**
  * History page behaviour
  */
  history : function() {
    $(document).ready(function() {

      $('tr.commit div.commit_files').hide(); // hide all paths on page load
      $('#toggle_all_paths span').text(App.lang('Show all paths')); // set initial text

      // show/hide one
      $('tr.commit').each(function() {
        var wrapper = $(this);

        wrapper.find('a.toggle_files').click(function() {
          wrapper.find('div.commit_files').toggle();
          return false;
        });
      }); // show/hide one


      $('#repository_delete_page_action').click(function() {
        return confirm(App.lang('Are you sure that you wish to delete this repository from activeCollab?'));
      });
      
      // show/hide all
      var toggle_new_class = null;
      var link_text = null;

      $('#toggle_all_paths').click(function() {
        var toggle_button = $(this);

        if (toggle_button.is('.hide')) {
          $('tr.commit div.commit_files').hide();
          link_text = App.lang('Show all paths');
          toggle_button.removeClass('hide');
          toggle_button.addClass('show');
        }
        else {
          $('tr.commit div.commit_files').show();
          link_text = App.lang('Hide all paths');
          toggle_button.removeClass('show');
          toggle_button.addClass('hide');
        }
        $('span', toggle_button).text(link_text);

        return false;
      }); //show/hide all


      // Update repository
      /*
      $('#repository_ajax_update, a.repository_ajax_update').click(function() {
        var delimiter = App.data.path_info_through_query_string ? '&' : '?';
        App.ModalDialog.show('repository_update', App.lang('Repository update'), $('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Checking for updates...') + '</p>').load($(this).attr('href')+ delimiter + 'skip_layout=1&async=1'), {
          buttons : false,
          width: 400
        });
        return false;
      });
      */


    });

  }, // history

  update : function() {
    progress_div = $('#repository_update_progress');
    
    var delimiter = App.data.path_info_through_query_string ? '&' : '?';
    
    var notify_subscribers = function(total_commits) {
      $('#progress_content').append('<p class="subscribers"><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Sending subscriptions...') + ' </p>');

      $.ajax({
        url: App.data.repository_update_url + delimiter + 'async=1&notify=' + total_commits,
        type: 'GET',
        success : function(response) {
          $('#progress_content p.subscribers img').attr({
          'src' : App.data.assets_url + '/images/ok_indicator.gif'
          });

          $('#progress_content p.subscribers').append(App.lang('Done!'));
        }
      });
    }

    var get_logs = function(commit, total_commits) {
      progress_content = $('#progress_content');

      progress_content.html('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> Importing commit #' + commit + '</p>');
      $.ajax( {
        url: App.data.repository_update_url + delimiter + 'r=' + commit + '&async=1&skip_missing_revisions=1',
        type: 'GET',
        success : function(response) {
          if (response == 'success') {
            if (commit !== App.data.repository_head_revision) {
              commit++;
              get_logs(commit, total_commits);
            }
            else {
              progress_content.html('<p><img src="' + App.data.assets_url + '/images/ok_indicator.gif" alt="" /> '+ App.lang('Repository successfully updated') + '</p>');
              notify_subscribers(total_commits);
            }
          }
          else {
            progress_content.html(response); // if not success, reponse is a svn error message
          }
        }
      });
    }


    if (App.data.repository_uptodate == 1) {
      progress_div.html('<p><img src="' + App.data.assets_url + '/images/ok_indicator.gif" alt="" /> '+ App.lang('Repository is already up-to-date') + '</p>');
    }
    else {
      total_commits = App.data.repository_head_revision - App.data.repository_last_revision;
      commit = App.data.repository_last_revision+1;

      if (total_commits > 0) {
        progress_div.prepend('<p>There are new commits, please wait until the repository gets updated to revision #'+App.data.repository_head_revision+'</p>');
        get_logs(commit, total_commits);
      }
      else {
        progress_div.prepend('<p>' + App.lang('Error getting new commits') + ':</p>');
      }
    }


  }, // update
  
  browse : function() {
    $(document).ready(function () {
      
      App.widgets.SourceFilePages.init();
      
      $('a.source_item_info').each(function() {
        var link_obj = $(this);
        link_obj.click(function() {         
          App.ModalDialog.show('item_info', App.lang('Item info'), $('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Fetching data...') + '</p>').load(link_obj.attr('href')), {
            buttons : false,
            width: 700
          });
          return false;
        });
      }); // show/hide one      
    })
  }, // browse
  
  
  repository_users : function() {
    $(document).ready(function () {
      
      var select_box = $('#repository_user');
      
      $('table.mapped_users').find('a.remove_source_user').click(function() {
        if (confirm(App.lang('Are you sure that you wish to delete remove this mapping?'))) {
          var link = $(this);
  
          var img = link.find('img');
          var old_src = img.attr('src');
  
          img.attr('src', App.data.indicator_url);
  
          $.ajax({
            url     : App.extendUrl(link.attr('href'), {'async' : 1}),
            type    : 'POST',
            data    : {'submitted' : 'submitted', 'repository_user' : link.attr('name')},
            success : function(response) {
              if (response == 'true') {
                link.parent().parent().remove();
                select_box.append('<option value="'+link.attr('name')+'">'+link.attr('name')+'</option>');
                
                $('#all_mapped').hide();
                $('#no_users').hide();
                $('#new_record').show();
                
                if ($('#records tbody').children().length == 0) {
                  $('#records').hide();
                }
                
              } else {
                img.attr('src', old_src);
              }
            }
          });

        }
        
        return false;
      });
      
      
      
      var form = $('form.map_user_form');
      form.attr('action', App.extendUrl(form.attr('action'), { async : 1 }));

      form.submit(function() {
        if ($('#user_id').find('option:selected').val() == '') {
          alert(App.lang('Please select activeCollab user'));
          return false;
        }
        
        var form = $(this);
        
        $('#new_record td.actions').prepend('<img src="' + App.data.indicator_url + '" alt="" />').find('button').hide();
        
        $(this).ajaxSubmit({
            success: function(responseText) {
              $('#records tbody').prepend(responseText);
              $('#records').show();
              
              $('#new_record td.actions').find('img').remove();
              $('#new_record td.actions').find('button').show();
              
              var new_row = $('#records tbody tr:first');
              new_row.find('td').highlightFade();
              
              select_box.find('option:selected').remove();
              
              if (select_box.children().length == 0) {
                $('#new_record').hide();
                $('#all_mapped').show();
              }
            },
            error : function() {
              $('#new_record td.actions').find('img').remove();
              $('#new_record td.actions').find('button').show();
            }
        });
          
        return false;
      });
      
      
    });
  }
  
};

/**
* Javascript for source administration
*/
App.source.controllers.source_admin = {

  index : function () {
    $(document).ready(function () {
      var test_results_div = $(this).find('.test_results');
      var test_div = test_results_div.parent();
      test_results_div.prepend('<img class="source_results_img" src="" alt=""/>');
      $('.source_results_img').hide();
      
      $('#check_svn_path button:eq(0)').click(function () {
        $('.source_results_img').show();
        var svn_path = $('#svn_path').val();
        var indicator_img = $('.source_results_img');
        var result_span = test_div.find('.test_results span:eq(0)');
        indicator_img.attr('src', App.data.indicator_url);
        result_span.html('');
        $.ajax({
          type: "GET",
          data: "svn_path=" + svn_path,
          url: App.data.test_svn_url,
          success: function(msg){
            if (msg=='true') {
              indicator_img.attr('src', App.data.ok_indicator_url);
              result_span.html(App.lang('Subversion executable found'));
            } else {
              indicator_img.attr('src', App.data.error_indicator_url);
              result_span.html(App.lang('Error accessing SVN executable') + ': ' + msg);
            } // if
          }
        });

      });
    });
  }

};

/**
* Init JS functions for source file pages
*/
App.widgets.SourceFilePages = function () {
  return {
    init : function () {
      var delimiter = '&';
      
      $('#object_quick_option_compare a').click(function () {
        var compared_revision = parseInt(prompt(App.lang('Enter revision number'), ""));
        
        if (isNaN(compared_revision)) {
          alert(App.lang('Please insert a revision number'));
        } else {
          window.location = App.data.compare_url + delimiter + 'compare_to=' + compared_revision + 'peg=' + App.data.active_revision;
        } // if

        return false;
      });
      
      $('#change_revision').click(function () {
        var new_revision = parseInt(prompt(App.lang('Enter new revision number'), ""));

        if (isNaN(new_revision)) {
          alert(App.lang('Please insert a revision number'));
        } else {
          window.location = App.data.browse_url + delimiter + 'r=' + new_revision + delimiter + 'peg=' + App.data.active_revision;
        } // if

        return false;
      });
    }
  }
} ();


/**
* Test repository connection
*/
App.source.AddEditForm = function() {
  return {
    init : function () {
      var result_container = $('#test_connection .test_connection_results');
      var result_image = $('img:eq(0)', result_container);
      var result_output = $('span:eq(0)', result_container);

      $('#test_connection button').click(function () {
        result_output.html(App.lang('Checking...'));
        result_image.attr('src', App.data.indicator_url);

        if ($('#repositoryUrl').attr('value') == undefined) {
          result_image.attr('src', App.data.error_indicator_url);
          result_output.html(App.lang('You need to enter repository URL first'));
        }
        else {
          var delimiter = App.data.path_info_through_query_string ? '&' : '?';
          $.ajax( {
            url: App.data.repository_test_connection_url + delimiter + 'url=' + $('#repositoryUrl').attr('value') + '&user=' + $('#repositoryUsername').attr('value') + '&pass=' + $('#repositoryPassword').attr('value') + '&engine=' + $('#repositoryType option:selected').attr('value'),
            type: 'GET',
            success : function(response) {
              if (response == 'ok') {
                result_image.attr('src', App.data.ok_indicator_url);
                result_output.html(App.lang('Connection parameters are valid'));
              }
              else {
                result_image.attr('src', App.data.error_indicator_url);
                result_output.html(App.lang('Could not connect to repository:') + ' ' + response); // if not success, reponse is a svn error message
              }
            }
          });
        }
      });
    }
  };
} ();