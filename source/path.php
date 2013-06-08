<?php


namespace Components;


  /**
   * Io_Path
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   */
  class Io_Path implements Object, Cloneable, Value_String, Iterable
  {
    // CONSTRUCTION
    public function __construct($path_)
    {
      $this->m_path=(string)$path_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $path_
     *
     * @return Io_Path
     */
    public static function get($path0_/*, $path1_, $path2_..*/)
    {
      $args=func_get_args();

      $prepend='';
      if(Io::DIRECTORY_SEPARATOR===$args[0])
        $prepend=array_shift($args);

      return new self($prepend.implode(Io::DIRECTORY_SEPARATOR, $args));
    }

    /**
     * @param string $path_
     *
     * @return \Components\Io_Path
     */
    public static function valueOf($path_)
    {
      return new self($path_);
    }

    /**
     * @param string $path_
     *
     * @return Io_Path
     */
    public static function resolve($path_)
    {
      if(false===($path_=@realpath($path_)))
        throw new Io_Exception('io/path', sprintf('Unable to resolve given path [%1$s].', $path_));

      return new self($path_);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function getName()
    {
      if(null===$this->m_name)
        $this->m_name=@basename($this->m_path);

      return $this->m_name;
    }

    /**
     * @return string
     */
    public function getPath()
    {
      return $this->m_path;
    }

    /**
     * @return Io_Path
     */
    public function getParent()
    {
      return new self(dirname($this->m_path));
    }

    /**
     * @return boolean
     */
    public function isAbsolutePath()
    {
      if(null===$this->m_isAbsolutePath)
      {
        if(null===$this->m_path || Io::DIRECTORY_SEPARATOR!==substr($this->m_path, 0, 1) || false!==strpos($this->m_path, '..'))
          $this->m_isAbsolutePath=false;
        else
          $this->m_isAbsolutePath=true;
      }

      return $this->m_isAbsolutePath;
    }

    /**
     * @return Io_Path
     *
     * @throws Io_Exception If fails to resolve path.
     */
    public function toAbsolutePath()
    {
      if($this->isAbsolutePath())
        return $this;

      if(false===($path=@realpath($this->m_path)))
        throw new Io_Exception('io/path', sprintf('Unable to resolve path [%s].', $this->m_path));

      return new self($path);
    }

    /**
     * @return Net_Uri
     */
    public function toUri()
    {
      return new Net_Uri($this->m_path);
    }

    /**
     * @return boolean
     */
    public function isDot()
    {
      return '.'===($basename=@basename($this->m_path)) || '..'===$basename;
    }

    /**
     * @return boolean
     */
    public function isDirectory()
    {
      return @is_dir($this->m_path);
    }

    /**
     * @return boolean
     */
    public function isFile()
    {
      return @is_file($this->m_path);
    }

    /**
     * @return Io_File
     */
    public function asFile($accessModeMask_=Io_File::READ)
    {
      if(@is_file($this->m_path))
        return Io_File::forMimetype($this->m_path, $accessModeMask_);

      return new Io_File($this->m_path);
    }

    /**
     * @param string $name_
     *
     * @return Io_File
     */
    public function getFile($name_, $accessModeMask_=Io_File::READ)
    {
      return Io_File::forMimetype("{$this->m_path}/$name_", $accessModeMask_);
    }

    /**
     * @return boolean
     */
    public function isImage()
    {
      if(@is_file($this->m_path))
        return Io_Mimetype::forFilePath($this->m_path)->isImage();

      return Io_Mimetype::forFileName($this->m_path);
    }

    /**
     * @return Io_Image
     */
    public function asImage()
    {
      return Io_Image::forPath($this->m_path);
    }

    /**
     * @param string $name_
     *
     * @return Io_Image
     */
    public function getImage($name_)
    {
      return Io_Image::forPath("{$this->m_path}/$name_");
    }

    /**
     * @return boolean
     */
    public function isReadable()
    {
      return @is_readable($this->m_path);
    }

    /**
     * @return boolean
     */
    public function isWritable()
    {
      return @is_writable($this->m_path);
    }

    /**
     * @return boolean
     */
    public function exists()
    {
      return @file_exists($this->m_path);
    }

    /**
     * @param boolean $recursive_
     * @param integer $umask_
     *
     * @return Io_Path
     *
     * @throws Io_Exception
     */
    public function create($umask_=0775)
    {
      return Io::directoryCreate($this->m_path, $umask_);
    }

    /**
     * @param boolean $recursive_
     *
     * @return Io_Path
     */
    public function delete($recursive_=false)
    {
      return Io::directoryDelete($this->m_path, $recursive_);
    }

    public function clear()
    {
      Io::directoryClear($this->m_path);

      return $this;
    }

    public function copy(Io_Path $target_)
    {
      Io::directoryCopy($this->m_path, $target_->m_path);

      return $target_;
    }

    public function copyInto(Io_Path $target_)
    {

    }

    public function move(Io_File $target_)
    {

    }

    public function moveInto(Io_Path $target_)
    {

    }

    public function isRoot()
    {
      // FIXME (CSH) Implement for windows ...
      if(Io::systemIsWindows())
        return false;

      return '/'===$this->m_path;
    }

    public function isParentOf(Io_Path $path_)
    {
      return 0===mb_strpos($path_->m_path, $this->m_path) && strlen($this->m_path)<strlen($path_->m_path);
    }

    public function isParentOfFile(Io_File $file_)
    {
      $filePath=(string)$file_;

      return 0===mb_strpos($filePath, $this->m_path) && strlen($this->m_path)<strlen($filePath);
    }

    public function isChildOf(Io_Path $path_)
    {
      return 0===mb_strpos($this->m_path, $path_->m_path) && strlen($this->m_path)>strlen($path_->m_path);
    }

    public function isSiblingOf(Io_Path $path_)
    {
      return $this->getParent()->m_path===$path_->getParent()->m_path;
    }

    public function containsFile(Io_File $file_)
    {
      return $this->m_path===$file_->getDirectory()->m_path;
    }

    public function hasChildren()
    {

    }

    public function hasFiles()
    {

    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    public function __get($name_)
    {
      return static::get($this->m_path, $name_);
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Iterable::getIterator()
     */
    public function getIterator()
    {
      return new Io_Path_Iterator($this->m_path);
    }

    /**
     * (non-PHPdoc)
     * @see Components\Cloneable::__clone()
     */
    public function __clone()
    {
      return new self($this->m_path);
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::hashCode()
     */
    public function hashCode()
    {
      return String::hash($this->m_path);
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return String::equal($this->m_path, $object_->m_path);

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::__toString()
     */
    public function __toString()
    {
      return $this->m_path;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Value_String::value()
     */
    public function value()
    {
      return $this->m_path;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_isAbsolutePath=false;
    private $m_name;
    private $m_path;
    //--------------------------------------------------------------------------
  }
?>
