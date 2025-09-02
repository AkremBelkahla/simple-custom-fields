<?php

namespace SCF\Fields;

class File
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

    public function getAllowedTypes()
    {
        return isset($this->settings['allowed_types']) ? $this->settings['allowed_types'] : 'jpg,jpeg,png,pdf,doc,docx';
    }

    public function validate($value)
    {
        if (empty($value)) {
            return true; // Champ optionnel
        }
        
        // Si c'est un tableau $_FILES, vérifier le type de fichier
        if (is_array($value) && isset($value['name']) && isset($value['type'])) {
            $file_ext = strtolower(pathinfo($value['name'], PATHINFO_EXTENSION));
            $allowed_types = array_map('trim', explode(',', $this->getAllowedTypes()));
            
            return in_array($file_ext, $allowed_types);
        }
        
        // Si c'est une URL ou un chemin de fichier existant
        return true;
    }
}
