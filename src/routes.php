<?php

Route::group(
    setMultilangRouting(),
    function () {
        Route::get(
            'social-login/{service}',
            [
                'as'   => 'socialLogin',
                'uses' =>
                    'Gzero\Social\Controller\SocialAuthController@socialLogin'
            ]
        );

        Route::get(
            'social-callback/{service}',
            [
                'as'   => 'socialCallback',
                'uses' =>
                    'Gzero\Social\Controller\SocialAuthController@socialCallback'
            ]
        );

        Route::group(
            ['prefix' => 'account', 'before' => 'auth'],
            function () {
                Route::get(
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
