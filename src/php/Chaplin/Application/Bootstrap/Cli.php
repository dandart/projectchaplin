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
namespace Chaplin\Application\Bootstrap;

use Exception;
use Zend_Application_Bootstrap_Bootstrap as ZendBootstrap;
use Zend_Mail as ZendMail;
use Zend_Mail_Transport_Smtp as TransportSmtp;

class Cli extends ZendBootstrap
{
    // @codingStandardsIgnoreStart
    protected function _initSmtp()
    {
        // @codingStandardsIgnoreEnd
        $transport = new TransportSmtp(
            getenv("SMTP_HOST"),
            [
                "port"      => getenv("SMTP_PORT"),
                "username"  => getenv("SMTP_USER"),
                "password"  => getenv("SMTP_PASSWORD"),
                "auth"      => "login",
                "ssl"       => "tls"//"tls"       => getenv("SMTP_USE_TLS")
            ]
        );
        ZendMail::setDefaultTransport($transport);
    }

    // @codingStandardsIgnoreStart
    protected function _bootstrap($resource = null)
    {
        // @codingStandardsIgnoreEnd
        try {
            parent::_bootstrap($resource);
        } catch (Exception $e) {
            echo $e->getMessage();
            flush();
        }
    }
    public function run()
    {
        try {
            parent::run();
        } catch (Exception $e) {
            echo $e->getMessage();
            ob_flush();
            flush();
        }
    }
}
