<?php


namespace Components;


  /**
   * Io_Filesize
   *
   * @api
   * @package net.evalcode.components.io
   *
   * @author evalcode.net
   */
  class Io_Filesize extends Integer
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


    // STATIC ACCESSORS
    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return float
     */
    public static function convertToKB($bytes_, $round_=self::ROUND_DEFAULT)
    {
      return round($bytes_/self::FACTOR_BYTES_KB, $round_);
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return float
     */
    public static function convertToMB($bytes_, $round_=self::ROUND_DEFAULT)
    {
      return round($bytes_/self::FACTOR_BYTES_MB, $round_);
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return float
     */
    public static function convertToGB($bytes_, $round_=self::ROUND_DEFAULT)
    {
      return round($bytes_/self::FACTOR_BYTES_GB, $round_);
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return float
     */
    public static function convertToTB($bytes_, $round_=self::ROUND_DEFAULT)
    {
      return round($bytes_/self::FACTOR_BYTES_TB, $round_);
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return string
     */
    public static function format($bytes_, $round_=self::ROUND_DEFAULT)
    {
      if(self::FACTOR_BYTES_KB>$bytes_)
        return static::formatAsBytes($bytes_, $round_);
      if(self::FACTOR_BYTES_MB>$bytes_)
        return static::formatAsKiloBytes($bytes_, $round_);
      if(self::FACTOR_BYTES_GB>$bytes_)
        return static::formatAsMegaBytes($bytes_, $round_);
      if(self::FACTOR_BYTES_TB>$bytes_)
        return static::formatAsGigaBytes($bytes_, $round_);

      return static::formatAsTeraBytes($bytes_, $round_);
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return string
     */
    public static function formatAsBytes($bytes_, $round_=self::ROUND_DEFAULT, $append_=self::BYTES)
    {
      return static::convertToKB($bytes_, $round_)." $append_";
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return string
     */
    public static function formatAsKiloBytes($bytes_, $round_=self::ROUND_DEFAULT, $append_=self::KILO_BYTES)
    {
      return static::convertToKB($bytes_, $round_)." $append_";
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return string
     */
    public static function formatAsMegaBytes($bytes_, $round_=self::ROUND_DEFAULT, $append_=self::MEGA_BYTES)
    {
      return static::convertToMB($bytes_, $round_)." $append_";
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return string
     */
    public static function formatAsGigaBytes($bytes_, $round_=self::ROUND_DEFAULT, $append_=self::GIGA_BYTES)
    {
      return static::convertToGB($bytes_, $round_)." $append_";
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return string
     */
    public static function formatAsTeraBytes($bytes_, $round_=self::ROUND_DEFAULT, $append_=self::TERA_BYTES)
    {
      return static::convertToTB($bytes_, $round_)." $append_";
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return number
     */
    public function bytes()
    {
      return $this->m_value;
    }

    /**
     * @param integer $round_
     *
     * @return number
     */
    public function kiloBytes($round_=self::ROUND_DEFAULT)
    {
      return static::convertToKB($this->m_value, $round_);
    }

    /**
     * @param integer $round_
     *
     * @return number
     */
    public function megaBytes($round_=self::ROUND_DEFAULT)
    {
      return static::convertToMB($this->m_value, $round_);
    }

    /**
     * @param integer $round_
     *
     * @return number
     */
    public function gigaBytes($round_=self::ROUND_DEFAULT)
    {
      return static::convertToGB($this->m_value, $round_);
    }

    /**
     * @param integer $round_
     *
     * @return number
     */
    public function teraBytes($round_=self::ROUND_DEFAULT)
    {
      return static::convertToTB($this->m_value, $round_);
    }

    /**
     * @param integer $round_
     *
     * @return string
     */
    public function formatted($round_=self::ROUND_DEFAULT)
    {
      return static::format($this->m_value, $round_);
    }

    /**
     * @param integer $round_
     *
     * @return string
     */
    public function formattedKB($round_=self::ROUND_DEFAULT)
    {
      return static::formatAsKiloBytes($this->m_value, $round_);
    }

    /**
     * @param integer $round_
     *
     * @return string
     */
    public function formattedMB($round_=self::ROUND_DEFAULT)
    {
      return static::formatAsMegaBytes($this->m_value, $round_);
    }

    /**
     * @param integer $round_
     *
     * @return string
     */
    public function formattedGB($round_=self::ROUND_DEFAULT)
    {
      return static::formatAsGigaBytes($this->m_value, $round_);
    }

    /**
     * @param integer $round_
     *
     * @return string
     */
    public function formattedTB($round_=self::ROUND_DEFAULT)
    {
      return static::formatAsTeraBytes($this->m_value, $round_);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Cloneable::__clone() \Components\Cloneable::__clone()
     */
    public function __clone()
    {
      return new self($this->m_value);
    }

    /**
     * @see \Components\Comparable::compareTo()) \Components\Comparable::compareTo())
     */
    public function compareTo($object_)
    {
      if(is_numeric($object_))
      {
        if($this->m_value===$object_)
          return 0;

        return $this->m_value<$object_->m_value?-1:1;
      }

      if($object_ instanceof Integer)
      {
        if($this->m_value===$object_->m_value)
          return 0;

        return $this->m_value<$object_->m_value?-1:1;
      }

      throw new Io_Exception('io/filesize', 'Can not compare to object of given type.');
    }

    /**
     * @see \Components\Object::hashCode() \Components\Object::hashCode()
     */
    public function hashCode()
    {
      return integer_hash($this->m_value);
    }

    /**
     * @see \Components\Object::equals() \Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_value===$object_->m_value;

      return false;
    }

    /**
     * @see \Components\Object::__toString() \Components\Object::__toString()
     */
    public function __toString()
    {
      return static::format($this->m_value);
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Serializable::serialVersionUid() \Components\Serializable::serialVersionUid()
     */
    public function serialVersionUid()
    {
      return 1;
    }
    //--------------------------------------------------------------------------
  }
?>
