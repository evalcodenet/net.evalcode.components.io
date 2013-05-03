<?php


namespace Components;


  /**
   * Io_Charset
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   *
   * @method \Components\Io_Charset BINARY
   * @method \Components\Io_Charset UTF_16
   * @method \Components\Io_Charset UTF_16_BE
   * @method \Components\Io_Charset UTF_16_LE
   * @method \Components\Io_Charset UTF_8
   * @method \Components\Io_Charset ISO_8859_1
   * @method \Components\Io_Charset ISO_8859_15
   * @method \Components\Io_Charset US_ASCII
   */
  class Io_Charset extends Enumeration
  {
    // PREDEFINED PROPERTIES
    const BINARY='BINARY';
    const UTF_16='UTF-16';
    const UTF_16_BE='UTF-16BE';
    const UTF_16_LE='UTF-16LE';
    const UTF_8='UTF-8';
    const ISO_8859_1='ISO-8859-1';
    const ISO_8859_15='ISO-8859-15';
    const US_ASCII='US-ASCII';

    const DEFAULT_CHARSET=self::UTF_8;
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $charset_
     *
     * @return \Components\Io_Charset
     */
    public static function forName($name_)
    {
      $name_=strtoupper($name_);
      if(isset(self::$m_charsets[$name_]))
      {
        $charset=self::$m_charsets[$name_];

        return self::$charset();
      }

      return null;
    }

    /**
     * @param string $filename_
     *
     * @return \Components\Io_Charset
     */
    public static function forFilePath($filepath_)
    {
      $info=null;
      if($finfo=@finfo_open(FILEINFO_MIME_ENCODING))
      {
        $info=@finfo_file($finfo, $filepath_);
        @finfo_close($finfo);
      }

      if(false===$info)
        return self::defaultCharset();

      return self::forName($info);
    }

    /**
     * @param \Components\Io_File $file_
     *
     * @return \Components\Io_Charset
     */
    public static function forFile(Io_File $file_)
    {
      return self::forFileName((string)$file_);
    }

    /**
     * @return \Components\Io_Charset
     */
    public static function defaultCharset()
    {
      if(null===self::$m_defaultCharset)
        self::$m_defaultCharset=static::UTF_8();

      return self::$m_defaultCharset;
    }

    /**
     * @see Components.Enumeration::values()
     */
    public static function values()
    {
      return array(
        'BINARY',
        'UTF_16',
        'UTF_16_BE',
        'UTF_16_LE',
        'UTF_8',
        'ISO_8859_1',
        'ISO_8859_15',
        'US_ASCII'
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Io_Charset
     */
    private static $m_defaultCharset;

    private static $m_charsets=array(
      self::BINARY=>'BINARY',
      self::UTF_16=>'UTF_16',
      self::UTF_16_BE=>'UTF_16_BE',
      self::UTF_16_LE=>'UTF_16_LE',
      self::UTF_8=>'UTF_8',
      self::ISO_8859_1=>'ISO_8859_1',
      self::ISO_8859_15=>'ISO_8859_15',
      self::US_ASCII=>'US_ASCII',
      'UTF8'=>'UTF_8',
      'UTF16'=>'UTF_16',
      'ASCII'=>'US_ASCII'
    );
    //--------------------------------------------------------------------------
  }
?>
