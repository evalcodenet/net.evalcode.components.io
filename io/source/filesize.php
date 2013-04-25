<?php


namespace Components;


  /**
   * Io_Filesize
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   */
  class Io_Filesize implements Object, Cloneable, Comparable
  {
    // PREDEFINED PROPERTIES
    const BYTES='Bytes';
    const KILO_BYTES='KB';
    const MEGA_BYTES='MB';
    const GIGA_BYTES='GB';
    const TERA_BYTES='TB';

    const FACTOR_BYTES_KB=1024;
    const FACTOR_BYTES_MB=1048576;
    const FACTOR_BYTES_GB=1073741824;
    const FACTOR_BYTES_TB=1099511627776;

    const ROUND_DEFAULT=2;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($bytes_)
    {
      $this->m_bytes=$bytes_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param int $bytes_
     * @param int $round_
     *
     * @return float
     */
    public static function convertToKB($bytes_, $round_=self::ROUND_DEFAULT)
    {
      return round($bytes_/self::FACTOR_BYTES_KB, $round_);
    }

    /**
     * @param int $bytes_
     * @param int $round_
     *
     * @return float
     */
    public static function convertToMB($bytes_, $round_=self::ROUND_DEFAULT)
    {
      return round($bytes_/self::FACTOR_BYTES_MB, $round_);
    }

    /**
     * @param int $bytes_
     * @param int $round_
     *
     * @return float
     */
    public static function convertToGB($bytes_, $round_=self::ROUND_DEFAULT)
    {
      return round($bytes_/self::FACTOR_BYTES_GB, $round_);
    }

    /**
     * @param int $bytes_
     * @param int $round_
     *
     * @return float
     */
    public static function convertToTB($bytes_, $round_=self::ROUND_DEFAULT)
    {
      return round($bytes_/self::FACTOR_BYTES_TB, $round_);
    }

    /**
     * @param int $bytes_
     * @param int $round_
     *
     * @return string
     */
    public static function format($bytes_, $round_=self::ROUND_DEFAULT)
    {
      if(self::FACTOR_BYTES_KB>$bytes_)
        return self::formatAsBytes($bytes_, $round_);
      if(self::FACTOR_BYTES_MB>$bytes_)
        return self::formatAsKiloBytes($bytes_, $round_);
      if(self::FACTOR_BYTES_GB>$bytes_)
        return self::formatAsMegaBytes($bytes_, $round_);
      if(self::FACTOR_BYTES_TB>$bytes_)
        return self::formatAsGigaBytes($bytes_, $round_);

      return self::formatAsTeraBytes($bytes_, $round_);
    }

    /**
     * @param int $bytes_
     * @param int $round_
     *
     * @return string
     */
    public static function formatAsBytes($bytes_, $round_=self::ROUND_DEFAULT, $append_=self::BYTES)
    {
      return self::convertToKB($bytes_, $round_)." $append_";
    }

    /**
     * @param int $bytes_
     * @param int $round_
     *
     * @return string
     */
    public static function formatAsKiloBytes($bytes_, $round_=self::ROUND_DEFAULT, $append_=self::KILO_BYTES)
    {
      return self::convertToKB($bytes_, $round_)." $append_";
    }

    /**
     * @param int $bytes_
     * @param int $round_
     *
     * @return string
     */
    public static function formatAsMegaBytes($bytes_, $round_=self::ROUND_DEFAULT, $append_=self::MEGA_BYTES)
    {
      return self::convertToMB($bytes_, $round_)." $append_";
    }

    /**
     * @param int $bytes_
     * @param int $round_
     *
     * @return string
     */
    public static function formatAsGigaBytes($bytes_, $round_=self::ROUND_DEFAULT, $append_=self::GIGA_BYTES)
    {
      return self::convertToGB($bytes_, $round_)." $append_";
    }

    /**
     * @param int $bytes_
     * @param int $round_
     *
     * @return string
     */
    public static function formatAsTeraBytes($bytes_, $round_=self::ROUND_DEFAULT, $append_=self::TERA_BYTES)
    {
      return self::convertToTB($bytes_, $round_)." $append_";
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @return number
     */
    public function bytes()
    {
      return $this->m_bytes;
    }

    /**
     * @param int $round_
     *
     * @return number
     */
    public function kiloBytes($round_=self::ROUND_DEFAULT)
    {
      return self::convertToKB($this->m_bytes, $round_);
    }

    /**
     * @param int $round_
     *
     * @return number
     */
    public function megaBytes($round_=self::ROUND_DEFAULT)
    {
      return self::convertToMB($this->m_bytes, $round_);
    }

    /**
     * @param int $round_
     *
     * @return number
     */
    public function gigaBytes($round_=self::ROUND_DEFAULT)
    {
      return self::convertToGB($this->m_bytes, $round_);
    }

    /**
     * @param int $round_
     *
     * @return number
     */
    public function teraBytes($round_=self::ROUND_DEFAULT)
    {
      return self::convertToTB($this->m_bytes, $round_);
    }


    /**
     * @param int $round_
     *
     * @return string
     */
    public function formatted($round_=self::ROUND_DEFAULT)
    {
      return self::format($this->m_bytes, $round_);
    }

    /**
     * @param int $round_
     *
     * @return string
     */
    public function formattedKB($round_=self::ROUND_DEFAULT)
    {
      return self::formatAsKiloBytes($this->m_bytes, $round_);
    }

    /**
     * @param int $round_
     *
     * @return string
     */
    public function formattedMB($round_=self::ROUND_DEFAULT)
    {
      return self::formatAsMegaBytes($this->m_bytes, $round_);
    }

    /**
     * @param int $round_
     *
     * @return string
     */
    public function formattedGB($round_=self::ROUND_DEFAULT)
    {
      return self::formatAsGigaBytes($this->m_bytes, $round_);
    }

    /**
     * @param int $round_
     *
     * @return string
     */
    public function formattedTB($round_=self::ROUND_DEFAULT)
    {
      return self::formatAsTeraBytes($this->m_bytes, $round_);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function __clone()
    {
      return new self($this->m_bytes);
    }

    public function compareTo($object_)
    {
      if($object_ instanceof self)
      {
        if($this->m_bytes===$object_->m_bytes)
          return 0;

        return $this->m_bytes<$object_->m_bytes?-1:1;
      }

      throw new Runtime_Exception('io/filesize', 'Can not compare to object of given type.');
    }

    /**
     * (non-PHPdoc)
     * @see Object::hashCode()
     */
    public function hashCode()
    {
      // TODO Implement Number::hash()
      return String::hash((string)$this->m_bytes);
    }

    /**
     * (non-PHPdoc)
     * @see Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_bytes===$object_->m_bytes;

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Object::__toString()
     */
    public function __toString()
    {
      return self::format($this->m_bytes);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var int
     */
    private $m_bytes;
    //--------------------------------------------------------------------------
  }
?>
