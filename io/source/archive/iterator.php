<?php


  /**
   * Io_Archive_Iterator
   *
   * @package net.evalcode.components
   * @subpackage io.archive
   *
   * @author evalcode.net
   */
  class Io_Archive_Iterator extends RecursiveIterator
  {
    // CONSTRUCTION
    public function __construct(Io_Archive $archive_)
    {
      $this->m_archive=$archive_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function hasChildren()
    {

    }

    public function getChildren()
    {

    }

    public function current()
    {

    }

    public function next()
    {

    }

    public function key()
    {

    }

    public function valid()
    {

    }

    public function rewind()
    {

    }
    //--------------------------------------------------------------------------


    // IMLEMENTATION
    /**
     * @var Io_Archive
     */
    private $m_archive;
    //--------------------------------------------------------------------------
  }
?>