<?php


namespace Components;


  /**
   * Io_File_Csv_Random
   *
   * @api
   * @package net.evalcode.components.io
   * @subpackage file.csv
   *
   * @author evalcode.net
   */
  class Io_File_Csv_Random extends Io_File_Csv
  {
    public function next()
    {
      $data=parent::next();

      $this->m_data[$this->m_line]=$data;

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
      while($this->hasMore())
        $this->next();

      return $this->m_data;
    }

    public function find($value_, $column_)
    {
      // TODO index & query / maybe cache in mongodb?
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_data=array();
    //--------------------------------------------------------------------------
  }
?>
