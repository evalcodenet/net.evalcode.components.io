<?php


  /**
   * Io_Buffer_Char
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   */
  class Io_Buffer_Char extends Io_Buffer_Byte
  {
    // OVERRIDES
    /**
     * @see Object::hashCode()
     */
    public function hashCode()
    {
      $hash=0;
      foreach($this->m_buffer as $value)
        $hash=31*$hash+$value;

      return $hash;
    }

    /**
     * @see Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{capacity: %s, position: %s, limit: %s, mark: %s}',
        __CLASS__,
        $this->hashCode(),
        $this->m_capacity,
        $this->m_position,
        $this->m_limit,
        $this->m_mark
      );
    }
    //--------------------------------------------------------------------------
  }
?>
