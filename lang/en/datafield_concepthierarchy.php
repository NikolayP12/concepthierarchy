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
 * Plugin strings are defined here.
 * 
 * @package     datafield
 * @subpackage  concepthierarchy
 * @copyright   2024 Nikolay <nikolaypn2002@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$string['pluginname'] = 'Parent Concept';
$string['fieldtypelabel'] = 'Parent Concept';
$string['privacy:metadata'] = 'The Parent Concept field component doesn\'t store any personal data; it uses tables defined in mod_data.';
$string['previewContent'] = 'Name of the parent concept:';
$string['parentFieldLabel'] = 'Parent Concept';

// Error strings.
$string['error_nonexistent_field'] = 'There is no field in the entry called "Concept" or "Concept".';
$string['error_nonexistent_parent'] = 'The value specified as parent does not exist.';
