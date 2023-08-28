<?php

// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Web service description
 *
 * @package    auth_sentry
 * @copyright  2023, Brain Station 23
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'auth_sentry_by_face_image_api' => array(
        'classname'   => 'auth_sentry_external',
        'methodname'  => 'get_user_image',
        'classpath'   => 'auth/sentry/externallib.php',
        'description' => 'Returns the image saved for login',
        'type'        => 'write',
        'ajax'        => true,
        'loginrequired' => false,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
    'auth_sentry_by_face_recognition_api' => array(
        'classname'   => 'auth_sentry_external',
        'methodname'  => 'face_recognition_api',
        'classpath'   => 'auth/sentry/externallib.php',
        'description' => 'Calling the api for face recognition',
        'type'        => 'write',
        'ajax'        => true,
        'loginrequired' => false,
        'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
);



