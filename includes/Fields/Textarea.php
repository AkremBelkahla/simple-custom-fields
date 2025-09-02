<?php

namespace SCF\Fields;

class Textarea
{
    protected $name;
    protected $label;
    protected $settings;

    public function __construct($name, $label, $settings = [])
    {
        $this->name = $name;
        $this->label = $label;
        $this->settings = $settings;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getRows()
    {
        return isset($this->settings['rows']) ? (int)$this->settings['rows'] : 5;
    }

    public function getPlaceholder()
    {
        return isset($this->settings['placeholder']) ? $this->settings['placeholder'] : '';
    }

    public function validate($value)
    {
        return true; // Textarea peut accepter des valeurs vides
    }
}
