<?php


namespace Components;


  /**
   * Io_Channel_Closeable
   *
   * @api
   * @package net.evalcode.components.io
   * @subpackage channel
   *
   * @author evalcode.net
   */
  interface Io_Channel_Closeable
  {
    // ACCESSORS
    /**
     * @return \Components\Io_Channel_Closeable
     *
     * @throws \Components\Io_Exception
     */
    function close();
    //--------------------------------------------------------------------------
  }
?>
