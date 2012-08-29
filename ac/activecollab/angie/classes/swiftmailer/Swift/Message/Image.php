<?php

/**
 * Swift Mailer Image Component
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Message
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Message_EmbeddedFile");

/**
 * Embedded Image component for Swift Mailer
 * @package Swift_Message
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Message_Image extends Swift_Message_EmbeddedFile
{
  /**
   * Constructor
   * @param Swift_File The input source file
   * @param string The filename to use, optional
   * @param string The MIME type to use, optional
   * @param string The Content-ID to use, optional
   * @param string The encoding format to use, optional
   */
  function Swift_Message_Image($data=null, $name=null, $type="application/octet-stream", $cid=null, $encoding="base64")
  {
    if (!is_a($data, "Swift_File"))
    {
      trigger_error("Swift_Message_Image requires input file to be of type Swift_File.");
      return;
    }
    $this->Swift_Message_EmbeddedFile($data, $name, $type, $cid, $encoding);
  }
  /**
   * Set data for the image
   * This overrides setData() in Swift_Message_Attachment
   * @param Swift_File The data to set, as a file
   * @throws Swift_Message_MimeException If the image cannot be used, or the file is not
   */
  function setData(&$data, $read_filename=true)
  {
    if (!is_a($data, "Swift_File"))
    {
      trigger_error("Parameter 1 of " . __CLASS__ . "::" . __FUNCTION__ . " must be instance of Swift_File");
      return;
    }
    parent::setData($data, $read_filename);
    $img_data = @getimagesize($data->getPath());
    if (!$img_data)
    {
      Swift_Errors::trigger(new Swift_Message_MimeException(
        "Cannot use file '" . $data->getPath() . "' as image since getimagesize() was unable to detect a file format. " .
        "Try using Swift_Message_EmbeddedFile instead"));
      return;
    }
    $type = image_type_to_mime_type($img_data[2]);
    $this->setContentType($type);
    if (!$this->getFileName()) $this->setFileName($data->getFileName());
  }
}
