App.documents = {
	controllers : {},
	models      : {}
};

/**
 * Main files JS file
 */
App.documents.controllers.documents = {
	
	/**
   * Documents list page behavior
   */
	index : function() {
		
		$(document).ready(function() {
			$('.documents_table span.delete a').click(function() {
				var link = $(this);
				var wrapper = link.parent();
				var row = wrapper.parent().parent();
				var container = row.parent();
				
				/**
		     * Reindex table rows
		     */
		    var reindex_odd_even_rows = function() {
		      var counter = 1;
		      container.find('tr').each(function() {
		        var rows = $(this);
		        rows.removeClass('even').removeClass('odd');
		        if(counter % 2 == 1) {
		          rows.addClass('odd');
		        } else {
		          rows.addClass('even');
		        } // if
		        counter++;
		      });
		    } // reidex_table_rows
				
		    if(confirm(App.lang('Are you sure that you want to permanently delete this document?'))) {
					link.hide();
					wrapper.prepend('<img src="' + App.data.indicator_url  + '" alt="" />');
					
					$.ajax({
						url     : link.attr('href'),
						type    : 'POST',
						data    : {'submitted' : 'submitted'},
						success : function(response) {
							row.remove();
							reindex_odd_even_rows();
							
							if($('.documents_table tr').length < 1) {
								$('.documents_table').after('<p class="empty_page">' + App.lang('There are no documents to show.') + '</p>');
							} // if
						},
						error   : function() {
							link.show();
						}
					});
					
					return false;
				} // if
				
				return false;
			})
			
		$('.documents_table .pin a').click(function() {
				var link = $(this);
				var wrapper = link.parent();
				var row = wrapper.parent();
				var container = row.parent();
				
				/**
		     * Reindex table rows
		     */
		    var reindex_odd_even_rows = function() {
		      var counter = 1;
		      container.find('tr').each(function() {
		        var rows = $(this);
		        rows.removeClass('even').removeClass('odd');
		        if(counter % 2 == 1) {
		          rows.addClass('odd');
		        } else {
		          rows.addClass('even');
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
					if (link.is('.not_pinned')){
						container.prepend(row);
						row.highlightFade();
						row.find('img.indicator').remove();
						link.attr({
							'href'  : response
						}).show().find('img').attr({
							'src'   : App.data.pin_icon_url,
							'title' : App.lang('Unpin'),
							'alt'   : App.lang('Unpin')
						});
						link.removeClass('not_pinned').addClass('pinned')
					} else {
						container.append(row);
						row.highlightFade();
						row.find('img.indicator').remove();
						link.attr({
							'href'  : response
						}).show().find('img').attr({
							'src'   : App.data.unpin_icon_url,
							'title' : App.lang('Pin to top'),
							'alt'   : App.lang('Pin to top')
						});
						link.removeClass('pinned').addClass('not_pinned')
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
	}
	
}

/**
 * Main files JS file
 */
App.documents.controllers.document_categories = {
	
	/**
   * Manage categories page behavior
   */
	index : function() {
		$(document).ready(function() {
			$('.common_table span.delete a').click(function() {
				var link = $(this);
				var wrapper = link.parent();
				var row = wrapper.parent().parent();
				var container = row.parent();
				
				/**
         * Reindex table rows
         */
        var reindex_odd_even_rows = function() {
          var counter = 1;
          container.find('tr').each(function() {
            var rows = $(this);
            rows.removeClass('even').removeClass('odd');
            if(counter % 2 == 1) {
              rows.addClass('odd');
            } else {
              rows.addClass('even');
            } // if
            counter++;
          });
        };
				
				if(confirm(App.lang('Are you sure that you want to delete this document category? You will also delete all documents belongs to category!'))) {
	        link.hide();
					wrapper.prepend('<img src="' + App.data.indicator_url  + '" alt="" />');
					
					$.ajax({
						url     : link.attr('href'),
						type    : 'POST',
						data    : {'submitted' : 'submitted'},
						success : function(response) {
							row.remove();
							reindex_odd_even_rows();
							
							if($('.documents_table tr').length < 1) {
								$('.documents_table').after('<p class="empty_page">' + App.lang('All document categories are deleted.') + '</p>');
							} // if
						},
						error   : function() {
							link.show();
						}
					});
				} // if
				
				return false;
			});
		});
	}, // index
	
	/**
   * Documents list inside category page behavior
   */
	view : function() {
		$(document).ready(function() {
			$('.documents_table span.delete a').click(function() {
				var link = $(this);
				var wrapper = link.parent();
				var row = wrapper.parent().parent();
				var container = row.parent();
				
				/**
         * Reindex table rows
         */
        var reindex_odd_even_rows = function() {
          var counter = 1;
          container.find('tr').each(function() {
            var rows = $(this);
            rows.removeClass('even').removeClass('odd');
            if(counter % 2 == 1) {
              rows.addClass('odd');
            } else {
              rows.addClass('even');
            } // if
            counter++;
          });
        };
				
        if(confirm(App.lang('Are you sure that you want to permanently delete this document?'))) {
					link.hide();
					wrapper.prepend('<img src="' + App.data.indicator_url  + '" alt="" />');
					
					$.ajax({
						url     : link.attr('href'),
						type    : 'POST',
						data    : {'submitted' : 'submitted'},
						success : function(response) {
							row.remove();
							reindex_odd_even_rows();
							
							if($('.documents_table tr').length < 1) {
								$('.documents_table').after('<p class="empty_page">' + App.lang('All files from this page are deleted.') + '</p>');
							} // if
						},
						error   : function() {
							link.show();
						}
					});
					
					return false;
        } // if
				
				return false;
			})
			
			$('.documents_table .pin a').click(function() {
				var link = $(this);
				var wrapper = link.parent();
				var row = wrapper.parent();
				var container = row.parent();
				
				/**
		     * Reindex table rows
		     */
		    var reindex_odd_even_rows = function() {
		      var counter = 1;
		      container.find('tr').each(function() {
		        var rows = $(this);
		        rows.removeClass('even').removeClass('odd');
		        if(counter % 2 == 1) {
		          rows.addClass('odd');
		        } else {
		          rows.addClass('even');
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
					if (link.is('.not_pinned')){
						container.prepend(row);
						row.find('img.indicator').remove();
						link.attr({
							'href'  : response
						}).show().find('img').attr({
							'src'   : App.data.pin_icon_url,
							'title' : App.lang('Unpin'),
							'alt'   : App.lang('Unpin')
						});
						link.removeClass('not_pinned').addClass('pinned')
					} else {
						container.append(row);
						row.find('img.indicator').remove();
						link.attr({
							'href'  : response
						}).show().find('img').attr({
							'src'   : App.data.unpin_icon_url,
							'title' : App.lang('Pin to top'),
							'alt'   : App.lang('Pin to top')
						});
						link.removeClass('pinned').addClass('not_pinned')
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
	}
	
};

/**
 * Manage document categories behavior
 */
App.system.ManageDocumentCategories = function() {
  
  /**
   * Manage document categories tab used to initialize the popup
   *
   * This value is present only on pages where we have document categories tabs
   *
   * @var jQuery
   */
  var manage_document_categories_tab = false;
  
  /**
   * Initialize single document category table row
   *
   * @var jQuery
   */
  var init_row = function(row) {
    var table = row.parent().parent();
    
    // Rename document category
    row.find('td.options a.rename_document_category').click(function() {
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
        
        var new_document_category_name = jQuery.trim(input.val());
        if(new_document_category_name == '') {
          input[0].focus();
        } // if
        
        // Check if new document category name is already in use
        var name_used = false;
        table.find('td.name').each(function() {
          var current_row = $(this);
          if(current_row.attr('class').indexOf('renaming') == -1 && current_row.text() == new_document_category_name) {
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
            'category[name]' : new_document_category_name
          },
          success : function(response) {
            if(manage_document_categories_tab) {
              var document_category_id = row.attr('document_category_id');
              manage_document_categories_tab.parent().find('li').each(function() {
                if($(this).attr('document_category_id') == document_category_id) {
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
            
            alert(App.lang('Failed to rename selected document category'));
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
    
    // Delete document category
    row.find('td.options a.delete_document_category').click(function() {
      var link = $(this);
      
      // Block additional clicks
      if(link[0].block_clicks) {
        return false;
      } // if
      
      if(confirm(App.lang('Are you sure that you want to delete this document category? There is no undo for this operation!'))) {
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
            if(manage_document_categories_tab) {
              var document_category_id = row.attr('document_category_id');
              manage_document_categories_tab.parent().find('li').each(function() {
                if($(this).attr('document_category_id') == document_category_id) {
                  $(this).remove();
                } // if
              });
            } // if
            
            row.remove();
            if(table.find('tr').length > 0) {
              reindex_even_odd_rows(table);
            } else {
              table.hide();
              $('#manage_document_categories_empty_list').show();
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
     * Initialize manage document category popup
     *
     * @param String list_item_id
     */
    init : function(list_item_id) {
      manage_document_categories_tab = $('#' + list_item_id); // Remember manage document category tab!
      
      var link = manage_document_categories_tab.find('a');
      
      link.click(function() {
        var open_url = App.extendUrl(link.attr('href'), {
          skip_layout : 1,
          async : 1
        });
        
        App.ModalDialog.show('manage_document_categories_popup', App.lang('Manage Document Categories'), $('<p><img src="' + App.data.assets_url + '/images/indicator.gif" alt="" /> ' + App.lang('Loading...') + '</p>').load(open_url), {});
        return false;
      });
    },
    
    /**
     * Initialize document categoriess list behavior
     *
     * @param String wrapper_id
     */
    init_page : function(wrapper_id) {
      var wrapper = $('#' + wrapper_id);
      
      // New document categories implementation
      var form = wrapper.find('form');
      var new_document_category_input = form.find('input');
      var new_document_category_icon = form.find('img');
      
      var default_text = App.lang('New Document Category...');
      new_document_category_input.focus(function() {
        if(new_document_category_input.val() == default_text) {
          new_document_category_input.val('');
        } // if
      }).blur(function() {
        if(new_document_category_input.val() == '') {
          new_document_category_input.val(default_text);
        } // if
      }).val(default_text);
      
      // Submitting form indicator
      var submitting_new_document_category = false;
      
      // Click on + image
      new_document_category_icon.click(function() {
        if(!submitting_new_document_category) {
          form.submit();
        } // if
      });
      
      // Create new document category...
      form.submit(function() {
        if(submitting_new_document_category) {
          return false;
        } // if
        
        var new_document_category_name = jQuery.trim(new_document_category_input.val());
        if(new_document_category_name == '') {
          new_document_category_input[0].focus();
        } // if
        
        // Check if new document category name is already in use
        var name_used = false;
        wrapper.find('td.name').each(function() {
          var current_row = $(this);
          if(current_row.text() == new_document_category_name) {
            name_used = true;
            current_row.highlightFade();
          } // if
        });
        
        if(name_used) {
          return false;
        } // if
        
        var old_icon_url = new_document_category_icon.attr('src');
        
        submitting_new_document_category = true;
        new_document_category_input.attr('disabled', 'disabled');
        new_document_category_icon.attr('src', App.data.indicator_url);
        
        $.ajax({
          type : 'POST',
          url : App.extendUrl(form.attr('action'), { async : 1}),
          data : {
            'submitted' : 'submitted',
            'category[name]' : new_document_category_name
          },
          success : function(response) {
            $('#manage_document_categories_empty_list').hide();
            
            var new_row = $(response);
            var table = wrapper.find('table');
            
            table.append(new_row).show();
            init_row(new_row);
            reindex_even_odd_rows(table);
            
            new_row.find('td').highlightFade();
            
            new_document_category_input.attr('disabled', '').val('')[0].focus();
            submitting_new_document_category = false;
            new_document_category_icon.attr('src', old_icon_url);
            
            // Add to list of document category tabs
            if(manage_document_categories_tab) {
              var new_tab = $('<li><a><span></span></a></li>');
              
              new_tab.attr('document_category_id', new_row.attr('document_category_id'));
              
              var new_document_category_link = new_row.find('td.name a');
              new_tab.find('a').attr('href', new_document_category_link.attr('href'));
              new_tab.find('span').text(new_document_category_link.text());
              
              manage_document_categories_tab.before(new_tab);
            } // if
          },
          error : function() {
            submitting_new_document_category = false;
            new_document_category_input.attr('disabled', '');
            new_document_category_icon.attr('src', old_icon_url);
            
            alert(App.lang('Failed to create new document category ":name"', { 'name' :  new_document_category_name}));
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