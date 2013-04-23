<?php


  /**
   * Io_Channel_Flushable
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   */
  interface Io_Channel_Flushable extends Io_Channel_Writable
  {
    // ACCESSORS
    /**
     * Returns 'true' on success.
     *
     * @return boolean
     *
     * @throws Io_Exception
     */
    function flush();
    //--------------------------------------------------------------------------
  }
?>
