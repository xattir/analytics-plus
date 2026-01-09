<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Predefined Selector Tags
    |--------------------------------------------------------------------------
    |
    | These tags are converted to CSS selectors when used in advertisements.
    | Users can select these predefined tags or use custom CSS selectors.
    |
    */
    'predefined_selectors' => [
        'after_header' => 'header + *',
        'before_footer' => 'footer',
        'sidebar' => '.sidebar, aside, [data-sidebar]',
        'after_content' => 'main > *:last-child, .content + *',
        'before_content' => 'main > *:first-child, .content',
        'in_header' => 'header',
        'in_footer' => 'footer',
        'top_of_page' => 'body > *:first-child',
        'bottom_of_page' => 'body > *:last-child',
    ],
];

