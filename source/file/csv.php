<?php


namespace Components;


  /**
   * Io_File_Csv
   *
   * @api
   * @package net.evalcode.components.io
   * @subpackage file
   *
   * @author evalcode.net
   */
  class Io_File_Csv extends Io_File implements Iterable, Countable
  {
    // PREDEFINED PROPERTIES
    const QUOTES_NONE=0x00;
    const QUOTES_SINGLE=0x27;
    const QUOTES_DOUBLE=0x22;
    const QUOTES_DEFAULT=self::QUOTES_DOUBLE;

    const FIELD_SEPARATOR_TAB=0x09;
    const FIELD_SEPARATOR_COMMA=0x2c;
    const FIELD_SEPARATOR_SEMIKOLON=0x3b;
    const FIELD_SEPARATOR_SPACE=0x20;
    const FIELD_SEPARATOR_DEFAULT=self::FIELD_SEPARATOR_COMMA;
    //--------------------------------------------------------------------------


    // PROPERTIES
    /**
     * @var integer
     */
    public $characterQuotes=self::QUOTES_DEFAULT;
    /**
     * @var integer
     */
    public $characterFieldSeparator=self::FIELD_SEPARATOR_DEFAULT;
    /**
     * @var integer
     */
    public $characterEscape=0x5c;
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function getColumns()
    {
      return $this->m_columns;
    }

    public function setColumns(array $columns_)
    {
      $this->m_columns=$columns_;

      // Match passed columns with existing columns.
      if($this->m_open && count($this->m_columnsIndex))
      {
        $columnsIndex=[];
        foreach($columns_ as $name)
        {
          if(isset($this->m_columnsIndex[$name]))
            $columnsIndex[$name]=$this->m_columnsIndex[$name];
        }

        $this->m_columnsIndex=$columnsIndex;
      }
      // Initialize with passed columns if file not opened yet.
      else
      {
        foreach($this->m_columns as $idx=>$name)
        {
          if(isset($this->m_columnsIndex[$name]))
            $this->m_columnsIndex[$name][$idx]=$idx;
          else
            $this->m_columnsIndex[$name]=array($idx=>$idx);
        }
      }
    }

    public function getColumnsMapped()
    {
      return $this->m_columnsMapped;
    }

    public function setColumnsMapped(array $columns_)
    {
      $this->setColumns(array_keys($columns_));
      $this->m_columnsMapped=$columns_;
    }

    public function getColumnsIndex()
    {
      return $this->m_columnsIndex;
    }

    public function mapColumn($from_, $to_)
    {
      if(false===isset($this->m_columnsIndex[$from_]))
      {
        if(1>count($this->m_columnsIndex))
        {
          throw new Exception_IllegalState('io/file/csv',
            'Columns must be initialized first. Either invoke io/file/csv#setColumns or io/file/csv#open() to initialize columns.'
          );
        }

        throw new Exception_IllegalArgument('io/file/csv', 'Can not map unknown column.');
      }

      $this->m_columnsMapped[$from_]=$to_;
    }

    public function hasMore()
    {
      return false===feof($this->m_pointer);
    }

    public function next()
    {
      if(0===$this->m_line)
        fseek($this->m_pointer, $this->m_offsetHeaderEnd);

      $this->m_line++;

      $line=fgetcsv(
        $this->m_pointer,
        null,
        chr($this->characterFieldSeparator),
        chr($this->characterQuotes),
        chr($this->characterEscape)
      );

      $this->m_position=ftell($this->m_pointer);

      if(null===$line)
        throw new Io_Exception('io/file/csv', sprintf('Failed to read line - file seems to be closed [%s].', $this));

      if(false===$line)
      {
        if(feof($this->m_pointer))
          return [];

        throw new Io_Exception('io/file/csv', sprintf('Failed to read line [%s].', $this));
      }

      $data=[];
      foreach($this->m_columnsIndex as $name=>$columns)
      {
        if(isset($this->m_columnsMapped[$name]))
          $name=$this->m_columnsMapped[$name];

        if(1===count($columns))
        {
          $column=reset($columns);
          if(isset($line[$column]) && trim($line[$column]))
            $data[$name]=$line[$column];
          else
            $data[$name]=null;
        }
        else
        {
          $data[$name]=[];
          foreach($columns as $column)
          {
            if(isset($line[$column]) && trim($line[$column]))
              $data[$name][$column]=$line[$column];
          }
        }
      }

      return $data;
    }

    public function currentLineNumber()
    {
      return $this->m_line;
    }

    public function seekToBegin()
    {
      parent::seekToBegin();

      $this->m_line=0;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Countable::count() \Components\Countable::count()
     */
    public function count()
    {
      $i=0;

      while(false===feof($this->m_pointer))
      {
        fgetcsv(
          $this->m_pointer,
          null,
          chr($this->characterFieldSeparator),
          chr($this->characterQuotes),
          chr($this->characterEscape)
        );

        $i++;
      }

      fseek($this->m_pointer, 0);

      return $i;
    }

    /**
     * @see \Components\Iterable::getIterator() \Components\Iterable::getIterator()
     *
     * @return \Components\Io_File_Csv_Iterator
     */
    public function getIterator()
    {
      return new Io_File_Csv_Iterator($this);
    }

    /**
     * @see \Components\Io_File::open() \Components\Io_File::open()
     *
     * @return \Components\Io_File_Csv
     */
    public function open()
    {
      parent::open();

      $this->readHeader();

      return $this;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected $m_line=0;

    private $m_columns=[];
    private $m_columnsIndex=[];
    private $m_columnsMapped=[];
    private $m_offsetHeaderStart=0;
    private $m_offsetHeaderEnd=0;
    //-----


    private function readHeader()
    {
      $position=ftell($this->m_pointer);
      fseek($this->m_pointer, 0);

      $header=fgetcsv(
        $this->m_pointer,
        null,
        chr($this->characterFieldSeparator),
        chr($this->characterQuotes),
        chr($this->characterEscape)
      );

      if(null===$header)
        throw new Io_Exception('io/file/csv', sprintf('Failed to read line - file seems to be closed [%s].', $this));

      if(false===$header)
      {
        if(feof($this->m_pointer))
          return [];

        throw new Io_Exception('io/file/csv', sprintf('Failed to read line [%s].', $this));
      }

      if(0<count($this->m_columnsIndex))
      {
        $columnsIndex=[];
        foreach($header as $idx=>$name)
        {
          if(isset($this->m_columnsIndex[$name]))
          {
            if(isset($columnsIndex[$name]))
              $columnsIndex[$name][$idx]=$idx;
            else
              $columnsIndex[$name]=array($idx=>$idx);
          }
        }

        if(1>count($columnsIndex))
          return $this->readHeader();

        $this->m_columnsIndex=$columnsIndex;
      }
      // Initialize with existing columns if nothing specified.
      else
      {
        if(1>count($header))
          return $this->readHeader();

        $this->setColumns($header);
      }

      $this->m_offsetHeaderStart=$position;
      $this->m_offsetHeaderEnd=ftell($this->m_pointer);
    }
    //--------------------------------------------------------------------------
  }
?>
