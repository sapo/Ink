App.pages = {
  controllers : {},
  models      : {}
};

/**
 * Revisions table behavior module
 */
App.pages.revisions_table = function() {
  
  // Public interface
  return {
    rebuild_even_odd_classes : function() {
      var counter = 0
      $('table.revisions_table tr').each(function() {
        counter++;
        
        var row = $(this);
        row.removeClass('even').removeClass('odd');
        if((counter % 2) > 0) {
          row.addClass('odd');
        } else {
          row.addClass('even');
        } // if
      });
    }
  };
  
}();

/**
 * reorder page behavior module
 */
App.pages.reorder_page = function() {
  
  // Public interface
  return {
    init  : function () {
      reorder_tree = new tree_component();
      reorder_tree.init($("#pages_reorder"),{
        ui    : {
          context     : false,
          theme_path  : App.data.assets_url + '/images/tree_component/',
          theme_name  : 'default'
        },
        rules : {
          draggable : 'all',
          dragrules : 'all',
          clickable   : "none",
          renameable  : "none",
          deletable   : "none",
          creatable   : "none"
        }
      });
      
      // force expand all pages
      $("#pages_reorder").find('li').each(function () {
        reorder_tree.open_branch($(this),true);
      });
      
      $('#reorder_form button:first').click(function () {
        var inputs = '';
        $('#pages_reorder ul li').each(function () {
          var li_element = $(this);
          var page_id = li_element.find('input:first').attr('value');
          var parent_li = li_element.parent().parent();
          if (parent_li.is('li')) {
            var parent_page_id = parent_li.find('input:first').attr('value');
          } else {
            var parent_page_id = 0;
          } // if
          inputs+= '<input type="hidden" name="ordered_pages['+page_id+']" value="'+parent_page_id+'" />';
        });
        
        inputs+= '<input type="hidden" name="submitted" value="submitted" />';
        var submit_url = $('#reorder_form').attr('action');
        
        if (!App.data.is_assync_call) {
          var hidden_form = $("<form method='post' action='"+submit_url+"' style='display:none' id='hidden_form'>"+inputs+"</form>");
          $('body').append(hidden_form);
          $('#hidden_form').submit();
          return false;
        } else {
          $('#reorder_form').before('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Loading...') + '</p>').hide();
          inputs = $(inputs);
          $('#page_content').block();
          App.ModalDialog.close()
          $.ajax({
            data    : inputs,
            url     : App.extendUrl(submit_url, {async : 1, skip_layout : 1}),
            type  : 'post',
            success : function (response) {
              $('#page_content').html(response);
            }
          });
          return false;
        } // if
      }) // click      
    }  
  };
  
}();

/**
 * Pages controller behavior
 */
App.pages.controllers.pages = {
  /**
   * Index page behaviour
   *
   * @param void
   * @return void
   */
  index : function () {
    $(document).ready(function () {
      $('#reorder_pages_button').click(function () {
        App.data.is_assync_call = true;
        var assync_url = App.extendUrl($(this).attr('href'), { async: 1 });
  
        App.ModalDialog.show('reorder_pages_dialog', App.lang('Reorder Pages'), $('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Loading...') + '</p>').load(assync_url), {
          buttons : null 
        });
        return false;
      });
    });
  },
  
  /**
   * Prepare view page behavior
   *
   * @param void
   * @return void
   */
  view : function() {
    $(document).ready(function() {
      $('table.revisions_table a.remove_revision').click(function() {
        var link = $(this);
      
        // Block additional clicks
        if(link[0].block_clicks) {
          return false;
        } else {
          link[0].block_clicks = true;
        } // if
        
        if(confirm(App.lang('Are you sure that you want to delete this page version? There is no undo!'))) {
          var img = link.find('img');
          var old_src = img.attr('src');
          
          img.attr('src', App.data.indicator_url);
          
          $.ajax({
            url     : App.extendUrl(link.attr('href'), {'async' : 1}),
            type    : 'POST',
            data    : {'submitted' : 'submitted'},
            success : function() {
              link.parent().parent().remove();
              App.pages.revisions_table.rebuild_even_odd_classes();
            },
            error   : function() {
              img.attr('src', old_src);
            }
          });
        } // if
        
        return false;
      });
    });
    
  },
  
  /**
   * Compare page behavior
   */
  compare_versions : function() {
    $(document).ready(function() {
      var loading = false;
      
      $('#page_compare form').submit(function() {
        if(loading) {
          return false; // let the last operation be completed...
        } // if
        
        var form = $(this);
        
        var new_version = $('#new_version_select').val();
        var old_version = $('#old_version_select').val();
        
        if(new_version == old_version) {
          alert(App.lang('Please select different versions!'));
          return false;
        } // if
        
        loading = true;
        
        var compared_versions_wrapper = $('#compared_versions');
        compared_versions_wrapper.block(App.lang('Loading...'));
        form.find('select').attr('disabled', 'disabled');
        
        $.ajax({
          type : 'GET',
          url : App.extendUrl(App.data.compare_pages_url, {
            'new'   : new_version,
            'old'   : old_version,
            'async' : 1
          }),
          success : function(response) {
            compared_versions_wrapper.find('table').remove();
            compared_versions_wrapper.append(response);
            compared_versions_wrapper.unblock();
            form.find('select').attr('disabled', '');
            compared_versions_wrapper.highlightFade();
            
            loading = false;
          },
          error : function() {
            compared_versions_wrapper.unblock();
            
            loading = false;
          }
        });
        
        return false;
      });
    });
  }
  
};