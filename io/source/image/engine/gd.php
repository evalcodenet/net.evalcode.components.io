<?php


  /**
   * Io_Image_Engine_Gd
   *
   * @package net.evalcode.components
   * @subpackage io.image.engine
   *
   * @author evalcode.net
   */
  class Io_Image_Engine_Gd implements Io_Image_Engine /* TODO Object, .. */
  {
    // CONSTRUCTION
    private function __construct()
    {

    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param Point $dimensions_
     *
     * @return Io_Image_Engine_Gd
     */
    public static function forBase64($base64_)
    {
      $instance=new self();
      $instance->m_resource=@imagecreatefromstring(String::decodeBase64($base64_));

      return $instance;
    }

    /**
     * @param Point $dimensions_
     *
     * @return Io_Image_Engine_Gd
     */
    public static function forPath($path_)
    {
      if(false===@is_file($path_))
        throw new Io_Exception('io/image/engine/gd', sprintf('Image for given path does not exist [%1$s].', $path_));

      $instance=new self();
      $instance->m_resource=@imagecreatefromstring(file_get_contents($path_));

      return $instance;
    }

    /**
     * @param Point $dimensions_
     *
     * @return Io_Image_Engine_Gd
     */
    public static function createImage(Point $dimensions_)
    {
      $instance=new self();
      $instance->m_resource=@imagecreatetruecolor($dimensions_->x, $dimensions_->y);

      return $this;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * (non-PHPdoc)
     * @see Io_Image_Engine::dimensions()
     */
    public function dimensions()
    {
      if(null===$this->m_dimensions)
        $this->m_dimensions=new Point(@imagesx($this->m_resource), @imagesy($this->m_resource));

      return $this->m_dimensions;
    }

    /**
     * (non-PHPdoc)
     * @see Io_Image_Engine::crop()
     */
    public function crop(Point $point_)
    {
      return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Io_Image_Engine::scale()
     */
    public function scale(Point $dimensions_)
    {
      if(@imageistruecolor($this->m_resource))
        $tmp=@imagecreatetruecolor($dimensions_->x, $dimensions_->y);
      else
        $tmp=@imagecreate($dimensions_->x, $dimensions_->y);

      @imagecopyresampled($tmp, $this->m_resource, 0, 0, 0, 0, $dimensions_->x, $dimensions_->y, @imagesx($this->m_resource), @imagesy($this->m_resource));
      @imagedestroy($this->m_resource);

      $this->m_resource=$tmp;
      $this->m_dimensions=$dimensions_;

      return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Io_Image_Engine::save()
     */
    public function save($path_, $type_=Io_MimeType::IMAGE_PNG)
    {
      @imagepng($this->m_resource, $path_);

      return $this;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var Point
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
      @imagedestroy($this->m_resource);
    }
    //--------------------------------------------------------------------------
  }
?>
