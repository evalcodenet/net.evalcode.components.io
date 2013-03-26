<?php


  /**
   * Io_Path
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   */
  class Io_Path implements Object, Cloneable, IteratorAggregate
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
    public static function resolve($path_)
    {
      if(false===($path_=@realpath($path_)))
        throw new Io_Exception('io/path', sprintf('Unable to resolve given path [%1$s].', $path_));

      return new self($path_);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
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
    public function asFile()
    {
      return new Io_File($this->m_path);
    }

    /**
     * @param string $name_
     *
     * @return Io_File
     */
    public function getFile($name_)
    {
      return new Io_File("{$this->m_path}/$name_");
    }

    /**
     * @return boolean
     */
    public function isImage()
    {
      if(@is_file($this->m_path))
        return Io_MimeType::forFilePath($this->m_path)->isImage();

      return Io_MimeType::forFileName($this->m_path);
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
     * @param int $umask_
     *
     * @return Io_Path
     *
     * @throws Io_Exception
     */
    public function create($umask_=0775)
    {
      if(false===Io::createDirectory($this->m_path, $umask_))
        throw new Io_Exception('io/path', sprintf('Failed to create directory [%1$s].', $this));

      return $this;
    }

    /**
     * @param boolean $recursive_
     *
     * @return Io_Path
     */
    public function delete($recursive_=false)
    {
      return Io::deletePath($this->m_path, $recursive_);
    }

    public function clear()
    {
      Io::clearPath($this->m_path);

      return $this;
    }

    public function copy(Io_Path $target_)
    {
      Io::copyDirectory($this->m_path, $target_->m_path);

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

    }

    public function isParentOf(Io_Path $path_)
    {

    }

    public function isChildOf(Io_Path $path_)
    {

    }

    public function isSiblingOf(Io_Path $path_)
    {

    }

    public function containsFile(Io_File $file_)
    {

    }

    public function hasChildren()
    {

    }

    public function hasFiles()
    {

    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function getIterator()
    {
      return new Io_Path_Iterator($this->m_path);
    }

    /**
     * (non-PHPdoc)
     * @see Cloneable::__clone()
     */
    public function __clone()
    {
      return new self($this->m_path);
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
      return $this->m_path;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_name;
    private $m_path;
    //--------------------------------------------------------------------------
  }
?>