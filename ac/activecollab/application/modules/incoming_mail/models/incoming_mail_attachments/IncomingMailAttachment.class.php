<?php

  /**
   * IncomingMailAttachment class
   */
  class IncomingMailAttachment extends BaseIncomingMailAttachment {
  
    /**
     * Delete incoming mail attachment
     *
     * @return boolean
     */
    function delete() {
      @unlink(INCOMING_MAIL_ATTACHMENTS_FOLDER.'/'.$this->getTemporaryFilename());
      return parent::delete();
    } // delete
  
  }

?>