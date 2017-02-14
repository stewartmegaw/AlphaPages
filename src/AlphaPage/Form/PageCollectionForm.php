<?php

namespace AlphaPage\Form;

use Zend\Form\Form;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageCollectionForm extends Form {

    public function __construct() {
        parent::__construct();
        $this->init();
    }

    public function init() {

        $this->add(array(
            'name' => 'name',
            'type' => 'text',
            'attributes' => array(
                'required' => 'required',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Collection Name',
                'label_attributes' => array(
                    'class' => 'label',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'description',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Collection Description',
                'label_attributes' => array(
                    'class' => 'label',
                ),
            ),
        ));
    }

}
