<?php


namespace Components;


  /**
   * Io_File_Csv_Random
   *
   * @package net.evalcode.components
   * @subpackage io.file.csv
   *
   * @author evalcode.net
   */
  class Io_File_Csv_Random extends Io_File_Csv
  {
    public function next()
    {
      $data=parent::next();

      if(count($data))
        $this->m_data[$this->m_line-1]=$data;

      return $data;
    }

    public function previous()
    {
      if(1>$this->m_line)
        return false;

      return $this->m_data[--$this->m_line];
    }

    public function current()
    {
      if(false===isset($this->m_data[$this->m_line]))
        return null;

      return $this->m_data[$this->m_line];
    }

    public function findAll()
    {
      while(false===feof($this->m_pointer))
        $this->next();

      return $this->m_data;
    }

    public function find($value_, $column_)
    {
      // TODO index & query / maybe cache in mongodb?
    }

    public function count()
    {
      $this->findAll();

      $line=$this->m_line;
      $this->seekToBegin();

      return $line;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_data=array();
    //--------------------------------------------------------------------------
  }
?>
