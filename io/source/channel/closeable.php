<?php


namespace Components;


  /**
   * Io_Channel_Closeable
   *
   * @package net.evalcode.components
   * @subpackage io.channel
   *
   * @author evalcode.net
   */
  interface Io_Channel_Closeable
  {
    // ACCESSORS
    /**
     * @return Components\Io_Channel_Closeable
     *
     * @throws Components\Io_Exception
     */
    function close();
    //--------------------------------------------------------------------------
  }
?>
