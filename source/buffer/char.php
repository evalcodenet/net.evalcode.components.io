<?php


namespace Components;


  /**
   * Io_Buffer_Char
   *
   * @api
   * @package net.evalcode.components.io
   * @subpackage buffer
   *
   * @author evalcode.net
   */
  class Io_Buffer_Char extends Io_Buffer_Byte
  {
    // OVERRIDES
    /**
     * @see \Components\Object::hashCode() \Components\Object::hashCode()
     */
    public function hashCode()
    {
      $hash=0;
      foreach($this->m_buffer as $value)
        $hash=31*$hash+$value;

      return $hash;
    }

    /**
     * @see \Components\Object::equals() \Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see \Components\Object::__toString() \Components\Object::__toString()
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
