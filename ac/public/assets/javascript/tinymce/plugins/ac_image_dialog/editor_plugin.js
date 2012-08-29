tinymce.create('tinymce.plugins.ac_image_dialog', {
    /**
     * Plugin init method
     */
    init : function(ed, url) {
        ed.addButton('ac_image_dialog', {
            title : App.lang('Choose Uploaded Image'),
            onclick : function() {
              var variable_name;
              if ($('#'+ed.editorId)) {
                variable_name = $('#'+ed.editorId).attr('inline_attachments_name');
              } else {
                variable_name = 'inline_attachments[]';
              } // if
              App.widgets.EditorImagePicker.show(ed, variable_name);
            },
            'class' : 'mce_image' // we use system icon
        });
    },
    
    /**
     * Plugin get information method
     */   
		getInfo : function() {
			return {
				longname : 'Activecollab Image Dialog',
				author : 'a51dev.com',
				authorurl : 'http://a51dev.com',
				infourl : 'http://vbsupport.org/forum/index.php',
				version : '1.0'
			};
		}
});

// Register plugin with a short name
tinymce.PluginManager.add('ac_image_dialog', tinymce.plugins.ac_image_dialog);