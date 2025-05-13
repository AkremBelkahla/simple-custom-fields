<?php

namespace SCF\Fields;

class Text
{
    protected $name;
    protected $label;

    public function __construct($name, $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function validate($value)
    {
        return !empty($value);
    }
}
