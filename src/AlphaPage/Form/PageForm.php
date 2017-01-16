<?php

namespace AlphaPage\Form;

use Zend\Form\Form;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageForm extends Form {

    public function __construct() {
        parent::__construct('PagesForm');

        $this->add(array(
            'name' => 'content',
            'type' => 'text',
            'attributes' => array(
                'id' => 'code-mirror-textarea',
                'required' => 'required',
                'encoding' => 'UTF-8',
                'cols' => '40',
                'rows' => '50',
                'class' => 'form-control',
                'wrap' => 'soft',
            ),
            'options' => array(
                'label' => 'Content',
                'label_attributes' => array(
                    'class' => 'label',
                ),
            ),
        ));
    }

}
