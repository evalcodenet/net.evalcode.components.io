<?php


namespace Components;


  /**
   * Io_Path_Iterator
   *
   * @api
   * @package net.evalcode.components.io
   * @subpackage path
   *
   * @author evalcode.net
   */
  class Io_Path_Iterator extends \RecursiveDirectoryIterator // TODO (CSH) Iterator
  {
    // CONSTRUCTION
    public function __construct($path_)
    {
      parent::__construct($path_, \RecursiveDirectoryIterator::SKIP_DOTS|\RecursiveDirectoryIterator::CURRENT_AS_PATHNAME);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \RecursiveDirectoryIterator::current() \RecursiveDirectoryIterator::current()
     *
     * @return \Components\Io_Path_Iterator
     */
    public function current()
    {
      return new Io_Path(parent::current());
    }
    //--------------------------------------------------------------------------
  }
?>
