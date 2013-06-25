<?php


namespace Components;


  /**
   * Io_Image
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   *
   * TODO Decouple from io/file - use composition instead of inheritance and
   * implement e.g. io/file/virtual for memory/cache-mapped images/image processing..
   */
  class Io_Image extends Io_File
  {
    // STATIC ACCESSORS
    /**
     * @return string
     */
    public static function defaultProcessingLibrary()
    {
      /**
       * TODO Resolve based on available/loaded extensions:
       * gd, imagick, imagemagick and/or external services/cli processors ...
       */
      return 'gd';
    }

    /**
     * @return string
     */
    public static function defaultProcessingEngine()
    {
      return self::$m_engineImpl[self::defaultProcessingLibrary()];
    }

    /**
     * @param string $path_
     *
     * @return \Components\Io_Image
     */
    public static function forPath($path_, $accessModeMask_=self::READ)
    {
      $engineImpl=self::defaultProcessingEngine();

      $instance=new static((string)$path_);
      $instance->m_engine=$engineImpl::forPath($instance->m_pathAsString);

      return $instance;
    }

    /**
     * @param string $path_
     * @param \Components\Point $dimensions_
     *
     * @return \Components\Io_Image
     */
    public static function createNew($path_, Point $dimensions_)
    {
      $engineImpl=self::defaultProcessingEngine();

      $instance=new static((string)$path_);
      $instance->m_engine=$engineImpl::createImage($instance->m_pathAsString, $dimensions_);

      return $instance;
    }

    /**
     * @param string $path_
     * @param \Components\Point $dimensions_
     *
     * @return \Components\Io_Image
     */
    public static function createForBase64($path_, $base64_)
    {
      $engineImpl=self::defaultProcessingEngine();

      $instance=new static((string)$path_);
      $instance->m_engine=$engineImpl::forBase64($base64_);

      return $instance;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return \Components\Point
     */
    public function getDimensions()
    {
      return $this->engine()->dimensions();
    }

    /**
     * @param \Components\Point $toSize_
     *
     * @return \Components\Io_Image
     */
    public function crop(Point $toSize_)
    {
      $this->engine()->crop($toSize_);

      return $this;
    }

    /**
     * @param \Components\Point $toSize_
     *
     * @return \Components\Io_Image
     */
    public function scale(Point $toSize_)
    {
      $this->engine()->scale($toSize_);

      return $this;
    }

    public function save()
    {
      $this->engine()->save($this->m_pathAsString, $this->getMimetype());

      return $this;
    }

    public function saveAs(Io_Image $image_)
    {
      $this->engine()->save($image_->m_pathAsString, $image_->getMimetype());

      return $this;
    }

    public function getBase64()
    {
      return base64_encode($this->getContent());
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    // TODO Override __clone(), copy(), move() etc. correctly
    /**
     * (non-PHPdoc)
     * @see Components\Io_File::isImage()
     */
    public function isImage()
    {
      return true;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Io_File::asImage()
     */
    public function asImage()
    {
      return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Cloneable::__clone()
     */
    public function __clone()
    {
      $instance=new self($this->m_pathAsString);
      $instance->m_engine=clone $this->engine();

      return $instance;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::hashCode()
     */
    public function hashCode()
    {
      return String::hash($this->m_pathAsString);
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
      return (string)$this->m_pathAsString;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var array|string
     */
    private static $m_engineImpl=array(
      'gd'=>'Components\\Io_Image_Engine_Gd'
    );

    /**
     * @var \Components\Io_Image_Engine
     */
    protected $m_engine;
    //-----


    /**
     * @return \Components\Io_Image_Engine
     */
    protected function engine()
    {
      if(null===$this->m_engine)
      {
        $engineImpl=static::defaultProcessingEngine();

        $this->m_engine=$engineImpl::forPath($this->m_pathAsString);
      }

      return $this->m_engine;
    }
    //--------------------------------------------------------------------------
  }
?>
