tinymce.create('tinymce.plugins.ac_paste_dialog', {
    /**
     * Plugin init method
     */
    init : function(ed, url) {
      ed.addButton('ac_paste_dialog', {
          title : App.lang('Clean text and paste'),
          onclick : function() {
            App.widgets.EditorCleanTextDialog.show(ed);
          },
          'class' : 'mce_pastetext' // we use system icon
      });
    },
    
    /**
     * Plugin get information method
     */   
		getInfo : function() {
			return {
				longname : 'Activecollab Link Dialog',
				author : 'a51dev.com',
				authorurl : 'http://a51dev.com',
				infourl : 'http://vbsupport.org/forum/index.php',
				version : '1.0'
			};
		}
});

// Register plugin with a short name
tinymce.PluginManager.add('ac_paste_dialog', tinymce.plugins.ac_paste_dialog);

