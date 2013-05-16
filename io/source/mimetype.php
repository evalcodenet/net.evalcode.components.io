<?php


namespace Components;


  /**
   * Io_MimeType
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @method Io_MimeType APPLICATION_JSON
   * @method Io_MimeType APPLICATION_XML
   * @method Io_MimeType APPLICATION_ZIP
   * @method Io_MimeType APPLICATION_OCTET_STREAM
   * @method Io_MimeType APPLICATION_VND_APPLE_PKPASS
   * @method Io_MimeType IMAGE_GIF
   * @method Io_MimeType IMAGE_PNG
   * @method Io_MimeType IMAGE_JPG
   * @method Io_MimeType IMAGE_SVG_XML
   * @method Io_MimeType MULTIPART_ALTERNATIVE
   * @method Io_MimeType MULTIPART_DIGEST
   * @method Io_MimeType MULTIPART_ENCRYPTED
   * @method Io_MimeType MULTIPART_MIXED
   * @method Io_MimeType MULTIPART_RELATED
   * @method Io_MimeType TEXT_HTML
   * @method Io_MimeType TEXT_JSON
   * @method Io_MimeType TEXT_PLAIN
   *
   * @author evalcode.net
   */
  class Io_MimeType extends Enumeration
  {
    // MIME TYPES
    const APPLICATION_JSON='application/json';
    const APPLICATION_XML='application/xml';
    const APPLICATION_ZIP='application/zip';
    const APPLICATION_OCTET_STREAM='application/octet-stream';
    const APPLICATION_VND_APPLE_PKPASS='application/vnd.apple.pkpass';
    const IMAGE_GIF='image/gif';
    const IMAGE_PNG='image/png';
    const IMAGE_JPG='image/jpg';
    const IMAGE_JPEG='image/jpeg';
    const IMAGE_SVG_XML='image/svg+xml';
    const MULTIPART_ALTERNATIVE='multipart/alternative';
    const MULTIPART_DIGEST='multipart/digest';
    const MULTIPART_ENCRYPTED='multipart/encrypted';
    const MULTIPART_MIXED='multipart/mixed';
    const MULTIPART_RELATED='multipart/related';
    const TEXT_HTML='text/html';
    const TEXT_JSON='text/json';
    const TEXT_PLAIN='text/plain';
    const TEXT_PHP_SOURCE='text/php';


    // FILE EXTENSIONS
    const EXTENSION_BIN='bin';
    const EXTENSION_EXE='exe';
    const EXTENSION_GIF='gif';
    const EXTENSION_HTML='html';
    const EXTENSION_JPG='jpg';
    const EXTENSION_JPEG='jpeg';
    const EXTENSION_JSON='json';
    const EXTENSION_PKPASS='pkpass';
    const EXTENSION_PNG='png';
    const EXTENSION_SVG='svg';
    const EXTENSION_TXT='txt';
    const EXTENSION_XML='xml';
    const EXTENSION_ZIP='zip';

    // MIME TYPE ICON SIZES
    const ICON_SIZE_16=16;
    const ICON_SIZE_24=24;
    const ICON_SIZE_32=32;
    const ICON_SIZE_48=48;
    const ICON_SIZE_64=64;
    const ICON_SIZE_128=128;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($name_, Io_Charset $charset_=null)
    {
      parent::__construct($name_);

      if(null===$charset_)
        $charset_=Io_Charset::defaultCharset();

      $this->m_charset=$charset_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $name_
     * @param \Components\Io_Charset $charset_
     *
     * @return \Components\Io_MimeType
     */
    public static function forName($name_, Io_Charset $charset_=null)
    {
      $name=self::$m_mapMimeTypes[$name_];

      return static::$name($charset_);
    }

    /**
     * @param \Components\Io_File $file_
     * @param \Components\Io_Charset $charset_
     *
     * @return \Components\Io_MimeType
     */
    public static function forFile(Io_File $file_, Io_Charset $charset_=null)
    {
      return self::forFilePath((string)$file_, $charset_);
    }

    /**
     * @param string $fileExtension_
     * @param \Components\Io_Charset $charset_
     *
     * @return \Components\Io_MimeType
     */
    public static function forFileName($filename_, Io_Charset $charset_=null)
    {
      return self::forFileExtension(Io::fileExtension($filename_));
    }

    /**
     * @param string $fileExtension_
     * @param \Components\Io_Charset $charset_
     *
     * @return \Components\Io_MimeType
     */
    public static function forFileExtension($fileExtension_, Io_Charset $charset_=null)
    {
      $fileExtension_=strtolower($fileExtension_);
      if(false===isset(self::$m_mapFileExtensions[$fileExtension_]))
        return null;

      $name=self::$m_mapFileExtensions[$fileExtension_];

      return static::$name($charset_);
    }

    /**
     * @param string $file_
     *
     * @return \Components\Io_MimeType
     */
    public static function forFilePath($file_, Io_Charset $charset_=null)
    {
      $info=null;
      if($finfo=@finfo_open(FILEINFO_MIME))
      {
        $info=@finfo_file($finfo, $file_);
        @finfo_close($finfo);
      }

      if(false===$info)
        return self::forFileName($file_);

      $mimetype=trim(substr($info, 0, strpos($info, ';')));

      if(isset(self::$m_mapMimeTypes[$mimetype]))
        $name=self::$m_mapMimeTypes[$mimetype];
      else if(0===strpos($mimetype, 'text'))
        $name=self::$m_mapMimeTypes[self::TEXT_PLAIN];
      else
        $name=self::$m_mapMimeTypes[self::APPLICATION_OCTET_STREAM];

      if(null===$charset_)
      {
        $charset=trim(substr($info, strpos($info, ';')+1));

        return static::$name(Io_Charset::forName($charset));
      }

      return static::$name($charset_);
    }

    /**
     * @param string $filename_
     *
     * @return boolean
     */
    public static function isImageFileName($filename_)
    {
      return self::isImageFileExtension(Io::fileExtension($filename_));
    }

    /**
     * @param string $fileExtension_
     *
     * @return boolean
     */
    public static function isImageFileExtension($fileExtension_)
    {
      if(isset(self::$m_mapFileExtensions[$fileExtension_]))
      {
        $mimeType=self::$m_mapFileExtensions[$fileExtension_];

        return 'image'===substr($mimeType, 0, strpos($mimeType, '/'));
      }

      return false;
    }

    /**
     * @see Components\Enumeration::values()
     */
    public static function values()
    {
      return array_values(self::$m_mapMimeTypes);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return string
     */
    public function title()
    {
      if($this->isImage())
        return I18n::translatef('io/mimetype/image', strtoupper($this->type()));

      return I18n::translatef('io/mimetype/file', strtoupper($this->type()));
    }

    /**
     * @return string
     */
    public function category()
    {
      if(null===$this->m_category)
        $this->m_category=substr($this->m_name, 0, strpos($this->m_name, '/'));

      return $this->m_category;
    }

    /**
     * @return string
     */
    public function type()
    {
      if(null===$this->m_type)
        $this->m_type=substr($this->m_name, strpos($this->m_name, '/')+1);

      return $this->m_type;
    }

    /**
     * @return string
     */
    public function fileExtension()
    {
      return self::$m_mapMimeTypeFileExtensions[$this->m_name];
    }

    /**
     * @return bool
     */
    public function isImage()
    {
      return 'image'===$this->category();
    }

    /**
     * @return bool
     */
    public function isText()
    {
      return 'text'===$this->category();
    }

    /**
     * @return boolean
     */
    public function isArchive()
    {
      return isset(self::$m_mapArchiveMimeTypes[$this->m_name]);
    }

    /**
     * @return \Components\Io_Charset
     */
    public function charset()
    {
      return $this->m_charset;
    }

    /**
     * @param string $size_
     *
     * @return string
     */
    public function icon($size_=self::ICON_SIZE_16)
    {
      return sprintf('/io/resource/image/icon/mime/%1$s/%2$s.png', $size_, $this->m_name);
    }

    /**
     * @param string $size_
     *
     * @return string
     */
    public function iconClass($size_=self::ICON_SIZE_16)
    {
      return sprintf('io_mime_icon io_mime_icon_%1$s io_mime_icon_%2$s io_mime_icon_%3$s',
        $size_,
        $this->category(),
        strtr($this->m_name, '/','_')
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_finfo;
    private static $m_mapFileExtensions=array(
      self::EXTENSION_JSON=>'APPLICATION_JSON',
      self::EXTENSION_BIN=>'APPLICATION_OCTET_STREAM',
      self::EXTENSION_EXE=>'APPLICATION_OCTET_STREAM',
      self::EXTENSION_XML=>'APPLICATION_XML',
      self::EXTENSION_ZIP=>'APPLICATION_ZIP',
      self::EXTENSION_GIF=>'IMAGE_GIF',
      self::EXTENSION_PKPASS=>'APPLICATION_VND_APPLE_PKPASS',
      self::EXTENSION_PNG=>'IMAGE_PNG',
      self::EXTENSION_JPG=>'IMAGE_JPG',
      self::EXTENSION_JPEG=>'IMAGE_JPG',
      self::EXTENSION_SVG=>'IMAGE_SVG_XML',
      self::EXTENSION_HTML=>'TEXT_HTML',
      self::EXTENSION_TXT=>'TEXT_PLAIN'
      // TODO complete ...
    );
    private static $m_mapMimeTypes=array(
      self::APPLICATION_JSON=>'APPLICATION_JSON',
      self::APPLICATION_OCTET_STREAM=>'APPLICATION_OCTET_STREAM',
      self::APPLICATION_XML=>'APPLICATION_XML',
      self::APPLICATION_ZIP=>'APPLICATION_ZIP',
      self::APPLICATION_VND_APPLE_PKPASS=>'APPLICATION_VND_APPLE_PKPASS',
      self::IMAGE_GIF=>'IMAGE_GIF',
      self::IMAGE_PNG=>'IMAGE_PNG',
      self::IMAGE_JPG=>'IMAGE_JPG',
      self::IMAGE_JPEG=>'IMAGE_JPG',
      self::IMAGE_SVG_XML=>'IMAGE_SVG_XML',
      self::MULTIPART_ALTERNATIVE=>'MULTIPART_ALTERNATIVE',
      self::MULTIPART_DIGEST=>'MULTIPART_DIGEST',
      self::MULTIPART_ENCRYPTED=>'MULTIPART_ENCRYPTED',
      self::MULTIPART_MIXED=>'MULTIPART_MIXED',
      self::MULTIPART_RELATED=>'MULTIPART_RELATED',
      self::TEXT_HTML=>'TEXT_HTML',
      self::TEXT_JSON=>'TEXT_JSON',
      self::TEXT_PLAIN=>'TEXT_PLAIN'
      // TODO complete ...
    );
    private static $m_mapMimeTypeFileExtensions=array(
      self::APPLICATION_JSON=>self::EXTENSION_JSON,
      self::APPLICATION_OCTET_STREAM=>self::EXTENSION_BIN,
      self::APPLICATION_XML=>self::EXTENSION_XML,
      self::APPLICATION_ZIP=>self::EXTENSION_ZIP,
      self::APPLICATION_VND_APPLE_PKPASS=>self::EXTENSION_PKPASS,
      self::IMAGE_GIF=>self::EXTENSION_GIF,
      self::IMAGE_PNG=>self::EXTENSION_PNG,
      self::IMAGE_JPG=>self::EXTENSION_JPG,
      self::IMAGE_JPEG=>self::EXTENSION_JPG,
      self::IMAGE_SVG_XML=>self::EXTENSION_SVG,
      self::TEXT_HTML=>self::EXTENSION_HTML,
      self::TEXT_JSON=>self::EXTENSION_JSON,
      self::TEXT_PLAIN=>self::EXTENSION_TXT
    );

    private static $m_mapArchiveMimeTypes=array(
      self::APPLICATION_ZIP=>'APPLICATION_ZIP',
      self::APPLICATION_VND_APPLE_PKPASS=>'APPLICATION_VND_APPLE_PKPASS'
    );

    private $m_type;
    private $m_category;
    /**
     * @var Components\Io_Charset
     */
    private $m_charset;
    //--------------------------------------------------------------------------
  }
?>
