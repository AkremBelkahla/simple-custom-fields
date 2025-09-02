<?php

namespace SCF\Fields;

class Number
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

    public function getMin()
    {
        return isset($this->settings['min']) ? (float)$this->settings['min'] : null;
    }

    public function getMax()
    {
        return isset($this->settings['max']) ? (float)$this->settings['max'] : null;
    }

    public function getStep()
    {
        return isset($this->settings['step']) ? (float)$this->settings['step'] : 1;
    }

    public function validate($value)
    {
        if (empty($value) && $value !== '0') {
            return true; // Champ optionnel
        }
        
        if (!is_numeric($value)) {
            return false;
        }
        
        $num_value = (float)$value;
        
        // Vérifier min/max si définis
        if ($this->getMin() !== null && $num_value < $this->getMin()) {
            return false;
        }
        
        if ($this->getMax() !== null && $num_value > $this->getMax()) {
            return false;
        }
        
        return true;
    }
}
