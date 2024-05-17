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
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
$string['pluginname'] = 'Concepto Padre';
$string['fieldtypelabel'] = 'Concepto Padre';
$string['privacy:metadata'] = 'El componente de campo Parent Concept no almacena ningún dato personal; utiliza tablas definidas en mod_data.';
$string['previewContent'] = 'Nombre del concepto padre:';
$string['parentFieldLabel'] = 'Concepto de padre';

// Error strings.
$string['error_nonexistent_field'] = 'No hay ningún campo en las entradas llamado "Concepto" o "Concept".';
$string['error_nonexistent_parent'] = 'El valor especificado como padre no existe.';
