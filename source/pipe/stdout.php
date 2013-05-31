<?php


namespace Components;


  /**
   * Io_Pipe_Stdout
   *
   * @package net.evalcode.components
   * @subpackage io.pipe
   *
   * @author evalcode.net
   */
  class Io_Pipe_Stdout extends Io_Pipe_Sink
  {
    // CONSTRUCTION
    public function __construct($descriptor_='php://stdout', $flags_='a')
    {
      parent::__construct($descriptor_, $flags_);
    }
    //--------------------------------------------------------------------------
  }
?>
