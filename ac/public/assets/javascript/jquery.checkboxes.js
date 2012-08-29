/**
 * Build behavior that is required for a group of checkboxes, action select box 
 * and submit button to work as expected
 *
 * Settings:
 *
 * - master_checkbox_class - class of master checkbox
 * - slave_checkbox_class - class that slave checkboxe use
 * - select_action_class - class of select action box (needs to be SELECT element)
 * - submit_action_class - class of submit button box (needs to be BUTTON element)
 */
jQuery.fn.checkboxes = function(settings) {
  settings = jQuery.extend({
    master_checkbox_class : 'master_checkbox',
    slave_checkbox_class  : 'slave_checkbox'
  }, settings);
  
  return this.each(function() {
    var parent = $(this);
    
    var master_checkbox = parent.find('input.' + settings.master_checkbox_class);
    var slave_checkboxes = parent.find('input.' + settings.slave_checkbox_class);
    var select_action = jQuery('#' + parent.attr('id') + '_action');
    var submit_action = jQuery('#' + parent.attr('id') + '_submit');
    
    /**
     * Simple handler that is called when we change something to see if submit 
     * button needs to be enabled or disabled
     */
    var enabled_disable_submit_button = function() {
      var submit_enabled = (select_action.val() != '') && (parent.find('input.' + settings.slave_checkbox_class + ':checked').length > 0);
      if(submit_enabled) {
        submit_action.attr('disabled', '').removeClass('button_disabled');
      } else {
        submit_action.attr('disabled', 'submit').addClass('button_disabled');
      } // if
    } // enabled_disable_submit_button
    
    // execute button disable function on initialisation
    enabled_disable_submit_button();
    
    /**
     * Click on master checkbox checks or unchecks all slave checkboxes (plus 
     * submit disabled / enabled value needs to be refreshed)
     */
    master_checkbox.click(function() {
      if(this.checked) {
        slave_checkboxes.checkCheckboxes();
      } else {
        slave_checkboxes.uncheckCheckboxes();
      } // if
      enabled_disable_submit_button();
    });
    
    /**
     * Click on slave checkbox can change checked value of master checkbox
     */
    slave_checkboxes.click(function() {
      var all_checked = true;
      slave_checkboxes.each(function() {
        if(!this.checked) {
          all_checked = false;
        } // if
      });
      
      master_checkbox[0].checked = all_checked;
      enabled_disable_submit_button();
    });
    
    /**
     * Select box change also results in recheck whether submit button needs to 
     * be enabled or disabled
     */
    select_action
      .change(enabled_disable_submit_button)
      .click(enabled_disable_submit_button)
      .keypress(enabled_disable_submit_button);
    
  }); // each
};

/**
 * Mark all checkboxes as checked
 */
jQuery.fn.checkCheckboxes = function() {
  return this.each(function() {
    this.checked = true;
  });
};

/**
 * Mark all checkboxes as unchecked
 */
jQuery.fn.uncheckCheckboxes = function() {
  return this.each(function() {
    this.checked = false;
  });
};