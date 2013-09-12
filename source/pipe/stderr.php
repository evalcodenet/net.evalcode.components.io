<?php


namespace Components;


  /**
   * Io_Pipe_Stderr
   *
   * @api
   * @package net.evalcode.components.io
   * @subpackage pipe
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
