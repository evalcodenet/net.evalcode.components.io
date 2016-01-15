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

    const FACTOR_BYTES_KB=LIBSTD_IO_BYTES_KB;
    const FACTOR_BYTES_MB=LIBSTD_IO_BYTES_MB;
    const FACTOR_BYTES_GB=LIBSTD_IO_BYTES_GB;
    const FACTOR_BYTES_TB=LIBSTD_IO_BYTES_TB;

    const ROUND_DEFAULT=2;
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return float
     */
    public static function bytesToKb($bytes_, $round_=self::ROUND_DEFAULT)
    {
      return \io\bytesToKb($bytes_, $round_);
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return float
     */
    public static function bytesToMb($bytes_, $round_=self::ROUND_DEFAULT)
    {
      return \io\bytesToMb($bytes_, $round_);
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return float
     */
    public static function bytesToGb($bytes_, $round_=self::ROUND_DEFAULT)
    {
      return \io\bytesToGb($bytes_, $round_);
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return float
     */
    public static function bytesToTb($bytes_, $round_=self::ROUND_DEFAULT)
    {
      return \io\bytesToTb($bytes_, $round_);
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
        return static::formatBytes($bytes_, $round_);
      if(self::FACTOR_BYTES_MB>$bytes_)
        return static::formatKb($bytes_, $round_);
      if(self::FACTOR_BYTES_GB>$bytes_)
        return static::formatMb($bytes_, $round_);
      if(self::FACTOR_BYTES_TB>$bytes_)
        return static::formatGb($bytes_, $round_);

      return static::formatTb($bytes_, $round_);
    }

    /**
     * @param integer $bytes_
     * @param string $append_
     *
     * @return string
     */
    public static function formatBytes($bytes_, $append_=self::BYTES)
    {
      return "$bytes_ $append_";
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     * @param string $append_
     *
     * @return string
     */
    public static function formatBytesAsKb($bytes_, $round_=self::ROUND_DEFAULT, $append_=self::KILO_BYTES)
    {
      return \io\bytesToKb($bytes_, $round_)." $append_";
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     * @param string $append_
     *
     * @return string
     */
    public static function formatBytesAsMb($bytes_, $round_=self::ROUND_DEFAULT, $append_=self::MEGA_BYTES)
    {
      return \io\bytesToMb($bytes_, $round_)." $append_";
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     * @param string $append_
     *
     * @return string
     */
    public static function formatBytesAsGb($bytes_, $round_=self::ROUND_DEFAULT, $append_=self::GIGA_BYTES)
    {
      return \io\bytesToGb($bytes_, $round_)." $append_";
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     * @param string $append_
     *
     * @return string
     */
    public static function formatBytesAsTb($bytes_, $round_=self::ROUND_DEFAULT, $append_=self::TERA_BYTES)
    {
      return \io\bytesToTb($bytes_, $round_)." $append_";
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return integer
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
    public function toKb($round_=self::ROUND_DEFAULT)
    {
      return \io\bytesToKb($this->m_value, $round_);
    }

    /**
     * @param integer $round_
     *
     * @return number
     */
    public function toMb($round_=self::ROUND_DEFAULT)
    {
      return \io\bytesToMb($this->m_value, $round_);
    }

    /**
     * @param integer $round_
     *
     * @return number
     */
    public function toGb($round_=self::ROUND_DEFAULT)
    {
      return \io\bytesToGb($this->m_value, $round_);
    }

    /**
     * @param integer $round_
     *
     * @return number
     */
    public function toTb($round_=self::ROUND_DEFAULT)
    {
      return \io\bytesToTb($this->m_value, $round_);
    }

    /**
     * @param integer $round_
     *
     * @return string
     */
    public function formatAsBytes($round_=self::ROUND_DEFAULT)
    {
      return static::formatBytes($this->m_value, $round_);
    }

    /**
     * @param integer $round_
     *
     * @return string
     */
    public function formatAsKb($round_=self::ROUND_DEFAULT)
    {
      return static::formatBytesAsKb($this->m_value, $round_);
    }

    /**
     * @param integer $round_
     *
     * @return string
     */
    public function formatAsMb($round_=self::ROUND_DEFAULT)
    {
      return static::formatBytesAsMb($this->m_value, $round_);
    }

    /**
     * @param integer $round_
     *
     * @return string
     */
    public function formatAsGb($round_=self::ROUND_DEFAULT)
    {
      return static::formatBytesAsGb($this->m_value, $round_);
    }

    /**
     * @param integer $round_
     *
     * @return string
     */
    public function formatAsTb($round_=self::ROUND_DEFAULT)
    {
      return static::formatBytesAsTb($this->m_value, $round_);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Cloneable::__clone() __clone
     */
    public function __clone()
    {
      return new self($this->m_value);
    }

    /**
     * @see \Components\Comparable::compareTo() compareTo
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
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hashi($this->m_value);
    }

    /**
     * @see \Components\Object::equals() equals
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_value===$object_->m_value;

      return false;
    }

    /**
     * @see \Components\Object::__toString() __toString
     */
    public function __toString()
    {
      return static::format($this->m_value);
    }

    /**
     * (non-PHPdoc)
     * @see \Components\Serializable::serialVersionUid() serialVersionUid
     */
    public function serialVersionUid()
    {
      return 1;
    }
    //--------------------------------------------------------------------------
  }
?>
