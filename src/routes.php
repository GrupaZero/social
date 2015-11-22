<?php

group(
    ['prefix' => '_hidden'],
    function () {
        get(
            'social-login/{service}',
            [
                'as'   => 'socialLogin',
                'uses' =>
                    'Gzero\Social\Controller\SocialAuthController@socialLogin'
            ]
        );

        get(
            'social-callback/{service}',
            [
                'as'   => 'socialCallback',
                'uses' =>
                    'Gzero\Social\Controller\SocialAuthController@socialCallback'
            ]
        );
    }
);

group(
    setMultilangRouting(),
    function () {
        group(
            ['prefix' => 'account', 'before' => 'auth'],
            function () {
                get(
                    'connected-services',
                    [
                        'as'   => 'connectedServices',
                        'uses' =>
                            'Gzero\Social\Controller\SocialAuthController@connectedServices'
                    ]
                );
            }
        );
    }
);
