<?php

class data_field_concepthierarchy extends data_field_base
{
    var $type = 'concepthierarchy'; // Se define el tipo del nuevo campo que se está creando.

    public function supports_preview(): bool
    {
        return true;
    }

    public function get_data_content_preview(int $recordid): stdClass
    {

        $previewContent = get_string('previewContent', 'datafield_concepthierarchy');

        return (object)[
            'id' => 0,
            'fieldid' => $this->field->id,
            'recordid' => $recordid,
            'content' => $previewContent, // Aquí va el contenido que deseo mostrar en la vista previa.
            'content1' => null,
            'content2' => null,
            'content3' => null,
            'content4' => null,
        ];
    }

    // En esta función se define cómo se va a ver y qué elementos contendrá el formulario cuando se agregue o edite una nueva entrada.
    function define_field_add(&$mform)
    {
        // Añade un campo de tipo texto con id parentName al formulario.
        $mform->addElement('text', 'parentName', get_string('parentFieldLabel', 'datafield_concepthierarchy'));
        $mform->setType('parentName', PARAM_TEXT); // Esta línea establece el tipo de datos esperado.
    }

    // Esta función se llama para actualizar los campos de la entrada.
    function update_content($recordid, $value, $name = '')
    {
        global $DB;

        // $value contiene el nombre del concepto padre ingresado.
        $value = trim($value); // Limpia el valor para eliminar espacios en blanco innecesarios.

        if (!empty($value)) {

            // Intenta obtener el ID del campo 'Concept' o 'Concepto'. Para saber si existe el campo en la Base de datos.
            $fieldConceptId = $DB->get_field('data_fields', 'id', ['dataid' => $this->field->dataid, 'name' => 'Concept'], IGNORE_MISSING);
            $fieldConceptoId = $DB->get_field('data_fields', 'id', ['dataid' => $this->field->dataid, 'name' => 'Concepto'], IGNORE_MISSING);

            // Comprueba cual de los dos es el que existe.
            $fieldId = $fieldConceptId ? $fieldConceptId : $fieldConceptoId;

            if ($fieldId) {
                // Construye la consulta usando sql_compare_text que cuenta cuántos registros en la tabla data_content tienen el fieldid anterior.
                $sql = "SELECT COUNT('x') FROM {data_content} WHERE fieldid = ? AND " . $DB->sql_compare_text('content') . " = ?";
                $params = [$fieldId, $value];
                $count = $DB->count_records_sql($sql, $params);

                if ($count < 1) {
                    throw new moodle_exception(get_string('error_nonexistent_parent', 'datafield_concepthierarchy'));
                }
            } else {
                // Si no se encuentra ningún campo 'Concept' o 'Concepto', lanza una excepción.
                throw new moodle_exception(get_string('error_nonexistent_field', 'datafield_concepthierarchy'));
            }
        }


        // Tercero, si el padre existe o no se ha introducido un padre, se procede con la actualización o inserción del contenido.
        $content = new stdClass();
        $content->recordid = $recordid;
        $content->fieldid = $this->field->id; // Es el id de la base de datos.
        $content->content = $value; // El valor a insertar.

        // Busca si ya existe contenido para este campo y registro.
        if ($existingcontent = $DB->get_record('data_content', ['fieldid' => $this->field->id, 'recordid' => $recordid])) {
            // Si existe, actualiza.
            $content->id = $existingcontent->id;
            $DB->update_record('data_content', $content);
        } else {
            // Si no existe, inserta uno nuevo.
            $DB->insert_record('data_content', $content);
        }

        return true; // Devuelve true si el proceso es exitoso. 
    }

    // Esta función se utiliza para la visualización del campo cuando se navega por las entradas.
    function display_browse_field($recordid, $template)
    {
        // Obtenemos el contenido del campo actual. 
        $content = $this->get_data_content($recordid);
        if (!$content || $content->content === '') {
            return ''; // En caso de que esté vacío no mostramos nada.
        }
        // Mostramos el nombre del concepto padre.
        return format_string($content->content);
    }

    // Esta función genera el HTML necesario para mostrar un campo de búsqueda para el campo que se está desarrollando.
    function display_search_field($value = '')
    {
        return '<label class="accesshide" for="f_' . $this->field->id . '">' . $this->field->name . '</label>' .
            '<input type="text" class="form-control" size="16" id="f_' . $this->field->id . '" ' .
            'name="f_' . $this->field->id . '" value="' . s($value) . '" />';
    }

    // Esta función es utilizada para procesar y obtener el valor del campo de búsqueda cuando se realiza una búsqueda.
    public function parse_search_field($defaults = null)
    {
        $param = 'f_' . $this->field->id; // Se construye el nombre del parámetro.

        if (empty($defaults[$param])) { // Se comprueba si existe un valor predeterminado para este campo.
            $defaults = array($param => ''); // Si no existe, se establece un valor vacio predeterminado.
        }

        // "optional_param" es una función que se utiliza para obtener el valor del parámetro de búsqueda desde la solicitud HTTP.
        // El primer argumento es el nombre del parámetro que se busca
        // El segundo argumento es el valor predeterminado para el parámetro, que se obtiene del array $defaults.
        return optional_param($param, $defaults[$param], PARAM_NOTAGS);
    }

    function generate_sql($tablealias, $value)
    {
        global $DB;

        static $i = 0; // Se va a utilizar para generar identificadores únicos con cada llamada a la función.
        $i++;
        $name = "df_parentConcept_$i";

        // Devuelve un array con dos elementos, la clausula Where y el array asociativo de los parametros nombre-valor.
        return array(" ({$tablealias}.fieldid = {$this->field->id} AND " . $DB->sql_like("{$tablealias}.content", ":$name", false) . ") ", array($name => "%$value%"));
    }

    /**
     * Comprueba si un campo de un formulario de adición está vacío.
     *
     * @param mixed $value
     * @param mixed $name
     * @return bool
     */
    function notemptyfield($value, $name)
    {
        return strval($value) !== '';
    }

    /**
     * Devuelve la configuración del plugin para funciones externas, .
     * Permite que las configuraciones del plugin sean accesibles desde fuera,
     * lo cual es útil para la integración con otros sistemas o para funciones avanzadas de Moodle.
     * @return array the list of config parameters
     * @since Moodle 3.3
     */
    public function get_config_for_external()
    {
        // Return all the config parameters.
        $configs = [];
        for ($i = 1; $i <= 10; $i++) {
            $configs["param$i"] = $this->field->{"param$i"};
        }
        return $configs;
    }
}
