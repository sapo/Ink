tinymce.create('tinymce.plugins.ac_link_dialog', {
    /**
     * Plugin init method
     */
    init : function(ed, url) {
      ed.addButton('ac_link_dialog_insert', {
          title : App.lang('Insert Link'),
          onclick : function() {
            App.widgets.EditorLinkPicker.show(ed);
          },
          'class' : 'mce_link' // we use system icon
      });
      
      /*
			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setDisabled('ac_link_dialog_insert', co && n.nodeName != 'A');
				cm.setActive('ac_link_dialog_insert', n.nodeName == 'A' && !n.name);
			});
			*/
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
tinymce.PluginManager.add('ac_link_dialog', tinymce.plugins.ac_link_dialog);

