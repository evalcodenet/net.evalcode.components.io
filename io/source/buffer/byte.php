<?php


namespace Components;


  /**
   * Io_Buffer_Byte
   *
   * @package net.evalcode.components
   * @subpackage io.buffer
   *
   * @author evalcode.net
   */
  class Io_Buffer_Byte implements Io_Buffer
  {
    // CONSTRUCTION
    /**
     * @see Components\Io_Buffer::__construct()
     */
    public function __construct($capacity_)
    {
      $this->m_capacity=$this->m_limit=$capacity_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * Allocates a new buffer of size of given capacity.
     *
     * @param integer $limit_
     *
     * @return \Components\Io_Buffer_Byte
     */
    public static function allocate($capacity_=4096)
    {
      return new static($capacity_);
    }

    /**
     * Creates a new buffer using given array as internal storage.
     *
     * <p>
     * Uses dimensions of given array as new buffer's capacity.
     * </p>
     *
     * <p>
     * Changes to the array will be visible to the buffer, changes made
     * by the buffer will be visible to accessors of the given array as
     * well.
     * </p>
     *
     * @param array $buffer_
     *
     * @return \Components\Io_Buffer_Byte
     */
    public static function wrap(&$buffer_)
    {
      $buffer=new static(count($buffer_));
      $buffer->m_buffer=&$buffer_;

      return $buffer;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see Components\Io_Buffer::capacity()
     */
    public function capacity()
    {
      return $this->m_capacity;
    }

    /**
     * @see Components\Io_Buffer::read()
     */
    public function read(Io_Buffer $target_)
    {
      $amount=$this->capacity();
      $target_->appendBuffer($this);

      return $amount;
    }

    /**
     * @see Components\Io_Buffer::next()
     */
    public function next()
    {
      return $this->m_buffer[$this->m_position++];
    }

    /**
     * @see Components\Io_Buffer::get()
     */
    public function get($position_=null)
    {
      if(null===$position_)
        return $this->m_buffer[$this->m_position];

      return $this->m_buffer[$position_];
    }

    /**
     * @see Components\Io_Buffer::append()
     *
     * @return \Components\Io_Buffer_Byte
     */
    public function append($value_)
    {
      array_push($this->m_buffer, $value_);
      $this->m_position++;

      return $this;
    }

    /**
     * @see Components\Io_Buffer::appendBuffer()
     *
     * @return \Components\Io_Buffer_Byte
     */
    public function appendBuffer(Io_Buffer $source_)
    {
      $remaining=min($this->remaining(), $source_->remaining());
      for($i=0; $i<$remaining; $i++)
        $this->append($source_->next());

      return $this;
    }

    /**
     * @see Components\Io_Buffer::flip()
     *
     * @return \Components\Io_Buffer_Byte
     */
    public function flip()
    {
      $this->m_limit=$this->m_position;
      $this->m_position=0;

      return $this;
    }

    /**
     * @see Components\Io_Buffer::position()
     */
    public function position($position_=null)
    {
      if(null===$position_)
        return $this->m_position;

      if($this->m_limit<$position_)
        throw new Exception_IllegalState('io/buffer/byte', 'Given position overlaps buffer\'s boundaries');

      return $this->m_position=$position_;
    }

    /**
     * @see Components\Io_Buffer::limit()
     */
    public function limit($limit_=null)
    {
      if(null===$limit_)
        return $this->m_limit;

      return $this->m_limit=$limit_;;
    }

    /**
     * @see Components\Io_Buffer::hasRemaining()
     */
    public function hasRemaining()
    {
      return $this->m_limit>$this->m_position;
    }

    /**
     * @see Components\Io_Buffer::remaining()
     */
    public function remaining()
    {
      return $this->m_limit-$this->m_position;
    }

    /**
     * @see Components\Io_Buffer::rewind()
     *
     * @return \Components\Io_Buffer_Byte
     */
    public function rewind()
    {
      $this->m_position=0;

      return $this;
    }

    /**
     * @see Components\Io_Buffer::clear()
     *
     * @return \Components\Io_Buffer_Byte
     */
    public function clear()
    {
      $this->m_position=0;
      $this->m_limit=$this->m_capacity;
      $this->m_buffer=array();

      return $this;
    }

    /**
     * @see Components\Io_Buffer::mark()
     *
     * @return \Components\Io_Buffer_Byte
     */
    public function mark()
    {
      $this->m_mark=$this->m_position;

      return $this;
    }

    /**
     * @see Components\Io_Buffer::reset()
     *
     * @return \Components\Io_Buffer_Byte
     */
    public function reset()
    {
      $this->m_position=$this->m_mark;

      return $this;
    }

    /**
     * @see Components\Io_Buffer::isArray()
     */
    public function isArray()
    {
      return true;
    }

    /**
     * @see Components\Io_Buffer::arrayValue()
     */
    public function arrayValue()
    {
      return $this->m_buffer;
    }

    /**
     * @see Components\Cloneable::__clone()
     *
     * @return \Components\Io_Buffer_Byte
     */
    public function __clone()
    {
      $buffer=new self($this->m_capacity);
      $buffer->m_buffer=&$this->m_buffer;
      $buffer->m_position=$this->m_position;
      $buffer->m_limit=$this->m_limit;
      $buffer->m_mark=$this->m_mark;

      return $buffer;
    }

    /**
     * @see Components\Object::hashCode()
     */
    public function hashCode()
    {
      $hash=0;
      foreach($this->m_buffer as $value)
        $hash=31*$hash+$value;

      return $hash;
    }

    /**
     * @see Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see Components\Object::__toString()
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


    // IMPLEMENTATION
    /**
     * @var integer|array
     */
    protected $m_buffer=array();
    /**
     * @var integer
     */
    protected $m_position=0;
    /**
     * @var integer
     */
    protected $m_mark=0;
    /**
     * @var integer
     */
    protected $m_limit;
    /**
     * @var integer
     */
    protected $m_capacity;
    //--------------------------------------------------------------------------
  }
?>
