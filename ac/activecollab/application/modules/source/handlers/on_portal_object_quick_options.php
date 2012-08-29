<?php

	/**
	 * Source module on_portal_object_quick_options event handler
	 *
	 * @package activeCollab.modules.source
	 * @subpackage handlers
	 */
	
	/**
	 * Populate portal object quick options
	 *
	 * @param NamedList $options
	 * @param ProjectObject $object
	 * @param Portal $portal
	 * @param Commit $commit
	 * @param string $file
	 * @return null
	 */
	function source_handle_on_portal_object_quick_options(&$options, $object, $portal = null, $commit = null, $file = null) {
		if(instance_of($object, 'Repository')) {
			$options->beginWith('source', array(
				'text' => lang('File Source'),
				'url'  => $object->getPortalBrowseUrl($portal, $commit, $file)
			));
			
			$options->addAfter('history', array(
				'text' => lang('File History'),
				'url'  => $object->getPortalFileHistoryUrl($portal, $commit, $file)
			), 'source');
			
			$options->addAfter('compare', array(
				'text' => lang('Compare'),
				'url'  => $object->getPortalFileCompareUrl($portal, $commit, $file)
			), 'history');
			
			$options->addAfter('download', array(
				'text' => lang('Download'),
				'url'  => $object->getPortalFileDownloadUrl($portal, $commit, $file)
			), 'compare');
		} // if
	} // source_handle_on_portal_object_quick_options

?>