<?php


namespace Components;


  /**
   * Io_Image_Engine
   *
   * @package net.evalcode.components
   * @subpackage io.image
   *
   * @author evalcode.net
   */
  interface Io_Image_Engine
  {
    // ACCESSORS/MUTATORS
    /**
     * @return Point
     */
    function dimensions();

    /**
     * @param Point $point_
     *
     * @return Io_Image_Engine
     */
    function crop(Point $point_);

    /**
     * @param Point $point_
     *
     * @return Io_Image_Engine
     */
    function scale(Point $point_);

    /**
     * @param string $path_
     * @param string $type_
     *
     * @return Io_Image_Engine
     */
    function save($path_, $type_=Io_MimeType::IMAGE_PNG);
    //--------------------------------------------------------------------------
  }
?>
