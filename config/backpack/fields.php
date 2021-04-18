<?php

return [
    'summernote' => [
        'options' => [
            'lang' => 'ru-RU',
            'height' => 140,
            'toolbar' => [
                ['font', ['bold', 'italic', 'underline', 'cleaner']],
                ['insert', ['link', 'emoji']]
            ],
            'cleaner' => [
                'action' => 'both',
                'newline' => '<p><br></p>',
                'icon' => '<i class="note-icon-eraser"></i>',
                'keepHtml' => false,
                'keepOnlyTags' => ['<p>', '<b>', '<i>', '<u>', '<a>'],
                'keepClasses' => false,
                'badTags' => ['style', 'script', 'applet', 'embed', 'noframes', 'noscript', 'html'],
                'badAttributes' => ['style', 'start'],
            ]
        ],
    ]
];
