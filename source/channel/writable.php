<?php


namespace Components;


  /**
   * Io_Channel_Writable
   *
   * @api
   * @package net.evalcode.components.io
   * @subpackage channel
   *
   * @author evalcode.net
   */
  interface Io_Channel_Writable extends Io_Channel
  {
    // ACCESSORS
    /**
     * Writes contents of given buffer.
     * Returns amount of written bytes/characters.
     *
     * @param \Components\Io_Buffer $buffer_
     *
     * @return integer
     *
     * @throws \Components\Io_Exception
     */
    function write(Io_Buffer $buffer_);
    //--------------------------------------------------------------------------
  }
?>
