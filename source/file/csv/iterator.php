<?php


namespace Components;


  /**
   * Io_File_Csv_Iterator
   *
   * @package net.evalcode.components
   * @subpackage io.file.csv
   *
   * @author evalcode.net
   */
  class Io_File_Csv_Iterator implements Iterator
  {
    // CONSTRUCTION
    public function __construct(Io_File_Csv $file_)
    {
      $this->m_file=$file_;
    }
    //--------------------------------------------------------------------------

    public function getInnerIterator()
    {
      return $this;
    }

    // OVERRIDES/IMPLEMENTS
    /**
     * (non-PHPdoc)
     * @see \Components\Iterator::current()
     */
    public function current()
    {
      return $this->m_file->currentLine();
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Iterator::key()
     */
    public function key()
    {
      return $this->m_file->currentLineNumber();
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Iterator::hasNext()
     */
    public function hasNext()
    {
      return $this->m_file->hasMoreLines();
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Iterator::hasPrevious()
     */
    public function hasPrevious()
    {
      return 0<$this->m_file->currentLineNumber();
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Iterator::next()
     */
    public function next()
    {
      return $this->m_file->readLine();
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Iterator::previous()
     */
    public function previous()
    {
      return $this->m_file->previousLine();
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Iterator::rewind()
     */
    public function rewind()
    {
      return $this->m_file->seekToBegin();
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Iterator::valid()
     */
    public function valid()
    {
      return null!==$this->m_file->currentLine();
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Io_File_Csv
     */
    private $m_file;
    //--------------------------------------------------------------------------
  }
?>
