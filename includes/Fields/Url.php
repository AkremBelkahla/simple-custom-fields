<?php

namespace SCF\Fields;

class Url
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

    public function getPlaceholder()
    {
        return isset($this->settings['placeholder']) ? $this->settings['placeholder'] : '';
    }

    public function validate($value)
    {
        if (empty($value)) {
            return true; // Champ optionnel
        }
        
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }
}
