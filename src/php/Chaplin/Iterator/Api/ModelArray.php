<?php
/**
 * This file is part of Project Chaplin.
 *
 * Project Chaplin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Project Chaplin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Project Chaplin. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   ProjectChaplin
 * @author    Dan Dart <chaplin@dandart.co.uk>
 * @copyright 2012-2018 Project Chaplin
 * @license   http://www.gnu.org/licenses/agpl-3.0.html GNU AGPL 3.0
 * @version   GIT: $Id$
 * @link      https://github.com/danwdart/projectchaplin
**/

namespace Chaplin\Iterator\Api;

use Chaplin\Iterator\IteratorInterface;
use BadMethodCallException;

class ModelArray implements IteratorInterface
{
    private $_daoInterface;
    private $_bEmpty        = false;

    private $_intOffset     = 0;
    private $_intStartRow   = 0;
    private $_intReturnRows;
    private $_strClass;
    private $_arrRows;
    private $_strURLPrefix;

    public function __construct($strClass, $strURLPrefix, array $arrRows)
    {
        $this->_arrRows = $arrRows;
        $this->_strClass = $strClass;
        $this->_strURLPrefix = $strURLPrefix;
    }
    public function isEmpty()
    {
        if (0 == count($this->_arrRows)) {
            $this->_bEmpty = true;
        }
        return $this->_bEmpty;
    }
    public function count()
    {
        return count($this->_arrRows);
    }
    public function current()
    {
        $arrCurrentItem = $this->_arrRows[$this->_intOffset];
        $strClass = $this->_strClass;
        return $strClass::createFromAPIResponse($arrCurrentItem, $this->_strURLPrefix);
    }
    function key()
    {
        return $this->_intOffset;
    }
    function next()
    {
        $this->_intOffset++;
    }
    function rewind()
    {
        $this->_intOffset = 0;
    }
    function valid()
    {
        return isset($this->_arrRows[$this->_intOffset]);
    }
    //Implements ArrayAccess
    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException(__METHOD__);
    }
    public function offsetExists($offset)
    {
        throw new BadMethodCallException(__METHOD__);
    }
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException(__METHOD__);
    }
    public function offsetGet($offset)
    {
        throw new BadMethodCallException(__METHOD__);
    }

    /*  Limits the number of rows to be returned in the cursor
     *  @param:     $intNoRows  = number of rows to return
     *  @return:    $this (this is a fluent interface)
     **/
    public function limit($intNoRows)
    {
        throw new BadMethodCallException(__METHOD__);
    }
    /**
     *  Skips the first  $intNoRows
     *
     *  @param:  $intNoRows  = number of rows to skip
     *  @return: $this (this is a fluent interface)
     **/
    public function skip($intNoRows)
    {
        throw new BadMethodCallException(__METHOD__);
    }
    /**
     *  Sorts the cursor
     *
     *  @param:  $arrColumns     Associative array of Key => value (1 = ASC, -1 = DESC)
     *  @return: $this (this is a fluent interface)
     **/
    public function sort(array $arrColumns = array())
    {
        throw new BadMethodCallException(__METHOD__);
    }

    //Implements SeekableIterator
    public function seek($strPosition)
    {
        throw new BadMethodCallException(__METHOD__);
    }

    public function toArray()
    {
        $arrOut = [];
        foreach ($this as $item) {
            $arrOut[] = $item->toArray();
        }
        return $arrOut;
    }
}
