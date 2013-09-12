<?php


namespace Components;


  /**
   * Io_Pipe_Stdin
   *
   * @api
   * @package net.evalcode.components.io
   * @subpackage pipe
   *
   * @author evalcode.net
   */
  class Io_Pipe_Stdin extends Io_Pipe_Source
  {
    // CONSTRUCTION
    public function __construct($descriptor_='php://stdin', $flags_='r')
    {
      parent::__construct($descriptor_, $flags_);
    }
    //--------------------------------------------------------------------------
  }
?>
