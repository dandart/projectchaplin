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

namespace Chaplin\Service\YouTube;

use Google_Client;
use Google_Service_YouTube;
use Chaplin\Model\User;
use Chaplin\Model\Video;
use Chaplin\Model\Video\Licence;
use Chaplin\Model\Video\Youtube;
use Chaplin\Gateway;

class API
{
    const LOCATION = 'youtube-dl';

    public function search($strSearchTerm, $page = 0, $intLimit = 50)
    {
        $client = new Google_Client();
        $client->setDeveloperKey(getenv("YOUTUBE_API_TOKEN"));

        $youtube = new Google_Service_YouTube($client);

        return $youtube->search->listSearch(
            'id,snippet',
            [
            'q' => $strSearchTerm,
            //'pageToken' => $page,
            'maxResults' => $intLimit,
            'order' => 'relevance',
            'videoLicense' => 'creativeCommon',
            'type' => 'video',
            ]
        );
    }

    public function getVideoById($strId)
    {
        $client = new Google_Client();
        $client->setDeveloperKey(getenv("YOUTUBE_API_TOKEN"));

        $youtube = new Google_Service_YouTube($client);

        $list = $youtube->videos->listVideos(
            'id,snippet',
            [
            'id' => $strId
            ]
        );

        return 0 < $list->pageInfo->totalResults ? $list->items[0] : null;
    }

    public function getUserProfile($strSearchTerm)
    {
        $client = new Google_Client();
        $client->setDeveloperKey(getenv("YOUTUBE_API_TOKEN"));

        $youtube = new Google_Service_YouTube($client);

        $list = $youtube->channels->listChannels(
            'id,snippet',
            [
            'forUsername' => $strSearchTerm
            ]
        );

        return 0 < $list->pageInfo->totalResults ? $list->items[0] : null;
    }

    public function getUserUploads($strChannelId, $strPageToken = null)
    {
        $client = new Google_Client();
        $client->setDeveloperKey(getenv("YOUTUBE_API_TOKEN"));

        $youtube = new Google_Service_YouTube($client);

        $arrRequest = [
            'channelId' => $strChannelId,
            'maxResults' => 50,
            'order' => 'relevance',
            'videoLicense' => 'creativeCommon',
            'type' => 'video',
        ];

        if ($strPageToken) {
            $arrRequest['pageToken'] = $strPageToken;
        }

        return $youtube->search->listSearch('id,snippet', $arrRequest);
    }

    public function getDownloadURL($strURL)
    {
        $strCommandLine = self::LOCATION.
            ' -4 --prefer-free-formats -g -- '.
            escapeshellarg($strURL);
        return system($strCommandLine);
    }

    public function downloadVideo($strURL, $strPathToSave, &$ret)
    {
        if ("true" === getenv("NO_UPLOADS")) {
            return;
        }

        $strCommandLine = self::LOCATION.
            " -4 --format=webm -o ".
            escapeshellarg($strPathToSave."/%(id)s.%(ext)s").
            " -- ".escapeshellarg($strURL).
            ' 2>&1';
        echo $strCommandLine.PHP_EOL;
        ob_flush();
        flush();
        return system($strCommandLine, $ret);
    }

    public function downloadThumbnail($strVideoId, $strPathToSave)
    {
        if ("true" === getenv("NO_UPLOADS")) {
            return;
        }

        $entryVideo = $this->getVideoById($strVideoId);

        $strFilename = $strPathToSave.'/'.$entryVideo->id.'.webm.png';

        $strURL =  $entryVideo->getSnippet()->thumbnails->high->url;

        $strImage = file_get_contents($strURL);
        file_put_contents($strFilename, $strImage);

        return '/uploads/'.basename($strFilename);
    }

    public function importVideo(User $modelUser, $strURL)
    {
        if ("true" === getenv("NO_UPLOADS")) {
            return;
        }

        $strVideoId = $strURL;

        $entryVideo = $this->getVideoById($strVideoId);

        $strTitle = $entryVideo->getSnippet()->title;
        $strDescription = $entryVideo->getSnippet()->description;

        $strRelaFile = '/uploads/'.$strVideoId.'.webm';
        $strThumbnail = $this->downloadThumbnail(
            $strVideoId,
            getenv("UPLOADS_PATH")
        );

        $modelVideo = Video::create(
            $modelUser,
            $strRelaFile,
            $strThumbnail,
            $strTitle,
            $strDescription,
            '', // uploader
            $strURL
        );
        // All YouTube imports are CC-BY
        $modelVideo->setLicence(Licence::ID_CCBY);
        $modelVideo->save();

        // msg
        $modelYoutube = Youtube::create(
            $modelVideo,
            $strVideoId
        );
        Gateway::getInstance()->getVideo_Youtube()->save($modelYoutube);

        return $modelVideo;
    }
}
