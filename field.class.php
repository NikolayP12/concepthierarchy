<?php
require_once($CFG->dirroot . '/mod/data/field/field.class.php');

class data_field_concepthierarchy extends data_field_base
{
    var $type = 'concepthierarchy'; // Se define el tipo del nuevo campo que se está creando.

    // En esta función se define cómo se va a ver y qué elementos contendrá el formulario cuando se agregue o edite una nueva entrada.
    function define_field_add(&$mform)
    {
        // Añade un campo de texto con id parentName al formulario.
        $mform->addElement('text', 'parentName', get_string('parentFieldLabel', 'datafield_concepthierarchy'));
        $mform->setType('parentName', PARAM_TEXT); // Esta línea establece el tipo de datos esperado.
    }
}
