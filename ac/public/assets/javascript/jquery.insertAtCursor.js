/**
 * insert text at carret location.
 */
jQuery.fn.insertAtCursor = function(string_to_insert) {
  return this.each(function() {
    var input_field = $(this);

    if ($.browser.msie) {
      input_field.focus();
      document.selection.createRange().text = string_to_insert;
      input_field.focus();
    } else {
      // find selection boundaries
      var selection_start = input_field[0].selectionStart;
      var selection_end = input_field[0].selectionEnd;   
      
      if (selection_start || selection_start == '0') {
        // make new string and insert it in input
        var new_string = input_field.val().substring(0, selection_start) + string_to_insert + input_field.val().substring(selection_end, input_field.val().length);
        input_field.val(new_string);
        input_field[0].selectionStart = input_field[0].selectionEnd = selection_start + string_to_insert.length;       
      } else {
        input_field.val(input_field.val() + string_to_insert);
      } // if
      input_field.focus();
    } // if
  });
};