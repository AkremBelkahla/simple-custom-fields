<?php

namespace SCF\Fields;

class Radio
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

    public function getOptions()
    {
        if (isset($this->settings['options'])) {
            return array_map('trim', explode("\n", $this->settings['options']));
        }
        return [];
    }

    public function validate($value)
    {
        if (empty($value)) {
            return true; // Champ optionnel
        }
        
        $options = $this->getOptions();
        
        // Vérifier que la valeur sélectionnée est dans les options disponibles
        return in_array($value, $options);
    }
}
