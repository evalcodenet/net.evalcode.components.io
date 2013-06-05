<?php


namespace Components;


  /**
   * Io_File_Csv
   *
   * @package net.evalcode.components
   * @subpackage io.file
   *
   * @author evalcode.net
   */
  class Io_File_Csv extends Io_File
  {
    // PREDEFINED PROPERTIES
    const QUOTES_NONE=1;
    const QUOTES_SINGLE=2;
    const QUOTES_DOUBLE=3;
    const QUOTES_DEFAULT=self::QUOTES_DOUBLE;

    const FIELD_SEPARATOR_TAB=1;
    const FIELD_SEPARATOR_COMMA=2;
    const FIELD_SEPARATOR_SEMIKOLON=3;
    const FIELD_SEPARATOR_SPACE=4;
    const FIELD_SEPARATOR_DEFAULT=self::FIELD_SEPARATOR_COMMA;

    const LINE_SEPARATOR_DEFAULT=Io::LINE_SEPARATOR_DEFAULT;
    //--------------------------------------------------------------------------


    // PROPERTIES
    public $quotes=self::QUOTES_DEFAULT;
    public $separatorField=self::FIELD_SEPARATOR_DEFAULT;
    public $separatorLine=self::LINE_SEPARATOR_DEFAULT;
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    public function getColumns()
    {
      return $this->m_columns;
    }

    public function setColumns(array $columns_)
    {
      $this->m_columns=$columns_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * (non-PHPdoc)
     * @see \Components\Io_File::open()
     */
    public function open()
    {
      parent::open();

      if($this->m_open)
      {
        if(1>count($this->m_columns))
        {
          $header=array();
          while(false===$this->isEnd())
          {
            $line=$this->read(512);

            if(-1<($idx=String::indexOf($line, self::LINE_SEPARATOR_DEFAULT)))
            {
              $header[]=String::substring($line, 0, $idx);

              break;
            }

            $header[]=$line;
          }
        }
      }

      return $this;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_columns=array();
    //--------------------------------------------------------------------------
  }
?>
