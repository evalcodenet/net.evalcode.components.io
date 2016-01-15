<?php


namespace Components;


  /**
   * Io_Charset
   *
   * @api
   * @package net.evalcode.components.io
   *
   * @author evalcode.net
   *
   * @method \Components\Io_Charset ASCII
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
    const ASCII=LIBSTD_ENV_CHARSET_ASCII;
    const BINARY=LIBSTD_ENV_CHARSET_BINARY;
    const UTF_16=LIBSTD_ENV_CHARSET_UTF16;
    const UTF_16_BE=LIBSTD_ENV_CHARSET_UTF16BE;
    const UTF_16_LE=LIBSTD_ENV_CHARSET_UTF16LE;
    const UTF_8=LIBSTD_ENV_CHARSET_UTF8;
    const ISO_8859_1=LIBSTD_ENV_CHARSET_ISO8859_1;
    const ISO_8859_15=LIBSTD_ENV_CHARSET_ISO8859_15;
    const US_ASCII=LIBSTD_ENV_CHARSET_ASCII;
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
     * @param string $filepath_
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
      return self::forFilePath((string)$file_);
    }

    /**
     * @return \Components\Io_Charset
     */
    public static function defaultCharset()
    {
      if(null===self::$m_defaultCharset)
      {
        $defaultCharsetName=static::defaultCharsetName();

        if(false===isset(self::$m_charsets[$defaultCharsetName]))
          throw new Exception_IllegalArgument('io/charset', "Charset not supported [name: $defaultCharsetName].");

        self::$m_defaultCharset=static::{self::$m_charsets[$defaultCharsetName]}();
      }

      return self::$m_defaultCharset;
    }

    /**
     * @return string
     */
    public static function defaultCharsetName()
    {
      if(null===self::$m_defaultCharsetName)
        self::$m_defaultCharsetName=\env\charset();

      return self::$m_defaultCharsetName;
    }

    /**
     * @see \Components\Enumeration::values() values
     */
    public static function values()
    {
      return [
        'ASCII',
        'BINARY',
        'UTF_16',
        'UTF_16_BE',
        'UTF_16_LE',
        'UTF_8',
        'ISO_8859_1',
        'ISO_8859_15',
        'US_ASCII'
      ];
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @param string $string_
     * @param \Components\Io_Charset $toCharset_
     *
     * @return string
     */
    public function convert($string_, Io_Charset $toCharset_)
    {
      return mb_convert_encoding($string_, $toCharset_->m_name, $this->m_name);
    }

    /**
     * @param string $unicode_
     * @param \Components\Io_Charset $charset_
     *
     * @return string
     */
    public function unicodeDecode($unicode_, Io_Charset $charset_=null)
    {
      if(null===$charset_)
        $charset_=Io_Charset::UTF_16_BE();

      self::$m_convertImplTo=$this;
      self::$m_convertImplFrom=$charset_;

      return preg_replace_callback('/(?:\\\\u[0-9a-fA-Z]{4})+/',
        function($string_)
        {
          return Io_Charset::__unicodeDecodeConvertEncodingImpl(pack('H*',
            strtr($string_[0], ['\\u'=>''])
          ));
        },
        $unicode_
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var string[]
     */
    private static $m_charsets=[
      self::BINARY=>'BINARY',
      self::UTF_16=>'UTF_16',
      self::UTF_16_BE=>'UTF_16_BE',
      self::UTF_16_LE=>'UTF_16_LE',
      self::UTF_8=>'UTF_8',
      self::ISO_8859_1=>'ISO_8859_1',
      self::ISO_8859_15=>'ISO_8859_15',
      self::US_ASCII=>'ASCII',
      'UTF8'=>'UTF_8',
      'UTF16'=>'UTF_16',
      'ASCII'=>'ASCII'
    ];
    /**
     * @var \Components\Io_Charset
     */
    private static $m_defaultCharset;
    /**
     * @var string
     */
    private static $m_defaultCharsetName;
    /**
     * @var \Components\Io_Charset
     */
    private static $m_convertImplTo;
    /**
     * @var \Components\Io_Charset
     */
    private static $m_convertImplFrom;
    //-----


    /**
     * @internal
     */
    /*private*/ static function __unicodeDecodeConvertEncodingImpl($string_)
    {
      return mb_convert_encoding($string_, self::$m_convertImplTo->name(), self::$m_convertImplFrom->name());
    }
    //--------------------------------------------------------------------------
  }
?>
