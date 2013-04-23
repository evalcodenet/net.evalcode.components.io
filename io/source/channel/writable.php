<?php


  /**
   * Io_Channel_Writable
   *
   * @package net.evalcode.components
   * @subpackage io
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
     * @param Io_Buffer $buffer_
     *
     * @return int
     *
     * @throws Io_Exception
     */
    function write(Io_Buffer $buffer_);
    //--------------------------------------------------------------------------
  }
?>
