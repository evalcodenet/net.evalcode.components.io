<?php


namespace Components;


  /**
   * Io_Buffer
   *
   * @package net.evalcode.components
   * @subpackage io
   *
   * @author evalcode.net
   */
  interface Io_Buffer extends Object, Cloneable
  {
    // CONSTRUCTION
    /**
     * @param integer $capacity_ Buffer capacity (maximum size).
     */
    function __construct($capacity_);
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * Returns buffer capacity as defined on construction.
     *
     * @return integer
     */
    function capacity();

    /**
     * Reads this buffer into passed target buffer.
     *
     * @param Io_Buffer $target_
     *
     * @return integer
     */
    function read(Io_Buffer $target_);

    /**
     * Iterate through buffer contents.
     *
     * Returns value at current position and increments the internal
     * position pointer to iterate through the buffer.
     * Returns 'null' if there is no value at current position.
     *
     * @return mixed
     */
    function next();

    /**
     * Returns value at given position or at current position if the passed
     * parameter is 'null'.
     *
     * @param integer $position_
     *
     * @return mixed
     */
    function get($position_=null);

    /**
     * Appends given value to this buffer at its current position and
     * increments the position by length of appended value.
     *
     * @param mixed $value_
     *
     * @return Io_Buffer
     */
    function append($value_);

    /**
     * Appends given buffer to this buffer at its current position until
     * this or the target buffer's limit is reached.
     * Internal position will be incremented by amount of values appended
     * in this operation.
     *
     * @param Io_Buffer $source_
     *
     * @return Io_Buffer
     */
    function appendBuffer(Io_Buffer $source_);

    /**
     * Sets this buffer's limit to its current position and resets the position
     * to zero.
     *
     * @return Io_Buffer
     */
    function flip();

    /**
     * Returns this buffer's internal position or sets it to the given one.
     *
     * @param integer $position_
     *
     * @return integer
     *
     * @throws Exception_IllegalState
     */
    function position($position_=null);

    /**
     * Returns this buffer's limit or sets it to the given one.
     *
     * @param integer $limit_
     *
     * @return integer
     *
     * @throws Exception_IllegalState
     */
    function limit($limit_=null);

    /**
     * Returns 'true' if the buffer's limit is not reached yet,
     * otherwise returns 'false'.
     *
     * @return boolean
     *
     * TODO Implement Iterable / Iterator, check current pointer impl.
     * (should be pre-incrementing, not post-incrementing).
     */
    function hasRemaining();

    /**
     * Returns remaining free slots until this buffers limit is reached.
     *
     * @return integer
     */
    function remaining();

    /**
     * Resets this buffers internal position to zero.
     *
     * @return Io_Buffer
     */
    function rewind();

    /**
     * Removes this buffers contents, resets its internal position to zero
     * and its limit to its capacity.
     *
     * @return Io_Buffer
     */
    function clear();

    /**
     * Sets a mark at current position.
     *
     * @return Io_Buffer
     */
    function mark();

    /**
     * Resets this buffers internal position to the last mark ot to
     * zero if no mark is set.
     *
     * @return Io_Buffer
     */
    function reset();

    /**
     * Returns 'true' if this buffer is internally backed by an array,
     * otherwise returns 'false'.
     *
     * @return boolean
     */
    function isArray();

    /**
     * Returns the array internally used as storage. Returns 'null' if
     * this buffer is not backed my an array.
     *
     * @return array|null
     */
    function arrayValue();

    /**
     * Clones this buffer.
     *
     * <p>
     * Cloned buffers share the same internal buffer, meaning
     * modifications to a cloned buffer will be visible in the
     * original buffer as well.
     * </p>
     *
     * <p>
     * Though each cloned instance holds its own internal position,
     * limit and marks, therefore can be searched independently.
     * </p>
     *
     * @see Cloneable::__clone()
     */
    function __clone();
    //--------------------------------------------------------------------------
  }
?>
