<?php


namespace Components;


  /**
   * Io_Channel
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   */
  interface Io_Channel extends Io_Channel_Closeable
  {
    // ACCESSORS
    /**
     * @return boolean
     */
    function isOpen();

    /**
     * Returns 'true' on success.
     *
     * @return boolean
     *
     * @throws Io_Exception
     */
    function open();
    //--------------------------------------------------------------------------
  }
?>
