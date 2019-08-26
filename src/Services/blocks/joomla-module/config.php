<?php
return [
  'slug' => 'joomla-module',
  'name' => 'Joomla Module',
  'groups' => ['joomla'],
  'form' => [
    'general' => [
      [ 
        'name' => 'modules_core',
        'label' => 'Module',
        'type' => 'fields-group',
        'status' => 'open',
        'schema' => [
          [ 
            'name' => 'module_id', 
            'type' => 'jmodule',  
            'label' => 'Select A Module',
            'value' => ''
          ]
        ]
      ]
    ],
  ]
];
