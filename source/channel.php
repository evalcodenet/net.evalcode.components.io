<?php


namespace Components;


  /**
   * Io_Channel
   *
   * @api
   * @package net.evalcode.components.io
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
     * @throws \Components\Io_Exception
     */
    function open();
    //--------------------------------------------------------------------------
  }
?>
