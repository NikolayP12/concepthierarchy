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
 * Implementation for the new datafield.
 *
 * @package    datafield
 * @copyright  2018 Carlos Escobedo <carlos@moodle.com>
 * @copyright  2024 Nikolay <nikolaypn2002@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Implements a custom field type for hierarchical concepts in the Database activity module.
 *
 * This class extends the base data field class, providing custom behavior for
 * handling, displaying, and searching hierarchical concept data within a Database activity.
 */

class data_field_concepthierarchy extends data_field_base
{
    var $type = 'concepthierarchy'; // Defines the type of the field being created.

    /**
     * Indicates support for preview mode.
     *
     * @return bool Always returns true, indicating this field type supports previews.
     */
    public function supports_preview(): bool
    {
        return true;
    }

    /**
     * Generates a preview of the data content.
     *
     * This method is used to provide a standardized preview of the content for this field type.
     *
     * @param int $recordid The ID of the database record.
     * @return stdClass An object containing preview content and related data.
     */
    public function get_data_content_preview(int $recordid): stdClass
    {

        $previewContent = get_string('previewContent', 'datafield_concepthierarchy');

        return (object)[
            'id' => 0,
            'fieldid' => $this->field->id,
            'recordid' => $recordid,
            'content' => $previewContent,
            'content1' => null,
            'content2' => null,
            'content3' => null,
            'content4' => null,
        ];
    }

    /**
     * Defines the structure of the form for adding or editing entries.
     *
     * @param object $mform The Moodle form object.
     */
    function define_field_add(&$mform)
    {
        // Adds a text input element to the form for entering the parent concept name.
        $mform->addElement('text', 'parentName', get_string('parentFieldLabel', 'datafield_concepthierarchy'));
        $mform->setType('parentName', PARAM_TEXT);
    }

    /**
     * Updates the content of the database field.
     *
     * This method handles the updating or insertion of the concept's parent name
     * into the database.
     *
     * @param int $recordid The ID of the record being updated.
     * @param mixed $value The value to be inserted or updated.
     * @param string $name The name of the field (optional).
     * @return bool Returns true on success.
     */
    function update_content($recordid, $value, $name = '')
    {
        global $DB;
        $errorOcurred = false;

        // $value contains the name of the parent concept entered.
        $value = trim($value);
        try {
            if (!empty($value)) {

                // Try to get the ID of the 'Concept' field. To find out if the field exists in the database.
                $fieldConceptId = $DB->get_field('data_fields', 'id', ['dataid' => $this->field->dataid, 'name' => 'Concept'], IGNORE_MISSING);
                $fieldConceptoId = $DB->get_field('data_fields', 'id', ['dataid' => $this->field->dataid, 'name' => 'Concepto'], IGNORE_MISSING);

                // Check wich one exists.
                $fieldId = $fieldConceptId ? $fieldConceptId : $fieldConceptoId;

                if ($fieldId) {
                    // Constructs a query to count matching records in the database for the specified concept name.
                    $sql = "SELECT COUNT('x') FROM {data_content} WHERE fieldid = ? AND " . $DB->sql_compare_text('content') . " = ?";
                    $params = [$fieldId, $value];
                    $count = $DB->count_records_sql($sql, $params);

                    // Checks if the parent concept exists based on the count result.
                    if ($count < 1) {
                        throw new moodle_exception('error_nonexistent_parent', 'datafield_concepthierarchy');
                        // Apunte: Eliminar el error que sale del stack (pila).
                    }
                } else {
                    // Throws an exception if no 'Concept' or 'Concepto' field was found.
                    throw new moodle_exception('error_nonexistent_field', 'datafield_concepthierarchy');
                }
            }
        } catch (moodle_exception $e) {
            \core\notification::error(get_string($e->errorcode, $e->module));
            $errorOcurred = true;
        }

        // If an error occurred, it terminates the execution of the method by returning false.
        if ($errorOcurred) {
            return false;
        }

        // Proceeds with updating or inserting the content for this field in the database.
        $content = new stdClass();
        $content->recordid = $recordid;
        $content->fieldid = $this->field->id;
        $content->content = $value;

        // Checks if content already exists for this field and record.
        if ($existingcontent = $DB->get_record('data_content', ['fieldid' => $this->field->id, 'recordid' => $recordid])) {
            // Updates existing content.
            $content->id = $existingcontent->id;
            $DB->update_record('data_content', $content);
        } else {
            // Inserts new content.
            $DB->insert_record('data_content', $content);
        }

        return true;
    }

    /**
     * Displays the field's content when browsing entries.
     *
     * @param int $recordid The ID of the record to display.
     * @param string $template The template used for rendering the content (optional).
     * @return string The rendered content for this field.
     */
    function display_browse_field($recordid, $template)
    {
        // Retrieves the content for the current field and record. 
        $content = $this->get_data_content($recordid);
        if (!$content || $content->content === '') {
            return ''; // Returns an empty string if there's no content to display.
        }
        // Returns the formatted content.
        return format_string($content->content);
    }

    /**
     * Generates the HTML necessary to display a search field for this data field.
     *
     * @param string $value The default value to populate in the search field (optional).
     * @return string HTML markup for the search field.
     */
    function display_search_field($value = '')
    {
        return '<label class="accesshide" for="f_' . $this->field->id . '">' . $this->field->name . '</label>' .
            '<input type="text" class="form-control" size="16" id="f_' . $this->field->id . '" ' .
            'name="f_' . $this->field->id . '" value="' . s($value) . '" />';
    }

    /**
     * Parses the search field value from the submitted form.
     *
     * @param array|null $defaults Default values for search fields (optional).
     * @return mixed The value of the search parameter.
     */
    public function parse_search_field($defaults = null)
    {
        $param = 'f_' . $this->field->id; // Constructs the parameter name.

        if (empty($defaults[$param])) { // It is checked if there is a default value for this field.
            $defaults = array($param => ''); // Sets a default empty value if not specified.
        }

        // Returns the parameter value, using a default if not provided.
        return optional_param($param, $defaults[$param], PARAM_NOTAGS);
    }

    /**
     * Generates the SQL condition for searching based on this field.
     *
     * @param string $tablealias The alias for the database table.
     * @param mixed $value The value to search for.
     * @return array An array containing the SQL condition and parameters.
     */
    function generate_sql($tablealias, $value)
    {
        global $DB;

        static $i = 0; // Se va a utilizar para generar identificadores únicos con cada llamada a la función.
        $i++;
        $name = "df_parentConcept_$i";

        // Returns an array with the SQL WHERE clause and named parameters for the search condition.
        return array(" ({$tablealias}.fieldid = {$this->field->id} AND " . $DB->sql_like("{$tablealias}.content", ":$name", false) . ") ", array($name => "%$value%"));
    }

    /**
     * Checks if a field in the add entry form is not empty.
     *
     * @param mixed $value The value of the field.
     * @param mixed $name The name of the field (optional).
     * @return bool True if the field is not empty.
     */
    function notemptyfield($value, $name)
    {
        return strval($value) !== '';
    }

    /**
     * Returns plugin settings for external access, such as for web services.
     *
     * This function allows external systems to access configuration settings of this plugin,
     * which can be useful for integrations or advanced features.
     *
     * @return array The list of configuration parameters.
     */
    public function get_config_for_external()
    {
        $configs = [];
        for ($i = 1; $i <= 10; $i++) {
            $configs["param$i"] = $this->field->{"param$i"};
        }
        return $configs;
    }
}
