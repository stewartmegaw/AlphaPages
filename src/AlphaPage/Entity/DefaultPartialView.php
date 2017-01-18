<?php

namespace AlphaPage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author Haris Mehmood <haris.mehmood@outlook.com>
 */

/**
 * @ORM\Entity
 * @ORM\Table(name="alpha_default_partial_views") 
 * @ORM\HasLifecycleCallbacks
 */
class DefaultPartialView {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     */
    protected $id;

    /** @ORM\Column(type="string") */
    protected $name;

    /** @ORM\Column(type="text") */
    protected $view;

    /** @ORM\Column(type="text", nullable=true) */
    protected $style;

    public function __call($name, $arguments) {

        $field = lcfirst(substr($name, 3));
        $method = substr($name, 0, 3);

        if ($method == 'set') {
            $this->{$field} = $arguments[0];
            return $this;
        }
        if ($method == 'get') {
            return $this->{$field};
        }
    }

}
