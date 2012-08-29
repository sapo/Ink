<?php
  
  /**
   * Attach files from $_FILES to $to object
   * 
   * Returns number of attached files or an errors
   *
   * @param ProjectObject $to
   * @param User $user
   * @return integer
   */
  function attach_from_files(&$to, $user) {
    $attached = 0;
    
    if(is_foreachable($_FILES)) {
      foreach($_FILES as $file) {
        $attach = $to->attachUploadedFile($file, $user);
        if($attach && !is_error($attach)) {
          $attached++;
        } // if
      } // foreach
    } // if
    return $attached;
  } // attach_from_files
  
  
  /**
   * Attach files from some array to $to object
   * 
   * - $from keys : - path
   *                - filename
   *                - type
   *
   * @param array $from
   * @param ProjectObject $to
   */
  function attach_from_array(&$from, &$to) {
     $attached = 0;
     if (is_foreachable($from)) {
       foreach ($from as $file) {
        $attach = $to->attachFile($file['path'], $file['filename'], $file['type']);       
        if (is_error($attach) || !$attach) {
          $to->clearPendingFiles();
          return $attach;
        } else {
          $attached++;
        } // if
       } // foreach
     } // if
     return $attached;
  } // attach_from_array

?>