<?php

namespace AlphaPage\Form;

use Zend\Form\Form;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
class PageCollectionItemForm extends Form {

    public function __construct() {
        parent::__construct();
        $this->init();
    }

    public function init() {
        $this->add(array(
            'name' => 'title',
            'type' => 'text',
            'attributes' => array(
                'required' => 'required',
                'placeholder' => 'Collection Item Title',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Title',
                'label_attributes' => array(
                    'class' => 'label',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'date',
            'type' => 'date',
            'attributes' => array(
                'required' => 'required',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Date',
                'label_attributes' => array(
                    'class' => 'label',
                ),
                'format' => 'Y-m-d',
            ),
        ));

        $this->add(array(
            'name' => 'externalUrl',
            'type' => 'text',
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'External Link to this Item',
            ),
            'options' => array(
                'label' => 'External Url',
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
                    '1' => 'News',
                    '2' => 'Event',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'smallDescription',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'required' => 'required',
                'placeholder' => 'Enter description about item, this will be shown in collection list view',
                'cols' => '40',
                'rows' => '3',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Description',
                'label_attributes' => array(
                    'class' => 'label',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'description',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'required' => 'required',
                'placeholder' => 'Enter details about collection item, this will be shown in collection list item view',
                'cols' => '40',
                'rows' => '6',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Event Details',
                'label_attributes' => array(
                    'class' => 'label',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'file',
            'type' => 'Zend\Form\Element\File',
            'attributes' => array(
                'required' => 'required',
                'accept' => 'image/jpg, image/png, image/jpeg'
            ),
            'options' => array(
                'label' => 'Image',
                'label_attributes' => array(
                    'class' => 'label',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'file2',
            'type' => 'Zend\Form\Element\File',
            'attributes' => array(
                'accept' => 'image/jpg, image/png, image/jpeg'
            ),
            'options' => array(
                'label' => 'Image',
                'label_attributes' => array(
                    'class' => 'label',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'file3',
            'type' => 'Zend\Form\Element\File',
            'attributes' => array(
                'accept' => 'image/jpg, image/png, image/jpeg'
            ),
            'options' => array(
                'label' => 'Image',
                'label_attributes' => array(
                    'class' => 'label',
                ),
            ),
        ));

        $this->add(array(
            'name' => 'file4',
            'type' => 'Zend\Form\Element\File',
            'attributes' => array(
                'accept' => 'image/jpg, image/png, image/jpeg'
            ),
            'options' => array(
                'label' => 'Image',
                'label_attributes' => array(
                    'class' => 'label',
                ),
            ),
        ));
    }

}
