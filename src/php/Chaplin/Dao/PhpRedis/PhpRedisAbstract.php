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

namespace Chaplin\Dao\PhpRedis;

use Chaplin\Dao\DaoInterface;
use Zend_Registry;
use Redis;

abstract class PhpRedisAbstract implements DaoInterface
{
    const DEFAULT_REGISTRY_KEY = 'RedisRegistry';

    /**
     * Redis instance
    **/
    private $_redis;

    /**
     * Gets the Redis instance
     *
     * @return Redis
     * @author Dan Dart <chaplin@dandart.co.uk>
    **/
    protected function _getRedis()
    {
        if (is_null($this->_redis)) {
            $this->_redis = Zend_Registry::get(self::DEFAULT_REGISTRY_KEY);
        }

        return $this->_redis;
    }

    /**
     * Injects a redis instance for testing
     *
     * @param  Redis $redis
     * @return void
     * @author Dan Dart <chaplin@dandart.co.uk>
    **/
    public function inject(Redis $redis)
    {
        $this->_redis = $redis;
    }
}
