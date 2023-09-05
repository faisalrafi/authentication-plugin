<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Admin settings and defaults.
 *
 * @package    auth_sentry
 * @copyright  2023 BrainStation-23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_description('auth_sentry/adminimage',
        get_string('setting:adminimagepage', 'auth_sentry'),
        '<a
            class="mb-5" style="font-size: 20px;"
            href=" ' . new moodle_url('/auth/sentry/userlist.php') .'">'.
        get_string('setting:userslist', 'auth_sentry') .
        '</a>'),
        'admin image');

    $settings->add(new admin_setting_configcheckbox('auth_sentry/enable_custom_button',
        get_string('enable_custom_button', 'auth_sentry'),
        get_string('enable_custom_button_description', 'auth_sentry'), 0));

    $settings->add(new admin_setting_configtext('auth_sentry/bsapi',
        get_string('setting:bs_api', 'auth_sentry'),
        get_string('setting:bs_apidesc', 'auth_sentry'),
        ''));

    $settings->add(new admin_setting_configpasswordunmask('auth_sentry/bs_api_key',
        get_string('setting:bs_api_key', 'auth_sentry'),
        get_string('setting:bs_api_keydesc', 'auth_sentry'),
        ''));

    $settings->add(new admin_setting_configtext('auth_sentry/threshold',
        get_string('threshold', 'auth_sentry'),
        get_string('thresholdlongtext', 'auth_sentry'),
        '0.68'));

    // Introductory explanation.
//    $settings->add(new admin_setting_heading('auth_sentry/pluginname', '',
//        new lang_string('auth_nonedescription', 'auth_sentry')));

    // Display locking / mapping of profile fields.
    $authplugin = get_auth_plugin('sentry');
    display_auth_lock_options($settings, $authplugin->authtype, $authplugin->userfields,
        get_string('auth_fieldlocks_help', 'auth'), false, false);
}


