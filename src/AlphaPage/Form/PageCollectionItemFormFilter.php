<?php

namespace AlphaPage\Form;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
use Zend\InputFilter\InputFilter;

class PageCollectionItemFormFilter extends InputFilter {

    public function __construct() {

        $this->add(array(
            'name' => 'title',
            'required' => true,
        ));

        $this->add(array(
            'name' => 'smallDescription',
            'required' => true,
        ));

        $this->add(array(
            'name' => 'description',
            'required' => true,
        ));

        $this->add(array(
            'name' => 'date',
            'required' => true,
        ));

        $this->add(array(
            'name' => 'type',
            'required' => true,
        ));

        $this->add(array(
            'name' => 'externalUrl',
            'required' => false,
        ));

        $this->add(array(
            'name' => 'parentItem',
            'required' => false,
        ));

        $this->add(array(
            'name' => 'routeLabel',
            'required' => false,
        ));

        $this->add(array(
            'name' => 'redirect',
            'required' => false,
        ));
        
        $this->add(array(
            'name' => 'file',
            'required' => false,
        ));
        
        $this->add(array(
            'name' => 'file2',
            'required' => false,
        ));
        
        $this->add(array(
            'name' => 'file3',
            'required' => false,
        ));
        
        $this->add(array(
            'name' => 'file4',
            'required' => false,
        ));
    }

}
