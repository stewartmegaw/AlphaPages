<?php

namespace AlphaPage\Form;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
use Zend\InputFilter\InputFilter;

class PageCollectionFormFilter extends InputFilter {

    public function __construct() {

        $this->add(array(
            'name' => 'name',
            'required' => true,
        ));

        $this->add(array(
            'name' => 'description',
            'required' => false,
        ));
    }

}
