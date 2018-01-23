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

namespace Chaplin\Cache\Http;

use Chaplin\Cache\CacheAbstract;
use Chaplin\Http\HttpInterface;
use Chaplin\Log;
use Zend_Cache_Core as Cache;
use Zend_Log as ZendLog;

class Client extends CacheAbstract implements HttpInterface
{
    private $_objHttpClient;

    public function __construct(HttpInterface $objHttpClient, Cache $cacheHttpClient = null)
    {
        $this->_objHttpClient = $objHttpClient;
        $this->setCache($cacheHttpClient);
    }

    public function getPageBody($strURL, $intLogPriority = ZendLog::ERR)
    {
        $cacheKey   = $this->_getCacheKey(__METHOD__, $strURL);
        if (false === ($response = $this->_cacheLoadKey($cacheKey))) {
            $response   = $this->_objHttpClient->getPageBody($strURL, $intLogPriority);
            $this->_cacheSaveKey($cacheKey, $response);
        }
        return $response;
    }

    public function getObject($strURL, $intLogPriority = ZendLog::ERR)
    {
        $cacheKey   = $this->_getCacheKey(__METHOD__, $strURL);
        if (false === ($response = $this->_cacheLoadKey($cacheKey))) {
            $response   = $this->_objHttpClient->getObject($strURL, $intLogPriority);
            $this->_cacheSaveKey($cacheKey, $response);
        }
        return $response;
    }

    public function scrapeXPath($strURL, $strXPath)
    {
        $cacheKey   = $this->_getCacheKey(__METHOD__, $strURL . 'X' . $strXPath);
        if (false === ($response = $this->_cacheLoadKey($cacheKey))) {
            $response   = $this->_objHttpClient->scrapeXPath($strURL, $strXPath);
            $this->_cacheSaveKey($cacheKey, $response);
        }
        return $response;
    }

    public function getHttpResponse($strURL, $intLogPriority = ZendLog::ERR, $bCache = true)
    {
        if (!$bCache) {
            return $this->_objHttpClient->getHttpResponse($strURL, $intLogPriority);
        }

        Log::getInstance()->log('Cache: trying to load ('.$strURL.')', $intLogPriority);
        $cacheKey = $this->_getCacheKey(__METHOD__, $strURL);
        if (false === ($response = $this->_cacheLoadKey($cacheKey))) {
            $response = $this->_objHttpClient->getHttpResponse($strURL, $intLogPriority);
            if (200 == $response->getStatus()) {
                $this->_cacheSaveKey($cacheKey, $response);
            }
        }
        Log::getInstance()->log('Cache: retrieved ('.$response->getBody().')', $intLogPriority);
        return $response;
    }

    public function parseRawXPath($strData, $strXPath)
    {
        return $this->_objHttpClient->parseRawXPath($strData, $strXPath);
    }

    public function parseRawHtmlXPath($strData, $strXPath, $strURL = null)
    {
        return $this->_objHttpClient->parseRawHtmlXPath($strData, $strXPath, $strURL);
    }
}
