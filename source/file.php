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
  // TODO Implement io/file/iterator/line
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


    // PROPERTIES
    public $linefeed=Io::LINE_SEPARATOR_DEFAULT;
    public $lineBufferSize=512;
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
     * @param string $value_
     *
     * @return \Components\Io_File
     */
    public static function valueOf($value_)
    {
      return new static($value_, self::READ);
    }

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
     * @param \Components\Io_Mimetype $mimeType_
     * @param string $path_
     * @param integer $accessModeMask_
     *
     * @return \Components\Io_File
     */
    public static function forMimetype($path_, $accessModeMask_=self::READ, Io_Mimetype $mimeType_=null)
    {
      if(null===$mimeType_)
        $mimeType_=Io_Mimetype::forFileName($path_);

      if(null===$mimeType_)
        return new static($path_, $accessModeMask_);

      if(isset(self::$m_implForMimetype[$mimeType_->name()]))
      {
        $type=self::$m_implForMimetype[$mimeType_->name()];

        return new $type($path_, $accessModeMask_);
      }

      return new static($path_, $accessModeMask_);
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
     * @return \Components\Io_Mimetype
     */
    public function getMimetype()
    {
      if(null===$this->m_mimeType)
      {
        if($this->exists())
          return $this->m_mimeType=Io_Mimetype::forFilePath($this->m_pathAsString);

        return $this->m_mimeType=Io_Mimetype::forFileExtension($this->getExtension());
      }

      return $this->m_mimeType;
    }

    /**
     * @return \Components\Io_Charset
     */
    public function getCharset()
    {
      if($mimeType=$this->getMimetype())
        return $mimeType->charset();

      return Io_Charset::defaultCharset();
    }

    /**
     * @return boolean
     */
    public function isImage()
    {
      return $this->getMimetype()->isImage();
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
      $this->getDirectory()->getRelativePath($file_->getPath());
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
          throw new Io_Exception('io/file', sprintf('File is not writable [%s].', $this));
        }
      }

      if($this->m_accessMask&self::APPEND)
        $mode=FILE_APPEND;
      else
        $mode=null;

      if(false===($written=file_put_contents($this->m_pathAsString, $string_, $mode)))
        throw new Io_Exception('io/file', sprintf('Failed to write to file [%s].', $this));

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
      if($this->m_open)
        return true;

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
        throw new Io_Exception('io/file', sprintf('Failed to create file in given location [%s].', $this));

      return $this;
    }

    public function delete()
    {
      if(false===unlink($this->m_pathAsString))
        throw new Io_Exception('io/file', sprintf('Failed to delete file [%s].', $this));

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
        throw new Io_Exception('io/file', sprintf('Failed to copy file to given destination [source: %s, destination: %s].', $this, $destination_));

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
          throw new Io_Exception('io/file', sprintf('Failed to move file to given destination [source: %s, destination: %s].', $this, $destination_));
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
        throw new Io_Exception('io/file', sprintf('Failed to open file [%s].', $this));

      if(null===$this->m_length)
      {
        if($this->m_accessMask^self::TRUNCATE)
          $this->m_length=filesize($this->m_pathAsString);
        else
          $this->m_length=0;
      }

      if($this->m_accessMask&self::APPEND)
      {
        fseek($this->m_pointer, 0, SEEK_END);
        $this->m_position=$this->m_length;
      }
      else if(null===$this->m_position)
      {
        $this->m_position=0;
      }

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
        fseek($this->m_pointer, $bytes_, SEEK_CUR);

      if(false===($read=fread($this->m_pointer, $bytes_)))
        throw new Io_Exception('io/file', sprintf('Failed to read from file [%s].', $this));

      $this->m_position=ftell($this->m_pointer);

      return $read;
    }

    /**
     * @param string $linefeed_
     *
     * @return string
     */
    public function readLine()
    {
      $line=fgets($this->m_pointer);
      $this->m_position=ftell($this->m_pointer);

      return $line;
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

      if(0===($written=fwrite($this->m_pointer, $string_, $length)))
        throw new Io_Exception('io/file', sprintf('Failed to write to file [%s].', $this));

      if($this->m_length<($this->m_position+=$written))
        $this->m_length=$this->m_position;

      return $written;
    }

    public function writeLine($string_)
    {
      return $this->write($string_.$this->linefeed);
    }

    public function append($string_)
    {
      if(false===$this->m_writable)
        throw new Io_Exception('io/file', sprintf('File is not writable [%s].', $this));

      if(1>($length=mb_strlen($string_)))
        return 0;

      fseek($this->m_pointer, $this->m_length, SEEK_SET);
      if(0===($written=fwrite($this->m_pointer, $string_, $length)))
        throw new Io_Exception('io/file', sprintf('Failed to write to file [%s].', $this));

      $this->m_length+=$written;
      $this->m_position=$this->m_length;

      return $written;
    }

    public function appendLine($string_)
    {
      return $this->append($string_.$this->linefeed);
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
      if(null===$length_)
        $length_=$this->m_position;

      if(false===ftruncate($this->m_pointer, $length_))
        throw new Io_Exception('io/file', sprintf('Failed to truncate file [%s].', $this));

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
    public function seek($position_)
    {
      if(-1===fseek($this->m_pointer, $position_, SEEK_CUR))
        throw new Io_Exception('io/file', sprintf('Failed to seek in file [%s].', $this));

      $this->m_position=$position_;

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
        throw new Io_Exception('io/file', sprintf('Failed to seek in file [%s].', $this));

      $this->m_position=$position_;

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
        throw new Io_Exception('io/file', sprintf('Failed to seek in file [%s].', $this));

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
        throw new Io_Exception('io/file', sprintf('Failed to seek in file [%s].', $this));

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
        throw new Io_Exception('io/file', sprintf('Failed to seek in file [%s].', $this));

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

    /**
     * @return boolean
     */
    public function isBegin()
    {
      return 0===$this->m_position;
    }

    /**
     * @return boolean
     */
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
    /**     * @see Components\Cloneable::__clone() Components\Cloneable::__clone()
     */
    public function __clone()
    {
      return self($this->m_pathAsString);
    }

    /**     * @see Components\Object::hashCode() Components\Object::hashCode()
     */
    public function hashCode()
    {
      return string_hash($this->m_pathAsString);
    }

    /**     * @see Components\Object::equals() Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return String::equal($this->m_pathAsString, $object_->m_pathAsString);

      return false;
    }

    /**     * @see Components\Object::__toString() Components\Object::__toString()
     */
    public function __toString()
    {
      return $this->m_pathAsString;
    }

    /**     * @see Components\Value_String::value() Components\Value_String::value()
     */
    public function value()
    {
      return $this->m_pathAsString;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_implForMimetype=array(
      Io_Mimetype::APPLICATION_ZIP=>'Components\\Io_Archive_Zip',
      Io_Mimetype::TEXT_CSV=>'Components\\Io_File_Csv_Random',
      Io_Mimetype::IMAGE_GIF=>'Components\\Io_Image',
      Io_Mimetype::IMAGE_JPEG=>'Components\\Io_Image',
      Io_Mimetype::IMAGE_JPG=>'Components\\Io_Image',
      Io_Mimetype::IMAGE_PNG=>'Components\\Io_Image'
    );

    protected $m_open=false;
    protected $m_pathAsString;
    protected $m_accessMask;
    protected $m_position;
    protected $m_length;
    /**
     * @var resource
     */
    protected $m_pointer;

    private $m_name;
    private $m_extension;
    /**
     * @var \Components\Io_Mimetype
     */
    private $m_mimeType;
    /**
     * @var \Components\Io_Path
     */
    private $m_path;
    /**
     * @var \Components\Io_Path
     */
    private $m_directory;
    private $m_directoryAsString;
    private $m_accessFlags;
    private $m_writable;
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
      if($this->m_open)
        @fclose($this->m_pointer);
    }
    //--------------------------------------------------------------------------
  }
?>
