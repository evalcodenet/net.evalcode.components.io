<?php


namespace Components;


  /**
   * Io_File
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   */
  class Io_File implements Object, Cloneable, Value_String
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
     * TODO Implement io/file/lock.
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
      $this->m_pathAsString=(string)$path_;
      $this->m_accessMask=$accessModeMask_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $path_
     *
     * @return \Components\Io_File
     */
    public static function forPath($path_, $accessModeMask_=self::READ)
    {
      return new static($path_, $accessModeMask_);
    }

    /**
     * @param string $value_
     *
     * @return \Components\Io_File
     */
    public static function valueOf($value_)
    {
      return new static($value_, self::READ);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return string
     */
    public function getName()
    {
      if(null===$this->m_name)
        $this->m_name=basename($this->m_pathAsString);

      return $this->m_name;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
      if(null===$this->m_extension)
        return mb_strtolower(mb_substr($this->m_pathAsString, mb_strrpos($this->m_pathAsString, '.')+1));

      return $this->m_extension;
    }

    /**
     * @return \Components\Io_Path
     */
    public function getPath()
    {
      if(null===$this->m_path)
        $this->m_path=new Io_Path($this->m_pathAsString);

      return $this->m_path;
    }

    /**
     * @return string
     */
    public function getPathAsString()
    {
      return $this->m_pathAsString;
    }

    /**
     * @return \Components\Io_Path
     */
    public function getDirectory()
    {
      if(null===$this->m_directory)
        $this->m_directory=new Io_Path($this->getDirectoryAsString());

      return $this->m_directory;
    }

    /**
     * @return string
     */
    public function getDirectoryAsString()
    {
      if(null===$this->m_directoryAsString)
        $this->m_directoryAsString=dirname($this->m_pathAsString);

      return $this->m_directoryAsString;
    }

    /**
     * @throws \Components\Io_Exception
     *
     * @return \Components\Io_Filesize
     */
    public function getSize()
    {
      // FIXME Get size without clearstatcache?
      clearstatcache(null, $this->m_pathAsString);
      if(false===($filesize=filesize($this->m_pathAsString)))
        return new Io_Filesize(0);

      return new Io_Filesize($filesize);
    }

    /**
     * @return \Components\Io_MimeType
     */
    public function getMimeType()
    {
      if(null===$this->m_mimeType)
      {
        if($this->exists())
          return $this->m_mimeType=Io_MimeType::forFilePath($this->m_pathAsString);

        return $this->m_mimeType=Io_MimeType::forFileExtension($this->getExtension());
      }

      return $this->m_mimeType;
    }

    /**
     * @return \Components\Io_Charset
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
     * @return \Components\Io_Image
     */
    public function asImage()
    {
      $image=new Io_Image($this->m_pathAsString, $this->m_accessMask);
      $image->m_name=$this->m_name;
      $image->m_directoryAsString=$this->m_directoryAsString;
      $image->m_extension=$this->m_extension;
      $image->m_mimeType=$this->m_mimeType;

      return $image;
    }

    /**
     * Returns a file for an absolute path or for a path that is relative to
     * the one of this file.
     *
     * @param string $path_
     *
     * @return \Components\Io_File
     */
    public function getRelated($path_)
    {
      if(String::startsWith($path_, '/'))
        return new self($path_);

      return new self($this->getDirectoryAsString()."/$path_");
    }

    /**
     * Returns path of given file relative to path of this file.
     *
     * @param \Components\Io_File $file_
     *
     * @return \Components\Io_Path
     */
    public function getRelativePath(Io_File $file_)
    {
      if($this->getDirectory()->isParentOfFile($file_))
        return Io::path(String::replace($file_->m_pathAsString, $this->getDirectoryAsString().'/', ''));

      $level=0;
      $segments=explode('/', $this->m_pathAsString);
      $filepath=$file_->m_pathAsString;
      while(array_pop($segments))
      {
        if(0===mb_strpos($filepath, implode('/', $segments)))
        {
          $path=implode('/', $segments);
          $subPath=String::replace($filepath, $path, '');

          return str_repeat('/..', $level).$subPath;
        }

        $level++;
      }

      return $file_->getPath();
    }

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
      if(false===$this->isWritable())
      {
        if($this->m_accessMask&self::CREATE && false===is_file($this->m_pathAsString))
        {
          $directory=$this->getDirectory();
          if(false===$directory->exists())
            $directory->create();
        }
        else
        {
          throw new Io_Exception('components/io/file', sprintf('File is not writable [%s].', $this));
        }
      }

      if($this->m_accessMask&self::APPEND)
        $mode=FILE_APPEND;
      else if($this->m_accessMask&self::LOCK)
        $mode=LOCK_EX;

      if(false===($written=file_put_contents($this->m_pathAsString, $string_, $mode)))
        throw new Io_Exception('components/io/file', sprintf('Unable to write to file [%s].', $this));

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
      return md5_file($this->m_pathAsString);
    }

    public function getHashSHA1()
    {
      return sha1_file($this->m_pathAsString);
    }

    public function isReadable()
    {
      return is_readable($this->m_pathAsString);
    }

    public function isWritable()
    {
      if(false===is_writable($this->m_pathAsString))
        return false;

      if(0<($this->m_accessMask&(self::APPEND|self::TRUNCATE|self::CREATE)))
        return true;

      if(0<($this->m_accessMask&(self::WRITE)))
        return is_file($this->m_pathAsString);

      return false;
    }

    /**
     * @return boolean
     */
    public function exists()
    {
      return is_file($this->m_pathAsString);
    }

    /**
     * @return \Components\Io_File
     */
    public function create()
    {
      if(false===touch($this->m_pathAsString))
      {
        $directory=$this->getDirectory();
        if(false===$directory->exists())
          $directory->create();
      }

      if(false===touch($this->m_pathAsString))
        throw new Io_Exception('components/io/file', sprintf('Unable to create file in given location [%s].', $this));

      return $this;
    }

    public function delete()
    {
      if(false===unlink($this->m_pathAsString))
        throw new Io_Exception('components/io/file', sprintf('Unable to delete file [%s].', $this));

      return $this;
    }

    /**
     * @param \Components\Io_File $destination_
     *
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function copy(Io_File $destination_, $autoCreateTargetDirectory_=false, $umaskTargetDirectory_=0775)
    {
      if(true===$autoCreateTargetDirectory_)
      {
        $targetDirectory_=$destination_->getDirectory();
        if(false===$targetDirectory_->exists())
          $targetDirectory_->create($umaskTargetDirectory_);
      }

      if(false===copy($this->m_pathAsString, $destination_->m_pathAsString))
        throw new Io_Exception('components/io/file', sprintf('Unable to copy file to given destination [source: %s, destination: %s].', $this, $destination_));

      return $destination_;
    }

    public function copyInto(Io_Path $destination_, $autoCreateTargetDirectory_=false, $umaskTargetDirectory_=0775)
    {
      return $this->copy($destination_->getFile($this->m_name), $autoCreateTargetDirectory_, $umaskTargetDirectory_);
    }

    /**
     * @param \Components\Io_File $target_
     *
     * @return \Components\Io_File
     */
    public function move(Io_File $destination_, $autoCreateTargetDirectory_=false, $umaskTargetDirectory_=0775)
    {
      if(true===$autoCreateTargetDirectory_)
      {
        $targetDirectory=$destination_->getDirectory();
        if(false===$targetDirectory->exists())
          $targetDirectory->create($umaskTargetDirectory_);
      }

      if(false===rename($this->m_pathAsString, $destination_->m_pathAsString))
      {
        if(is_file($destination_->m_pathAsString))
          unlink($this->m_pathAsString);
        else
          throw new Io_Exception('components/io/file', sprintf('Unable to move file to given destination [source: %s, destination: %s].', $this, $destination_));
      }

      return $destination_;
    }

    /**
     * @param \Components\Io_Path $target_
     * @param boolean $autoCreateTargetDirectory_
     * @param integer $umaskTargetDirectory_
     *
     * @return \Components\Io_File
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
     * @param integer $mask_
     *
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function open()
    {
      if(false===($this->m_pointer=fopen($this->m_pathAsString, $this->accessFlagsForMask($this->m_accessMask))))
        throw new Io_Exception('components/io/file', sprintf('Unable to open file [%s].', $this));

      if(null===$this->m_length)
      {
        if($this->m_accessMask^self::TRUNCATE)
          $this->m_length=filesize($this->m_pathAsString);
        else
          $this->m_length=0;
      }

      if($this->m_accessMask&self::APPEND)
        $this->m_position=fseek($this->m_pointer, 0, SEEK_END);
      else if(null===$this->m_position)
        $this->m_position=0;

      $this->m_writable=$this->isWritable();
      $this->m_open=true;

      return $this;
    }

    /**
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function close()
    {
      fclose($this->m_pointer);

      $this->m_open=false;

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
      if(0>$bytes_)
        fseek($this->m_pointer, $this->m_position-($bytes_=-$bytes_));

      if(false===($read=fread($this->m_pointer, $bytes_)))
        throw new Io_Exception('components/io/file', sprintf('Unable to read from file [%s].', $this));

      $this->m_position=ftell($this->m_pointer);

      return $read;
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
        throw new Io_Exception('components/io/file', sprintf('File is not writable [%s].', $this));

      if(1>($length=strlen($string_)))
        return 0;

      if(0===($written=fwrite($this->m_pointer, $string_, $length)))
        throw new Io_Exception('components/io/file', sprintf('Unable to write to file [%s].', $this));

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
        throw new Io_Exception('components/io/file', sprintf('File is not writable [%s].', $this));

      if(1>($length=strlen($string_)))
        return 0;

      fseek($this->m_pointer, $this->m_length, SEEK_SET);
      if(0===($written=fwrite($this->m_pointer, $string_, $length)))
        throw new Io_Exception('components/io/file', sprintf('Unable to write to file [%s].', $this));

      $this->m_length+=$written;
      $this->m_position=$this->m_length;

      return $written;
    }

    public function appendLine($string_, $separatorLine_=Io::LINE_SEPARATOR_DEFAULT)
    {
      return $this->append($string_.$separatorLine_);
    }

    /**
     * @param integer $length_
     *
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function truncate($length_=0)
    {
      if(false===ftruncate($this->m_pointer, $length_))
        throw new Io_Exception('components/io/file', sprintf('Failed to truncate file [%s].', $this));

      $this->m_length=$length_;
      if($this->m_position>$length_)
        $this->m_position=$length_;

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
      if(-1===fseek($this->m_pointer, $position_, SEEK_SET))
        throw new Io_Exception('components/io/file', sprintf('Unable to seek in file [%s].', $this));

      $this->m_position=ftell($this->m_pointer);

      return $this;
    }

    /**
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function seekToBegin()
    {
      if(false===rewind($this->m_pointer))
        throw new Io_Exception('components/io/file', sprintf('Unable to seek in file [%s].', $this));

      $this->m_position=0;

      return $this;
    }

    /**
     * @return \Components\Io_File
     *
     * @throws \Components\Io_Exception
     */
    public function seekToEnd()
    {
      if(-1===fseek($this->m_pointer, 0, SEEK_END))
        throw new Io_Exception('components/io/file', sprintf('Unable to seek in file [%s].', $this));

      $this->m_position=ftell($this->m_pointer);

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
      if(-1===fseek($this->m_pointer, $bytes_, SEEK_CUR))
        throw new Io_Exception('components/io/file', sprintf('Unable to seek in file [%s].', $this));

      $this->m_position=ftell($this->m_pointer);

      return $this;
    }

    /**
     * @return integer
     *
     * @throws \Components\Io_Exception
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
      return feof($this->m_pointer);
    }

    /**
     * @return integer
     */
    public function length()
    {
      return $this->m_length;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * (non-PHPdoc)
     * @see Components\Cloneable::__clone()
     */
    public function __clone()
    {
      return self($this->m_pathAsString);
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::hashCode()
     */
    public function hashCode()
    {
      return string_hash($this->m_pathAsString);
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return String::equal($this->m_pathAsString, $object_->m_pathAsString);

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::__toString()
     */
    public function __toString()
    {
      return $this->m_pathAsString;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Value_String::value()
     */
    public function value()
    {
      return $this->m_pathAsString;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected $m_open=false;
    protected $m_pathAsString;
    protected $m_accessMask;
    protected $m_position;
    protected $m_length;

    private $m_name;
    private $m_extension;
    /**
     * @var Components\Io_Path
     */
    private $m_path;
    /**
     * @var Components\Io_Path
     */
    private $m_directory;
    private $m_directoryAsString;
    private $m_pointer;
    private $m_accessFlags;
    private $m_writable;
    /**
     * @var Components\Io_MimeType
     */
    private $m_mimeType;
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
