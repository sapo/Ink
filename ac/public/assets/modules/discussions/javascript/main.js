App.discussions = {
  controllers : {},
  models      : {}
};

/**
 * Main discussions JS file
 */
App.discussions.controllers.discussions = {
  view : function() {
    $(document).ready(function() {
      $('#object_quick_option_details').click(function () {
        $('.discussion_details_toggled').toggle();
        return false;
      });
    });
  }
}