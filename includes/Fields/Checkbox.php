<?php

namespace SCF\Fields;

class Checkbox
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
        
        if (!is_array($value)) {
            $value = [$value];
        }
        
        $options = $this->getOptions();
        
        // Vérifier que toutes les valeurs sélectionnées sont dans les options disponibles
        foreach ($value as $selected) {
            if (!in_array($selected, $options)) {
                return false;
            }
        }
        
        return true;
    }
}
