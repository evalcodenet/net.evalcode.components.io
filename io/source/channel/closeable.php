<?php


namespace Components;


  /**
   * Io_Channel_Closeable
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   */
  interface Io_Channel_Closeable
  {
    // ACCESSORS
    /**
     * Returns 'true' on success.
     *
     * @return boolean
     *
     * @throws Io_Exception
     */
    function close();
    //--------------------------------------------------------------------------
  }
?>
