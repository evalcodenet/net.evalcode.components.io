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
  class Io_File_Csv extends Io_File implements \IteratorAggregate
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


    // ACCESSORS/MUTATORS
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
        $columnsIndex=array();
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

    public function getColumnsIndex()
    {
      return $this->m_columnsIndex;
    }

    public function hasMoreLines()
    {
      if(isset($this->m_data[$this->m_line+1]))
        return true;

      try
      {
        $this->readLine();
      }
      catch(Io_Exception $e)
      {
        return false;
      }

      $this->m_line--;

      return isset($this->m_data[$this->m_line+1]);
    }

    public function nextLine()
    {
      return $this->readLine();
    }

    public function previousLine()
    {
      return $this->m_data[--$this->m_line];
    }

    public function currentLine()
    {
      if(false===isset($this->m_data[$this->m_line]))
        return null;

      return $this->m_data[$this->m_line];
    }

    public function currentLineNumber()
    {
      return $this->m_line;
    }

    public function readAll()
    {
      while(false===feof($this->m_pointer))
        $this->readLine();

      return $this;
    }

    public function find($value_, $column_)
    {
      // TODO index & query / maybe cache in mongodb?
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * (non-PHPdoc)
     * @see Components\Iterable::__clone()
     *
     * @return \Components\Io_File_Csv_Iterator
     */
    public function getIterator()
    {
      return new Io_File_Csv_Iterator($this);
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Io_File::open()
     */
    public function open()
    {
      parent::open();

      $this->readHeader();

      return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Io_File::readLine()
     *
     * @return array|string
     *
     * @throws \Components\Io_Exception
     */
    public function readLine()
    {
      $this->m_line++;

      if(isset($this->m_data[$this->m_line]))
        return $this->m_data[$this->m_line];

      $start=ftell($this->m_pointer);

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
          return array();

        throw new Io_Exception('io/file/csv', sprintf('Failed to read line [%s].', $this));
      }

      $this->m_offsetLines[$this->m_line]=array($start, $this->m_position);

      $data=array();
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
          foreach($columns as $column)
          {
            if(isset($line[$column]) && trim($line[$column]))
              $data[$name][$column]=$line[$column];
          }
        }
      }

      $this->m_data[$this->m_line]=$data;

      return $data;
    }

    /**
     * @param integer $line_
     *
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function truncate($line_=0)
    {
      $line_=max(0, $line_);

      if(0===$line_)
      {
        ftruncate($this->m_pointer, $this->m_offsetHeaderEnd);

        $this->m_data=array();
        $this->m_offsetLines=array();
        $this->m_position=$this->m_offsetHeaderEnd;
        $this->m_length=$this->m_offsetHeaderEnd;
        $this->m_line=0;

        return $this;
      }

      if(false===isset($this->m_offsetLines[$line_]))
      {
        while($this->m_line<$line_)
          $this->readLine();
      }

      ftruncate($this->m_pointer, $this->m_offsetLines[$line_][0]);

      $this->m_data=array_slice($this->m_data, 0, $line_);
      $this->m_offsetLines=array_slice($this->m_offsetLines, 0, $line_);
      $this->m_position=$this->m_offsetLines[$line_][0];
      $this->m_length=$this->m_offsetLines[$line_][0];
      $this->m_line=$line_;

      return $this;
    }

    /**
     * @param integer $line_
     *
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function seek($line_=1)
    {
      $line_=$this->m_line+$line_;

      if(isset($this->m_offsetLines[$line_]))
      {
        fseek($this->m_pointer, $this->m_offsetLines[$line_][1], SEEK_SET);

        $this->m_position=$this->m_offsetLines[$line_][1];
        $this->m_line=$line_;
      }
      else
      {
        // XXX Throw IllegalState!?
        if($line_<$this->m_line)
          $this->seekToBegin();

        while($this->m_line<$line_)
          $this->readLine();
      }

      return $this;
    }

    /**
     * @param integer $line_
     *
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function seekTo($line_)
    {
      $line_=max(0, $line_);

      if(isset($this->m_offsetLines[$line_]))
      {
        fseek($this->m_pointer, $this->m_offsetLines[$line_][1], SEEK_SET);

        $this->m_position=$this->m_offsetLines[$line_][1];
        $this->m_line=$line_;
      }
      else
      {
        // XXX Throw IllegalState!?
        if($line_<$this->m_line)
          $this->seekToBegin();

        while($this->m_line<$line_)
          $this->readLine();
      }

      return $this;
    }

    /**
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function seekToBegin()
    {
      return $this->seekTo(0);
    }

    /**
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function seekToEnd()
    {
      if($last=end($this->m_offsetLines))
      {
        fseek($this->m_pointer, $last[1], SEEK_SET);
        $this->m_line=key($this->m_offsetLines);
      }

      return $this;
    }

    /**
     * @param integer $lines_
     *
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    // FIXME Inconsistent state (data buffer, offset buffers..)?
    public function skip($lines_=1)
    {
      while(0<$lines_--)
      {
        fgetcsv(
          $this->m_pointer,
          null,
          chr($this->characterFieldSeparator),
          chr($this->characterQuotes),
          chr($this->characterEscape)
        );
      }

      $this->m_position=ftell($this->m_pointer);

      return $this;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_columns=array();
    private $m_columnsIndex=array();
    private $m_columnsMapped=array();
    private $m_offsetLines=array();
    private $m_data=array();
    private $m_line=0;
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
          return array();

        throw new Io_Exception('io/file/csv', sprintf('Failed to read line [%s].', $this));
      }

      if(0<count($this->m_columnsIndex))
      {
        $columnsIndex=array();
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
