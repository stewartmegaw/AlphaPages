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
    }

}
