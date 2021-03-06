<?php


namespace Components;


  /**
   * Io_Image_Engine
   *
   * @api
   * @package net.evalcode.components.io
   * @subpackage image
   *
   * @author evalcode.net
   */
  interface Io_Image_Engine /* TODO Object, .. */
  {
    // ACCESSORS
    /**
     * @return \Components\Point
     */
    function dimensions();

    /**
     * @param \Components\Point $point_
     *
     * @return \Components\Io_Image_Engine
     */
    function crop(Point $point_);

    /**
     * @param \Components\Point $point_
     *
     * @return \Components\Io_Image_Engine
     */
    function scale(Point $point_);

    /**
     * @param string $path_
     * @param string $type_
     *
     * @return \Components\Io_Image_Engine
     */
    function save($path_, Io_Mimetype $type_=null);
    //--------------------------------------------------------------------------
  }
?>
