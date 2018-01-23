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
namespace Chaplin\Module\Api\Controller;

use Chaplin\Controller\Action\Api as ApiController;
use Chaplin\Gateway;
use Exception;

class ServicesController extends ApiController
{
    // The OEmbed Service
    public function getOembed()
    {
        // currently we only support video oembeds


        $strVideoURL = $this->_request->getQuery('url');
        $vhost = getenv("SCHEME")."://".getenv("VHOST");

        if (false === strpos($strVideoURL, $vhost)) {
            $arrOut = ['error' => 'Video not in current server.'];
            $this->getResponse()->setHttpResponseCode(404);
            return $this->_forceAPI($arrOut);
        }

        $arrMatches = [];
        $strVideoId = preg_match(
            '#\/video\/watch\/id\/(.*)$#',
            $strVideoURL,
            $arrMatches
        );

        if (empty($arrMatches)
            || !isset($arrMatches[1])
            || !is_string($arrMatches[1])
        ) {
            $arrOut = ['error' => 'Request shows not a video resource'];
            $this->getResponse()->setHttpResponseCode(404);
            return $this->_forceAPI($arrOut);
        }

        $strVideoId = $arrMatches[1];

        try {
            $video = Gateway::getVideo()
             ->getByVideoId($strVideoId);
        } catch (Exception $e) {
            $arrOut = ['error' => 'Video not found: ',$strVideoId];
            $this->getResponse()->setHttpResponseCode(404);
            return $this->_forceAPI($arrOut);
        }

        if ($video->getPrivacy()->isPrivate()) {
            $arrOut = ['error' => 'Private Video'];
            $this->getResponse()->setHttpResponseCode(401);
            return $this->_forceAPI($arrOut);
        }

        $arrOut = [
         'version' => '1.0',
         'type' => 'video',
         'width' => 640,
         'height' => 480,
         'title' => $video->getTitle(),
         'html' => '<video src="'.$vhost . $video->getFilename().'" controls></video>',
         'author_name' => $video->getUsername(),
         'author_url' => $vhost.'/user/'.$video->getUsername(),
         'provider_name' => 'Project Chaplin',
         'provider_url' =>  $vhost
        ];

        return $this->_forceAPI($arrOut);
    }
}
