<?php


  /**
   * Io_Path_Iterator
   *
   * @package net.evalcode.components
   * @subpackage io.path
   *
   * @author evalcode.net
   */
  class Io_Path_Iterator extends RecursiveDirectoryIterator
  {
    // CONSTRUCTION
    public function __construct($path_)
    {
      parent::__construct($path_, RecursiveDirectoryIterator::SKIP_DOTS|RecursiveDirectoryIterator::CURRENT_AS_PATHNAME);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function current()
    {
      return new Io_Path(parent::current());
    }
    //--------------------------------------------------------------------------
  }
?>
