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

namespace Chaplin\Model;

use Chaplin\Model\Field\Hash;
use Chaplin\Model\User;
use Chaplin\Gateway;



class Channel extends Hash
{
    const FIELD_CHANNELID = 'ChannelId';
    const FIELD_FULLNAME = 'FullName';
    const FIELD_USERNAME = 'Username';

    protected $_arrFields = array(
        self::FIELD_CHANNELID => array('Class' => 'Chaplin\\Model\\Field\\FieldId'),
        self::FIELD_FULLNAME => array('Class' => 'Chaplin\\Model\\Field\\Field'),
        self::FIELD_USERNAME => array('Class' => 'Chaplin\\Model\\Field\\Field'),
    );

    public static function create($strChannelId, $strFullName, User $modelUser)
    {
        $modelChannel = new self();
        $modelChannel->_bIsNew = true;
        $modelChannel->_setField(self::FIELD_CHANNELID, $strChannelId);
        $modelChannel->_setField(self::FIELD_FULLNAME, $strFullName);
        $modelChannel->_setField(self::FIELD_USERNAME, $modelUser->getUsername());
        return $modelChannel;
    }

    public function getId()
    {
        return $this->_getField(self::FIELD_CHANNELID, null);
    }

    public function getChannelId()
    {
        return $this->_getField(self::FIELD_CHANNELID, null);
    }

    public function getFullName()
    {
        return $this->_getField(self::FIELD_FULLNAME, null);
    }

    public function getUser()
    {
        return Gateway::getUser()
            ->getByUsername(
                $this->_getField(self::FIELD_USERNAME, null)
            );
    }

    public function delete()
    {
        return Gateway::getInstance()->getChannel()->delete($this);
    }

    public function save()
    {
        return Gateway::getInstance()->getChannel()->save($this);
    }
}
