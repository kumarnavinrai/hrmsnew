<?php

/**
 * Implementation of hook_menu().
 * Registers a form-based page that you can access at "http://localhost/myajax"
*/

'#ajax' => array(
    'event' => 'change',
    'callback' => 'myajax_ajax_callback',
    'wrapper' => 'dropdown_second_replace',
),

function myajax_menu(){
    return array(
        'myajax' => array(
            'title' => 'A page to test ajax',
            'page callback' => 'drupal_get_form',
            'page arguments' => array('myajax_page'),
            'access arguments' => array('access content'), 
        )
    );
}



/**
 * A form with a dropdown whose options are dependent on a
 * choice made in a previous dropdown.
 *
 * On changing the first dropdown, the options in the second are updated.
 */
function myajax_page($form, &$form_state) {
    // Get the list of options to populate the first dropdown.
    $options_first = myajax_first_dropdown_options();

    // If we have a value for the first dropdown from $form_state['values'] we use
    // this both as the default value for the first dropdown and also as a
    // parameter to pass to the function that retrieves the options for the
    // second dropdown.
    $value_dropdown_first = isset($form_state['values']['dropdown_first']) ? $form_state['values']['dropdown_first'] : key($options_first);

    $form['dropdown_first'] = array(
        '#type' => 'select',
        '#title' => 'First Dropdown',
        '#options' => $options_first,
        '#default_value' => $value_dropdown_first,

        // Bind an ajax callback to the change event (which is the default for the
        // select form type) of the first dropdown. It will replace the second
        // dropdown when rebuilt
        '#ajax' => array(
            // When 'event' occurs, Drupal will perform an ajax request in the
            // background. Usually the default value is sufficient (eg. change for
            // select elements), but valid values include any jQuery event,
            // most notably 'mousedown', 'blur', and 'submit'.
            'event' => 'change',
            'callback' => 'myajax_ajax_callback',
            'wrapper' => 'dropdown_second_replace',
        ),
    );
    $form['dropdown_second'] = array(
        '#type' => 'select',
        '#title' => 'Second Dropdown',
        // The entire enclosing div created here gets replaced when dropdown_first
        // is changed.
        '#prefix' => '<div id="dropdown_second_replace">',
        '#suffix' => '</div>',
        // when the form is rebuilt during ajax processing, the $value_dropdown_first variable
        // will now have the new value and so the options will change
        '#options' => myajax_second_dropdown_options($value_dropdown_first),
        '#default_value' => isset($form_state['values']['dropdown_second']) ? $form_state['values']['dropdown_second'] : '',
    );
    return $form;
}

/**
 * Selects just the second dropdown to be returned for re-rendering
 *
 * Since the controlling logic for populating the form is in the form builder
 * function, all we do here is select the element and return it to be updated.
 *
 * @return renderable array (the second dropdown)
 */
function myajax_ajax_callback($form, $form_state) {
    return $form['dropdown_second'];
}


/**
 * Helper function to populate the first dropdown. This would normally be
 * pulling data from the database.
 *
 * @return array of options
 */
function myajax_first_dropdown_options() {
    return array(
        'colors' => 'Names of colors',
        'cities' => 'Names of cities',
        'animals' => 'Names of animals',
    );
}


/**
 * Helper function to populate the second dropdown. This would normally be
 * pulling data from the database.
 *
 * @param key. This will determine which set of options is returned.
 *
 * @return array of options
 */
function myajax_second_dropdown_options($key = '') {
    $options = array(
        'colors' => array(
            'red' => 'Red',
            'green' => 'Green',
            'blue' => 'Blue'
        ),
        'cities' => array(
            'paris' => 'Paris, France',
            'tokyo' => 'Tokyo, Japan',
            'newyork' => 'New York, US'
        ),
        'animals' => array(
            'dog' => 'Dog',
            'cat' => 'Cat',
            'bird' => 'Bird'
        ),  
    );
    if (isset($options[$key])) {
        return $options[$key];
    }
    else {
        return array();
    }
}