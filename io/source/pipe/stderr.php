<?php


  /**
   * Io_Pipe_Stderr
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   */
  class Io_Pipe_Stderr extends Io_Pipe_Sink
  {
    // CONSTRUCTION
    public function __construct($descriptor_='php://stderr', $flags_='a')
    {
      parent::__construct($descriptor_, $flags_);
    }
    //--------------------------------------------------------------------------
  }
?>