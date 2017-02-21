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

        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Radio',
            'attributes' => array(
                'required' => 'required',
            ),
            'options' => array(
                'label' => 'Type',
                'value_options' => array(
                    \AlphaPage\Entity\PageCollection::NESTED_TYPE_COLLECTION => 'Nested Items',
                    \AlphaPage\Entity\PageCollection::LIST_TYPE_COLLECTION => 'Simple List Items',
                ),
            ),
        ));
    }

}
