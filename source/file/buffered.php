<?php


namespace Components;


  /**
   * Io_File_Buffered
   *
   * @package net.evalcode.components
   * @subpackage io.file
   *
   * @author evalcode.net
   */
  class Io_File_Buffered extends Io_File
  {
    // OVERRIDES
    /**
     * @return string
     */
    public function getContent()
    {
      return file_get_contents($this->m_pathAsString);
    }

    /**
     * @param string $string_
     *
     * @return \Components\Io_File
     */
    public function setContent($string_)
    {
      $written=parent::setContent($string_);

      if($this->m_accessMask&self::APPEND)
        $this->m_buffer.=$string_;
      else
        $this->m_buffer=$string_;

      $this->m_bufferPosition=$this->m_length;

      return $written;
    }

    /**
     * @param integer $mask_
     *
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function open()
    {
      parent::open();

      if($this->m_accessMask&self::APPEND)
        $this->m_bufferPosition=$this->m_length;
      else
        $this->m_bufferPosition=0;

      $this->m_eof=$this->m_bufferPosition===$this->m_length;

      return $this;
    }

    /**
     * @param integer $bytes_
     *
     * @return string
     *
     * @throws \Components\Io_Exception
     */
    public function read($bytes_=4096)
    {
      $bytes=abs($bytes_);
      $size=strlen($this->m_buffer);

      if($size<$this->m_bufferPosition)
      {
        fseek($this->m_pointer, $size);

        if(false===($read=fread($this->m_pointer, $this->m_bufferPosition)))
          throw new Io_Exception('io/file', sprintf('Unable to read from file [%s].', $this));

        $this->m_buffer.=$read;
        $size=$this->m_bufferPosition;
      }

      while(($this->m_bufferPosition+$bytes_)>$this->m_position
        && false===($this->m_eof=feof($this->m_pointer)))
      {
        if(false===($read=fread($this->m_pointer, ($this->m_bufferPosition+$bytes)-$this->m_position)))
          throw new Io_Exception('io/file', sprintf('Unable to read from file [%s].', $this));

        $this->m_buffer.=$read;
        $this->m_position=ftell($this->m_pointer);
      }

      if(0>$bytes_)
      {
        if(0>($this->m_bufferPosition+$bytes_))
          $bytes_=-$this->m_bufferPosition;

        return substr($this->m_buffer, $this->m_bufferPosition+=$bytes_, $bytes);
      }

      if(($this->m_bufferPosition+$bytes_)>$this->m_position)
        $bytes=$this->m_bufferPosition-$this->m_position;

      if($read=substr($this->m_buffer, $this->m_bufferPosition, $bytes))
        $this->m_bufferPosition+=$bytes;

      return $read;
    }

    /**
     * @param string $linefeed_
     *
     * @return string
     */
    public function readLine()
    {
      $buffer=array();
      $bytes=4096;

      while(false!==($read=$this->read($bytes)))
      {
        if(false===($idx=strpos($read, $this->linefeed)))
        {
          $buffer[]=$read;
        }
        else
        {
          if($bytes>($len=strlen($read)))
            $bytes=$len;

          $this->m_bufferPosition-=($bytes-$idx-1);

          return implode($buffer).substr($read, 0, $idx);
        }
      }

      if(0<strlen($line=implode($buffer)))
        return $line;

      return false;
    }

    /**
     * @param string $string_
     *
     * @return integer
     *
     * @throws \Components\Io_Exception
     */
    public function write($string_)
    {
      if(false===$this->m_writable)
        throw new Io_Exception('io/file', sprintf('File is not writable [%s].', $this));

      if(1>($length=strlen($string_)))
        return 0;

      if($this->m_position!=$this->m_bufferPosition)
        fseek($this->m_pointer, $this->m_bufferPosition, SEEK_SET);

      if(0===($written=fwrite($this->m_pointer, $string_)))
        throw new Io_Exception('io/file', sprintf('Unable to write to file [%s].', $this));

      $this->m_position=$this->m_bufferPosition+$written;
      if($this->m_length<$this->m_position)
        $this->m_length=$this->m_position;

      $this->m_buffer='';

      return $written;
    }

    public function writeLine($string_)
    {
      return $this->write($string_.$this->linefeed);
    }

    public function append($string_)
    {
      $position=$this->m_bufferPosition;
      $this->m_bufferPosition=$this->m_length;

      $written=$this->write($string_);

      $this->m_bufferPosition=$position;

      return $written;
    }

    public function appendLine($string_)
    {
      $position=$this->m_bufferPosition;
      $this->m_bufferPosition=$this->m_length;

      $written=$this->writeLine($string_);

      $this->m_bufferPosition=$position;

      return $written;
    }

    /**
     * @param integer $length_
     *
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function truncate($length_=null)
    {
      if(0===$length_)
      {
        if(false===ftruncate($this->m_pointer, $length_))
        {
          if(false===$this->m_writable)
            throw new Io_Exception('io/file', sprintf('File is not writable [%s].', $this));

          throw new Io_Exception('io/file', sprintf('Failed to truncate file [%s].', $this));
        }

        $this->m_buffer='';
        $this->m_bufferPosition=0;
        $this->m_position=0;
        $this->m_length=0;

        return $this;
      }

      if(null===$length_)
        $length_=$this->m_bufferPosition;

      if($length_>=$this->m_length)
        return $this;

      if(false===ftruncate($this->m_pointer, $length_))
      {
        if(false===$this->m_writable)
          throw new Io_Exception('io/file', sprintf('File is not writable [%s].', $this));

        throw new Io_Exception('io/file', sprintf('Failed to truncate file [%s].', $this));
      }

      $this->m_length=$length_;

      if($this->m_position>$length_)
        $this->m_position=$length_;

      if($this->m_bufferPosition>$length_)
        $this->m_bufferPosition=$length_;

      if(strlen($this->m_buffer)>$this->m_bufferPosition)
        $this->m_buffer=substr($this->m_buffer, 0, $this->m_bufferPosition);

      return $this;
    }

    /**
     * @param integer $position_
     *
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function seek($position_)
    {
      if(-1===fseek($this->m_pointer, $position_, SEEK_CUR))
        throw new Io_Exception('io/file', sprintf('Unable to seek in file [%s].', $this));

      $this->m_position=ftell($this->m_pointer);

      return $this;
    }

    /**
     * @param integer $position_
     *
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function seekTo($position_)
    {
      $this->m_bufferPosition=$position_;

      return $this;
    }

    /**
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function seekToBegin()
    {
      $this->m_bufferPosition=0;

      return $this;
    }

    /**
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function seekToEnd()
    {
      $this->m_bufferPosition=$this->m_length;

      return $this;
    }

    /**
     * @param integer $bytes_
     *
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function skip($bytes_=1)
    {
      $this->m_bufferPosition+=$bytes_;

      return $this;
    }

    /**
     * @return integer
     *
     * @throws \Components\Io_Exception
     */
    public function position()
    {
      return $this->m_bufferPosition;
    }

    /**
     * @return boolean
     */
    public function isBegin()
    {
      return 0===$this->m_bufferPosition;
    }

    /**
     * @return boolean
     */
    public function isEnd()
    {
      return $this->m_eof && $this->m_bufferPosition>=$this->m_length;
    }

    /**
     * @return integer
     */
    public function length()
    {
      return $this->m_length;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_buffer='';
    private $m_bufferPosition;
    private $m_eof;
    //--------------------------------------------------------------------------
  }
?>
