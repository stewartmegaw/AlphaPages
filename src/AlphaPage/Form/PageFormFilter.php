<?php

namespace AlphaPage\Form;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */
use Zend\InputFilter\InputFilter;

class PageFormFilter extends InputFilter {

    public function __construct() {

        $this->add(array(
            'name' => 'content',
            'required' => true,
        ));
    }

}
