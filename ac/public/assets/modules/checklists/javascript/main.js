App.checklists = {
  controllers : {},
  models      : {}
};

/**
 * Project Exporter client side behaviour
 */
App.checklists.controllers.checklists = { 
  /**
   * Archive page bahaviour
   *
   */   
  index : function () {   
    $(document).ready(function() {
      $('td.expander a').click(function () {
        var anchor = $(this);
        var anchor_row = anchor.parents('tr:first');
        var anchor_image = anchor.find('img');
        var ajax_url = App.extendUrl(anchor.attr('href'), {show_only_tasks : 'true', async : 1})
        var checklist_tasks_row = anchor.parents('div.checklist').find('.tasks_container:first');
        
        if (anchor.is('.collapsed')) {
          anchor.removeClass('collapsed');
          anchor_row.removeClass('collapsed');
          anchor_image.attr('src', App.data.indicator_url);
          if (!checklist_tasks_row.html()) {
            $.ajax({
              url : ajax_url,
              success : function (response) {
                anchor.addClass('expanded');
                anchor_row.addClass('expanded');
                anchor_row.removeClass('collapsed');
                anchor_image.attr('src', App.data.expander_expanded);
                checklist_tasks_row.hide();
                checklist_tasks_row.html(response);
                checklist_tasks_row.slideDown();
              },
              error : function(response) {
                anchor_image.attr('src', App.data.expander_collapsed);
              }
            });
          } else {
            anchor.addClass('expanded');
            anchor_row.addClass('expanded');
            anchor_row.removeClass('collapsed');
            anchor_image.attr('src', App.data.expander_expanded);
            checklist_tasks_row.slideDown();
          } // if
        } else if (anchor.is('.expanded')) {
          anchor.addClass('collapsed');
          anchor_row.addClass('collapsed');
          anchor_row.removeClass('expanded');
          anchor_image.attr('src', App.data.expander_collapsed);
          checklist_tasks_row.slideUp();
        } // if
        return false;
      });
      
      if(App.data.can_manage_checklists) {
        $('#checklists .checklists_container').sortable({
          items: 'div.checklist',
          axis: 'y',
          distance: '3',
          handle: 'table:first',
          update: function (event, ui) {
            var ajax_data = {
              'submitted' : 'submitted'
            };
            
            var counter = 0;
            $('#checklists div.checklists_container div.checklist:not(.ui-sortable-placeholder)').each(function () {
              ajax_data['checklists[' + counter + ']'] = $(this).attr('checklist_id');
              counter++;
            });
            $.ajax({
              type : 'post',
              data : ajax_data,
              url : App.extendUrl(App.data.reorder_checklists_url, { async : 1 })
            })
          }
        });
        $('#checklists .checklists_container .checklist:not(.ui-sortable-placeholder) table').css('cursor', 'move');
      } // if
    });
  }
};

App.widgets.reorder_checklists = function() {
  var reorder_list;
  var reorder_form;

  return {
    init : function(list_id) {
      reorder_list = $('#' + list_id);
      reorder_form = reorder_list.parents('form');
      
      var list_container = $('#checklists .checklists_container:first');
      var list_prefix = 'checklist_';
      
      reorder_list.sortable({
        axis        : 'y'
      });
      
      reorder_form.find('.buttonHolder button').click(function () {
        reorder_form.block();
        reorder_form.ajaxSubmit({
          method  : 'post',
          url  : App.makeAsyncUrl(reorder_form.attr('action')),
          success : function (response) {
            reorder_form.find('input').each(function () {
              list_container.append($('#'+list_prefix+$(this).val()));
            });
            App.ModalDialog.close();
          },
          error : function (response) {
            reorder_form.unblock();
          }
        });
      });
    }
  }
}();

