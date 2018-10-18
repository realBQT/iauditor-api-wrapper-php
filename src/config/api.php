<?php 
$root   =   'https://api.safetyculture.io/';
return [
    'resources' =>  [
        'audit'    =>  [
            'plural'    =>  'audits',
            'create'    =>  [],
            'list'      =>  [
                'method'    =>  'GET',
                'endpoint'  =>  $root.'audits/search'
            ],
            'update'    =>  [],
            'delete'    =>  [],
            'show'      =>  [
                'method'    =>  'GET',
                'endpoint'  =>  $root.'audits/{0}'
            ]
        ],
        'settings'  =>  [
            'order'     =>  'desc',
            'limit'     =>  100
        ]
    ],
    'settings'  =>  [
        'site'      =>  [
            'method'    =>  'GET',
            'endpoint'  =>  $root.'audits/search',
            'payload'   =>  []
        ]
    ]
]

?>