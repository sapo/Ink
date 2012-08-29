// uniform validation helper
window.tiny_value_present = function(field, caption) {
  if(tinyMCE && tinyMCE.activeEditor) {
    if(tinyMCE.activeEditor.getContent().length < 1) {
      return App.lang('Required');
    } // if
  } else {
    if(field.val() == '') {
      return App.lang('Required');
    } // if
  }
  return true;
}

var tiny_mce_editor;
var tiny_mce_editor_iframe;
var tiny_mce_editor_span_container;

// initialize editor
window.tinyMCE.init({
  mode: "specific_textareas",
  textarea_trigger : 'mce_editable',
  width: "100%",
  plugins: "safari,ac_image_dialog,ac_link_dialog,ac_paste_dialog",
  browsers : "msie,gecko,opera,safari",
  accessibility_focus : false,
  gecko_spellcheck: true,
  remove_linebreaks : true,
  apply_source_formatting : false,
  convert_newlines_to_brs : true,
  relative_urls : false,
  absolute_urls : true,
  convert_urls : false,
  init_instance_callback : "tinyMCEPostInit",
  theme : "advanced",
  theme_advanced_toolbar_location : "top",
  theme_advanced_toolbar_align : "left",
  theme_advanced_path : false,
  theme_advanced_statusbar_location : "bottom",
  theme_advanced_buttons1 : "undo, redo, separator, formatselect, styleselect, bold, italic, underline, strikethrough, separator, bullist, numlist, separator, outdent, indent, separator, ac_link_dialog_insert, unlink, ac_image_dialog, separator, ac_paste_dialog, removeformat",
  theme_advanced_buttons2 : "",
  theme_advanced_buttons3 : "",
  theme_advanced_resizing : true,
  theme_advanced_resize_horizontal : false,
  theme_advanced_styles : App.lang('Title') + '=title;' + App.lang('Subtitle') + '=subtitle;' + App.lang('Quote') + '=quote;' + App.lang('Important') + '=important;' + App.lang('Note') + '=note;' + App.lang('Updated') + '=updated',
  valid_elements : "" +
    "#p[id|style|dir|class|align]," + 
    "+a[href|target|title|class]," + 
    "-strong/-b[class|style]," + 
    "-em/-i[class|style]," + 
    "-strike[class|style]," + 
    "-u[class|style]," + 
    "-ol[class|style]," + 
    "-ul[class|style]," + 
    "-li[class|style]," + 
     "br," + 
     "img[id|dir|lang|longdesc|usemap|style|class|src|border|alt=|title|hspace|vspace|width|height|align]," + 
    "-sub[style|class]," + 
    "-sup[style|class]," + 
    "-blockquote[dir|style]," + 
    "-div[id|dir|class|align|style]," + 
    "-span[style|class|align]," + 
    "-pre[class|align|style]," + 
     "address[class|align|style]," + 
    "-h1[id|style|dir|class|align]," + 
    "-h2[id|style|dir|class|align]," + 
    "-h3[id|style|dir|class|align]," + 
    "-h4[id|style|dir|class|align]," + 
    "-h5[id|style|dir|class|align]," + 
    "-h6[id|style|dir|class|align]," + 
     "hr[class|style]"
});

adjustHeight = function () {
  if (!window.tinyMCE.activeEditor._doc_element || !window.tinyMCE.activeEditor._iframe_element) {
    return false;
  } // if
  
  var inner_body_height;
  var iframe_height = window.tinyMCE.activeEditor._iframe_element.height();
  if ($.browser.msie) {
    // IE
    inner_body_height = window.tinyMCE.activeEditor._body_element.attr('scrollHeight');
  } else if ($.browser.safari && (App.compareVersions('530', $.browser.version) == 1)) {
    // SAFARI AND CHROME (webkit < 530)
    var last_element = window.tinyMCE.activeEditor._body_element.find('> *:last');
    if (last_element.length > 0) {
      var last_element_position = last_element.position();
      inner_body_height = last_element_position.top + last_element.height() + parseInt(last_element.css('marginBottom')) + parseInt(last_element.css('paddingBottom')) + 20;
    } else {

      inner_body_height = 0;
    } // if
  } else {
    // OTHERS
    inner_body_height = window.tinyMCE.activeEditor._body_element.height();
  } // if
   
  var new_height = inner_body_height + 25;   
  if ( inner_body_height > iframe_height ) {
    window.tinyMCE.activeEditor._iframe_element.css('height', new_height + 'px');
  } // if
  setTimeout("adjustHeight()",250);
};

function tinyMCEPostInit(ed) {
  tiny_mce_editor = $(ed);
  tiny_mce_editor_iframe = $('#' + tiny_mce_editor.attr('id') + '_ifr');
  
  // object on which we hook blur i focus events
  var hook_nod = ed.settings.content_editable ? ed.getBody() : (tinymce.isGecko ? ed.getDoc() : ed.getWin());
  
  tiny_mce_editor_span_container = $(ed.contentAreaContainer).parents('span.mceEditor');
  
  // find objects that are important for uniform validation
  var parent_form = $(ed.contentAreaContainer);
  while(parent_form[0].nodeName != 'FORM') {
    parent_form = parent_form.parent();
  } // if
  var textarea = $(ed.getElement());
  
  // variables needed for resizing
  ed._doc_element = ed.getDoc();
  ed._body_element = $(ed.getDoc()).find('body:first');
  ed._iframe_element = $(ed.contentAreaContainer).find('iframe:first');
  
  // hook events
  tinymce.dom.Event.add(hook_nod, 'focus', function(e) {
    UniForm.focus_field(parent_form,textarea);
    if (!tiny_mce_editor_span_container.is('expanded')) {
      tiny_mce_editor_span_container.addClass('expanded');
    } // if
  });
  tinymce.dom.Event.add(hook_nod, 'blur', function(e) {
    UniForm.validate(parent_form, false);
  });
  tinymce.dom.Event.add(hook_nod, 'keypress', function(event) {
    if ((event.keyCode == 37) && (event.metaKey == true)) {
      return false;
    } // if
  });
  
  if ((textarea.attr('auto_expand') && (textarea.attr('auto_expand') != 'no'))) {
    ed._iframe_element.css('overflow-y', 'hidden');
    adjustHeight();
  } // if
} // tinyMCEPostInit