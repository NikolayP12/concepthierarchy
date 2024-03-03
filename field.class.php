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
        // El siguiente es un ejemplo genérico:
        $previewContent = 'Nombre del Concepto Padre: ';

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
        // Añade un campo de texto con id parentName al formulario.
        $mform->addElement('text', 'parentName', get_string('parentFieldLabel', 'datafield_concepthierarchy'));
        $mform->setType('parentName', PARAM_TEXT); // Esta línea establece el tipo de datos esperado.
    }

    // Esta función se llama para actualizar los campos de la entrada.
    function update_content($recordid, $value, $name = '')
    {
        global $DB;

        // $value contiene el nombre del concepto padre ingresado.
        $value = trim($value); // Limpia el valor para eliminar espacios en blanco innecesarios.

        // Primero, obtengo el id del campo 'Name' o 'Nombre', en funcion de cómo se haya llamado.
        $fieldName = $DB->get_record('data_fields', array('dataid' => $this->field->dataid, 'name' => 'Name'), '*', IGNORE_MISSING);
        $fieldNombre = $DB->get_record('data_fields', array('dataid' => $this->field->dataid, 'name' => 'Nombre'), '*', IGNORE_MISSING);

        if (!$fieldName && !$fieldNombre) {
            // Si el campo 'Name' o 'Nombre' no se encuentra, lanzamos una excepción.
            throw new moodle_exception(get_string('error_nonexistent_field', 'datafield_concepthierarchy'));
        }

        // Segundo, compruebo si existe el padre introducido.
        if ($fieldName) {
            $parentExists = $DB->record_exists('data_content', array('fieldid' => $fieldName->id, 'content' => $value));
            if (!$parentExists) {
                throw new moodle_exception(get_string('error_nonexistent_parent', 'datafield_concepthierarchy'));
            }
        } else {
            $parentExists = $DB->record_exists('data_content', array('fieldid' => $fieldNombre->id, 'content' => $value));
            if (!$parentExists) {
                throw new moodle_exception(get_string('error_nonexistent_parent', 'datafield_concepthierarchy'));
            }
        }

        // Tercero, si el concepto padre existe, procede con la actualización o inserción del contenido.
        $content = new stdClass();
        $content->recordid = $recordid;
        $content->fieldid = $this->field->id; // Es el id de la base de datos.
        $content->content = $value; // El valor a insertar.

        // Busca si ya existe contenido para este campo y registro.
        if ($existingcontent = $DB->get_record('data_content', ['fieldid' => $this->field->id, 'recordid' => $recordid])) {
            // Si existe, actualízalo.
            $content->id = $existingcontent->id;
            $DB->update_record('data_content', $content);
        } else {
            // Si no existe, inserta uno nuevo.
            $DB->insert_record('data_content', $content);
        }

        return true; // Devuelve verdadero si el proceso es exitoso. 
    }
}
