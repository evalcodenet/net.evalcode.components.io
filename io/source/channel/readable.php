<?php


namespace Components;


  /**
   * Io_Channel_Readable
   *
   * @package net.evalcode.components
   * @subpackage io.channel
   *
   * @author evalcode.net
   */
  interface Io_Channel_Readable extends Io_Channel
  {
    // ACCESSORS
    /**
     * Reads into given {@code buffer_} until its defined limit is reached,
     * the buffer is full or optionally given (@code interrupt_) is passed
     * through the channel.
     *
     * Returns amount of read bytes/characters.
     *
     * @param \Components\Io_Buffer $buffer_ Target buffer.
     * @param integer $interrupt_ ASCII code for expected interrupt character.
     *
     * @return integer
     *
     * @throws \Components\Io_Exception
     */
    function read(Io_Buffer $buffer_, $interrupt_=null);
    //--------------------------------------------------------------------------
  }
?>
