App.milestones = {
  controllers : {},
  models      : {}
};

/**
 * Main milestones JS file
 */
App.milestones.controllers.milestones = {
  
  /**
   * Prepare stuff on reschedule form
   *
   * @param void
   * @return null
   */
  reschedule : function() {
    $(document).ready(function() {
      
      // Lets hide successive milestone. There must be a better way to do this 
      // but we'll leave it at this for now :)
      $('div.with_successive_milestones input[type=radio]').each(function() {
        if(this.checked && $(this).val() != 'move_selected') {
          $('div.with_successive_milestones div.successive_milestones').hide();
        }
      })
      
      // Click handler for action selectors
      $('div.with_successive_milestones input[type=radio]').click(function() {
        if($(this).val() == 'move_selected') {
          $('div.with_successive_milestones div.successive_milestones').show('fast');
        } else {
          $('div.with_successive_milestones div.successive_milestones').hide('fast');
        }
      });
    });
  }
  
}