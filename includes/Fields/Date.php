<?php

namespace SCF\Fields;

class Date
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

    public function getFormat()
    {
        return isset($this->settings['format']) ? $this->settings['format'] : 'Y-m-d';
    }

    public function validate($value)
    {
        if (empty($value)) {
            return true; // Champ optionnel
        }
        
        $d = \DateTime::createFromFormat($this->getFormat(), $value);
        return $d && $d->format($this->getFormat()) === $value;
    }
}
