<?php


namespace Components;


  /**
   * Io_Channel_Flushable
   *
   * @package net.evalcode.components
   * @subpackage io.channel
   *
   * @author evalcode.net
   */
  interface Io_Channel_Flushable extends Io_Channel_Writable
  {
    // ACCESSORS
    /**
     * @return Components\Io_Channel_Flushable
     *
     * @throws Components\Io_Exception
     */
    function flush();
    //--------------------------------------------------------------------------
  }
?>
