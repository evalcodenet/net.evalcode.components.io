<?php


namespace Components;


  /**
   * Io_Image_Engine_Gd
   *
   * @api
   * @package net.evalcode.components.io
   * @subpackage image.engine
   *
   * @author evalcode.net
   */
  class Io_Image_Engine_Gd implements Io_Image_Engine
  {
    // STATIC ACCESSORS
    /**
     * @param \Components\Point $dimensions_
     *
     * @return \Components\Io_Image_Engine_Gd
     */
    public static function forBase64($base64_)
    {
      $instance=new static();
      $instance->m_resource=imagecreatefromstring(str\decodeBase64Url($base64_));

      return $instance;
    }

    /**
     * @param \Components\Point $dimensions_
     *
     * @return \Components\Io_Image_Engine_Gd
     */
    public static function forPath($path_)
    {
      if(false===is_file($path_))
        throw new Io_Exception('io/image/engine/gd', sprintf('Image for given path does not exist [%s].', $path_));

      $instance=new static();
      $instance->m_resource=imagecreatefromstring(file_get_contents($path_));

      return $instance;
    }

    /**
     * @param \Components\Point $dimensions_
     *
     * @return \Components\Io_Image_Engine_Gd
     */
    public static function createImage(Point $dimensions_)
    {
      $instance=new static();
      $instance->m_resource=imagecreatetruecolor($dimensions_->x, $dimensions_->y);

      return $this;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Io_Image_Engine::dimensions() \Components\Io_Image_Engine::dimensions()
     *
     * @return \Components\Io_Image_Engine_Gd
     */
    public function dimensions()
    {
      if(null===$this->m_dimensions)
        $this->m_dimensions=new Point(imagesx($this->m_resource), imagesy($this->m_resource));

      return $this->m_dimensions;
    }

    /**
     * @see \Components\Io_Image_Engine::crop() \Components\Io_Image_Engine::crop()
     *
     * @return \Components\Io_Image_Engine_Gd
     */
    public function crop(Point $point_)
    {
      // TODO Implement.

      return $this;
    }

    /**
     * @see \Components\Io_Image_Engine::scale() \Components\Io_Image_Engine::scale()
     *
     * @return \Components\Io_Image_Engine_Gd
     */
    public function scale(Point $dimensions_)
    {
      $widthOriginal=imagesx($this->m_resource);
      $heightOriginal=imagesy($this->m_resource);

      $width=$dimensions_->x;
      $height=$dimensions_->y;

      if($width && !$height)
        $height=round($heightOriginal/($widthOriginal/$width));
      else if($height && !$width)
        $width=round($widthOriginal/($heightOriginal/$height));

      if($width===$widthOriginal && $height===$heightOriginal)
        return $this;

      if(imageistruecolor($this->m_resource))
        $tmp=imagecreatetruecolor($width, $height);
      else
        $tmp=imagecreate($width, $height);

      imagecopyresampled($tmp, $this->m_resource, 0, 0, 0, 0, $width, $height, imagesx($this->m_resource), imagesy($this->m_resource));
      imagedestroy($this->m_resource);

      $this->m_resource=$tmp;
      $this->m_dimensions=$dimensions_;

      return $this;
    }

    /**
     * @see \Components\Io_Image_Engine::save() \Components\Io_Image_Engine::save()
     *
     * @return \Components\Io_Image_Engine_Gd
     */
    public function save($path_, Io_Mimetype $type_=null)
    {
      if(null===$type_)
        $type_=Io_Mimetype::forFileExtension(Io::fileExtension($path_));
      if(null===$type_)
        $type_=Io_Mimetype::IMAGE_PNG();

      $typeName=$type_->name();

      if(false===isset(self::$m_saveHandler[$typeName]))
        throw new Exception_NotSupported('io/image/engine/gd', sprintf('Saving to image of requested type is not supported [%s].', $type_));

      $directory=dirname($path_);
      if(false===is_dir($directory))
        Io::directoryCreate($directory);

      $saveHandler=self::$m_saveHandler[$typeName];
      $saveHandler($this->m_resource, $path_);

      return $this;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_saveHandler=[
      Io_Mimetype::IMAGE_GIF=>'imagegif',
      Io_Mimetype::IMAGE_JPG=>'imagejpeg',
      Io_Mimetype::IMAGE_PNG=>'imagepng'
    ];

    /**
     * @var \Components\Point
     */
    private $m_dimensions;
    /**
     * @var resource
     */
    private $m_resource;
    //-----


    // DESTRUCTION
    public function __destruct()
    {
      imagedestroy($this->m_resource);
    }
    //--------------------------------------------------------------------------
  }
?>
