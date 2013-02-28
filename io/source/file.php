<?php


  /**
   * Io_File
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   */
  class Io_File implements Object, Cloneable
  {
    // PREDEFINED PROPERTIES
    /**
     * Read-only access (default).
     */
    const READ=0;
    /**
     * Write access.
     */
    const WRITE=1;
    /**
     * Create file on access.
     */
    const CREATE=2;
    /**
     * Lock file for exclusive access.
     * TODO Implement locking.
     */
    const LOCK=4;
    /**
     * Append (Implies CREATE/WRITE).
     */
    const APPEND=8;
    /**
     * Truncate (Implies CREATE/WRITE).
     */
    const TRUNCATE=16;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($path_, $accessModeMask_=self::READ)
    {
      $this->m_path=$path_;
      $this->m_accessMask=$accessModeMask_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $path_
     *
     * @return Io_File
     */
    public static function forPath($path_, $accessModeMask_=self::READ)
    {
      return new self($path_, $accessModeMask_);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @return string
     */
    public function getName()
    {
      if(null===$this->m_name)
        $this->m_name=@basename($this->m_path);

      return $this->m_name;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
      if(null===$this->m_extension)
        return strtolower(substr($this->m_path, strrpos($this->m_path, '.')+1));

      return $this->m_extension;
    }

    /**
     * @return Io_Path
     */
    public function getPath()
    {
      return new Io_Path($this->m_path);
    }

    /**
     * @return string
     */
    public function getPathAsString()
    {
      return $this->m_path;
    }

    /**
     * @return Io_Path
     */
    public function getDirectory()
    {
      return new Io_Path($this->getDirectoryAsString());
    }

    /**
     * @return string
     */
    public function getDirectoryAsString()
    {
      if(null===$this->m_directory)
        $this->m_directory=@dirname($this->m_path);

      return $this->m_directory;
    }

    /**
     * @throws Io_Exception
     *
     * @return Io_Filesize
     */
    public function getSize()
    {
      // TODO
      @clearstatcache(null, $this->m_path);
      if(false===($filesize=@filesize($this->m_path)))
        return new Io_FileSize(0);

      return new Io_Filesize($filesize);
    }

    /**
     * @return Io_MimeType
     */
    public function getMimeType()
    {
      if(null===$this->m_mimeType)
      {
        if($this->exists())
          return $this->m_mimeType=Io_MimeType::forFilePath($this->m_path);

        return $this->m_mimeType=Io_MimeType::forFileExtension($this->getExtension());
      }

      return $this->m_mimeType;
    }

    /**
     * @return Io_Charset
     */
    public function getCharset()
    {
      if($mimeType=$this->getMimeType())
        return $mimeType->charset();

      return Io_Charset::defaultCharset();
    }

    /**
     * @return boolean
     */
    public function isImage()
    {
      return $this->getMimeType()->isImage();
    }

    /**
     * @return Io_Image
     */
    public function asImage()
    {
      $image=new Io_Image($this->m_path, $this->m_accessMask);
      $image->m_name=$this->m_name;
      $image->m_directory=$this->m_directory;
      $image->m_extension=$this->m_extension;
      $image->m_mimeType=$this->m_mimeType;

      return $image;
    }

    /**
     * @param string $path_
     *
     * @return Io_File
     */
    public function getRelative($path_)
    {
      if(String::startsWith($path_, Io::DIRECTORY_SEPARATOR))
        return new self($path_);

      return new self($this->getDirectoryAsString().Io::DIRECTORY_SEPARATOR.$path_);
    }

    /**
     * @return string
     */
    public function getContent()
    {
      return @file_get_contents($this->m_path);
    }

    /**
     * @param string $string_
     *
     * @return Io_File
     */
    public function setContent($string_)
    {
      if(false===$this->isWritable())
      {
        if($this->m_accessMask&self::CREATE && false===@is_file($this->m_path))
        {
          $directory=$this->getDirectory();
          if(false===$directory->exists())
            $directory->create();
        }
        else
        {
          throw new Io_Exception('io/file', sprintf('File is not writable [%1$s].', $this));
        }
      }

      if($this->m_accessMask&self::APPEND)
        $mode=FILE_APPEND;
      else if($this->m_accessMask&self::LOCK)
        $mode=LOCK_EX;

      if(false===($written=@file_put_contents($this->m_path, $string_, $mode)))
        throw new Io_Exception('io/file', sprintf('Unable to write to file [%1$s].', $this));

      if($this->m_accessMask&self::APPEND)
        $this->m_length+=$written;
      else
        $this->m_length=$written;

      $this->m_position=$this->m_length;

      return $written;
    }

    public function getChecksum()
    {
      return crc32($this->getContent());
    }

    public function getHashMD5()
    {
      return @md5_file($this->m_path);
    }

    public function getHashSHA1()
    {
      return @sha1_file($this->m_path);
    }

    public function isReadable()
    {
      return @is_readable($this->m_path);
    }

    public function isWritable()
    {
      if(false===@is_writable($this->m_path))
        return false;

      if(0<($this->m_accessMask&(self::APPEND|self::TRUNCATE|self::CREATE)))
        return true;

      if(0<($this->m_accessMask&(self::WRITE)))
        return @is_file($this->m_path);

      return false;
    }

    /**
     * @return boolean
     */
    public function exists()
    {
      return @is_file($this->m_path);
    }

    /**
     * @return Io_File
     */
    public function create()
    {
      if(false===@touch($this->m_path))
      {
        $directory=$this->getDirectory();
        if(false===$directory->exists())
          $directory->create();
      }

      if(false===@touch($this->m_path))
        throw new Io_Exception('io/file', sprintf('Unable to create file in given location [%1$s].', $this));

      return $this;
    }

    public function delete()
    {
      if(false===@unlink($this->m_path))
        throw new Io_Exception('io/file', sprintf('Unable to delete file [%1$s].', $this));

      return $this;
    }

    /**
     * @param Io_File $destination_
     *
     * @return Io_File
     *
     * @throws Io_Exception
     */
    public function copy(Io_File $destination_, $autoCreateTargetDirectory_=false, $umaskTargetDirectory_=0775)
    {
      if(true===$autoCreateTargetDirectory_)
      {
        $targetDirectory_=$destination_->getDirectory();
        if(false===$targetDirectory_->exists())
          $targetDirectory_->create($umaskTargetDirectory_);
      }

      if(false===@copy($this->m_path, $destination_->m_path))
        throw new Io_Exception('io/file', sprintf('Unable to copy file to given destination [source: %1$s, destination: %2$s].', $this, $destination_));

      return $destination_;
    }

    public function copyInto(Io_Path $destination_, $autoCreateTargetDirectory_=false, $umaskTargetDirectory_=0775)
    {
      return $this->copy($destination_->getFile($this->m_name), $autoCreateTargetDirectory_, $umaskTargetDirectory_);
    }

    /**
     * @param Io_File $target_
     *
     * @return Io_File
     */
    public function move(Io_File $destination_, $autoCreateTargetDirectory_=false, $umaskTargetDirectory_=0775)
    {
      if(true===$autoCreateTargetDirectory_)
      {
        $targetDirectory=$destination_->getDirectory();
        if(false===$targetDirectory->exists())
          $targetDirectory->create($umaskTargetDirectory_);
      }

      if(false===@rename($this->m_path, $destination_->m_path))
      {
        if(@is_file($destination_->m_path))
          @unlink($this->m_path);
        else
          throw new Io_Exception('io/file', sprintf('Unable to move file to given destination [source: %1$s, destination: %2$s].', $this, $destination_));
      }

      return $destination_;
    }

    /**
     * @param Io_Path $target_
     * @param boolean $autoCreateTargetDirectory_
     * @param int $umaskTargetDirectory_
     *
     * @return Io_File
     */
    public function moveInto(Io_Path $destination_, $autoCreateTargetDirectory_=false, $umaskTargetDirectory_=0775)
    {
      return $this->move($destination_->getFile($this->m_name), $autoCreateTargetDirectory_, $umaskTargetDirectory_);
    }

    /**
     * @return boolean
     */
    public function isOpen()
    {
      return $this->m_open;
    }

    /**
     * @param int $mask_
     *
     * @return Io_File
     *
     * @throws Io_Exception
     */
    public function open()
    {
      if(false===($this->m_pointer=@fopen($this->m_path, $this->accessFlagsForMask($this->m_accessMask))))
        throw new Io_Exception('io/file', sprintf('Unable to open file [%1$s].', $this));

      if(null===$this->m_length)
      {
        if($this->m_accessMask^self::TRUNCATE)
          $this->m_length=@filesize($this->m_path);
        else
          $this->m_length=0;
      }

      if($this->m_accessMask&self::APPEND)
        $this->m_position=@fseek($this->m_pointer, 0, SEEK_END);
      else if(null===$this->m_position)
        $this->m_position=0;

      $this->m_writable=$this->isWritable();
      $this->m_open=true;

      return $this;
    }

    /**
     * @return Io_File
     *
     * @throws Io_Exception
     */
    public function close()
    {
      @fclose($this->m_pointer);

      $this->m_open=false;

      return $this;
    }

    /**
     * @param int $bytes_
     *
     * @return string
     *
     * @throws Io_Exception
     */
    public function read($bytes_=4096)
    {
      if(0>$bytes_)
        @fseek($this->m_pointer, $this->m_position-($bytes_=-$bytes_));

      if(false===($read=@fread($this->m_pointer, $bytes_)))
        throw new Io_Exception('io/file', sprintf('Unable to read from file [%1$s].', $this));

      $this->m_position=@ftell($this->m_pointer);

      return $read;
    }

    /**
     * @param string $string_
     *
     * @return int
     *
     * @throws Io_Exception
     */
    public function write($string_)
    {
      if(false===$this->m_writable)
        throw new Io_Exception('io/file', sprintf('File is not writable [%1$s].', $this));

      if(1>($length=strlen($string_)))
        return 0;

      if(0===($written=@fwrite($this->m_pointer, $string_, $length)))
        throw new Io_Exception('io/file', sprintf('Unable to write to file [%1$s].', $this));

      if($this->m_length<($this->m_position+=$written))
        $this->m_length=$this->m_position;

      return $written;
    }

    public function writeLine($string_, $separatorLine_=Io::LINE_SEPARATOR_DEFAULT)
    {
      return $this->write($string_.$separatorLine_);
    }

    public function append($string_)
    {
      if(false===$this->m_writable)
        throw new Io_Exception('io/file', sprintf('File is not writable [%1$s].', $this));

      if(1>($length=strlen($string_)))
        return 0;

      @fseek($this->m_pointer, $this->m_length, SEEK_SET);
      if(0===($written=@fwrite($this->m_pointer, $string_, $length)))
        throw new Io_Exception('io/file', sprintf('Unable to write to file [%1$s].', $this));

      $this->m_length+=$written;
      $this->m_position=$this->m_length;

      return $written;
    }

    public function appendLine($string_, $separatorLine_=Io::LINE_SEPARATOR_DEFAULT)
    {
      return $this->append($string_.$separatorLine_);
    }

    /**
     * @param int $length_
     *
     * @return Io_File
     *
     * @throws Io_Exception
     */
    public function truncate($length_=0)
    {
      if(false===@ftruncate($this->m_pointer, $length_))
        throw new Io_Exception('io/file', sprintf('Failed to truncate file [%1$s].', $this));

      $this->m_length=$length_;
      if($this->m_position>$length_)
        $this->m_position=$length_;

      return $this;
    }

    /**
     * @param int $position_
     *
     * @return Io_File
     *
     * @throws Io_Exception
     */
    public function seekTo($position_)
    {
      if(-1===@fseek($this->m_pointer, $position_, SEEK_SET))
        throw new Io_Exception('io/file', sprintf('Unable to seek in file [%1$s].', $this));

      $this->m_position=@ftell($this->m_pointer);

      return $this;
    }

    /**
     * @return Io_File
     *
     * @throws Io_Exception
     */
    public function seekToBegin()
    {
      if(false===@rewind($this->m_pointer))
        throw new Io_Exception('io/file', sprintf('Unable to seek in file [%1$s].', $this));

      $this->m_position=0;

      return $this;
    }

    /**
     * @return Io_File
     *
     * @throws Io_Exception
     */
    public function seekToEnd()
    {
      if(-1===@fseek($this->m_pointer, 0, SEEK_END))
        throw new Io_Exception('io/file', sprintf('Unable to seek in file [%1$s].', $this));

      $this->m_position=@ftell($this->m_pointer);

      return $this;
    }

    /**
     * @param int $bytes_
     *
     * @return Io_File
     *
     * @throws Io_Exception
     */
    public function skip($bytes_=1)
    {
      if(-1===@fseek($this->m_pointer, $bytes_, SEEK_CUR))
        throw new Io_Exception('io/file', sprintf('Unable to seek in file [%1$s].', $this));

      $this->m_position=@ftell($this->m_pointer);

      return $this;
    }

    /**
     * @return int
     *
     * @throws Io_Exception
     */
    public function position()
    {
      return $this->m_position;
    }

    public function isBegin()
    {
      return 0===$this->m_position;
    }


    public function isEnd()
    {
      return @feof($this->m_pointer);
    }

    /**
     * @return int
     */
    public function length()
    {
      return $this->m_length;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * (non-PHPdoc)
     * @see Cloneable::__clone()
     */
    public function __clone()
    {
      return self($this->m_path);
    }

    /**
     * (non-PHPdoc)
     * @see Object::hashCode()
     */
    public function hashCode()
    {
      return String::hash($this->m_path);
    }

    /**
     * (non-PHPdoc)
     * @see Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return String::equal($this->m_path, $object_->m_path);

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Object::__toString()
     */
    public function __toString()
    {
      return (string)$this->m_path;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected $m_path;

    private $m_open=false;
    private $m_name;
    private $m_directory;
    private $m_extension;
    private $m_accessFlags;
    private $m_accessMask;
    private $m_pointer;
    /**
     * @var Io_MimeType
     */
    private $m_mimeType;
    private $m_writable;
    private $m_position;
    private $m_length;
    //-----


    private function accessFlagsForMask($mask_)
    {
      if(null!==$this->m_accessFlags)
        return $this->m_accessFlags;

      $flag=Io::systemIsWindows()?'b':'';

      if(0<($mask_&self::TRUNCATE))
        return $this->m_accessFlags="{$flag}w+";
      if(0<($mask_&self::APPEND))
        return $this->m_accessFlags="{$flag}a+";
      if(0<($mask_&self::CREATE))
        return $this->m_accessFlags="{$flag}c+";
      if(0<($mask_&self::WRITE))
        return $this->m_accessFlags="{$flag}r+";

      return $this->m_accessFlags="{$flag}r";
    }


    // DESTRUCTION
    public function __destruct()
    {
      if(null!==$this->m_pointer)
        @fclose($this->m_pointer);
    }
    //--------------------------------------------------------------------------
  }
?>
