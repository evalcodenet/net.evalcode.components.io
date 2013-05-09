<?php


namespace Components;


  /**
   * Io_Image
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
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
     * @return Components\Io_Image
     */
    public static function forPath($path_, $accessModeMask_=self::READ)
    {
      $engineImpl=self::defaultProcessingEngine();

      $instance=new self((string)$path_);
      $instance->m_engine=$engineImpl::forPath($instance->m_pathAsString);

      return $instance;
    }

    /**
     * @param string $path_
     * @param Components\Point $dimensions_
     *
     * @return Components\Io_Image
     */
    public static function createNew($path_, Point $dimensions_)
    {
      $engineImpl=self::defaultProcessingEngine();

      $instance=new self((string)$path_);
      $instance->m_engine=$engineImpl::createImage($instance->m_pathAsString, $dimensions_);

      return $instance;
    }

    /**
     * @param string $path_
     * @param Components\Point $dimensions_
     *
     * @return Components\Io_Image
     */
    public static function createForBase64($path_, $base64_)
    {
      $engineImpl=self::defaultProcessingEngine();

      $instance=new self((string)$path_);
      $instance->m_engine=$engineImpl::forBase64($base64_);

      return $instance;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return Components\Point
     */
    public function getDimensions()
    {
      return $this->m_engine->dimensions();
    }

    /**
     * @param Components\Point $toSize_
     *
     * @return Components\Io_Image
     */
    public function crop(Point $toSize_)
    {
      $this->m_engine->crop($toSize_);

      return $this;
    }

    /**
     * @param Components\Point $toSize_
     *
     * @return Components\Io_Image
     */
    public function scale(Point $toSize_)
    {
      $this->m_engine->scale($toSize_);

      return $this;
    }

    public function save()
    {
      $this->m_engine->save($this->m_pathAsString);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    // TODO Override __clone(), copy(), move() etc. correctly
    /**
     * (non-PHPdoc)
     * @see Components.Io_File::isImage()
     */
    public function isImage()
    {
      return true;
    }

    /**
     * (non-PHPdoc)
     * @see Components.Io_File::asImage()
     */
    public function asImage()
    {
      return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Components.Cloneable::__clone()
     */
    public function __clone()
    {
      $instance=new self($this->m_pathAsString);
      $instance->m_engine=clone $this->m_engine;

      return $instance;
    }

    /**
     * (non-PHPdoc)
     * @see Components.Object::hashCode()
     */
    public function hashCode()
    {
      return String::hash($this->m_pathAsString);
    }

    /**
     * (non-PHPdoc)
     * @see Components.Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return String::equal($this->m_pathAsString, $object_->m_pathAsString);

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Components.Object::__toString()
     */
    public function __toString()
    {
      return (string)$this->m_pathAsString;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_engineImpl=array(
      'gd'=>'Components\\Io_Image_Engine_Gd'
    );

    /**
     * @var Components\Io_Image_Engine
     */
    private $m_engine;
    //--------------------------------------------------------------------------
  }
?>
