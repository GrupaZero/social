<?php

Route::group(
    setMultilangRouting(),
    function () {
        Route::get(
            'social-login/{service}',
            [
                'as' => 'socialLogin',
                'uses' =>
                    'Gzero\Social\Controller\SocialAuthController@socialLogin'
            ]
        );

        Route::get(
            'social-callback/{service}',
            [
                'as' => 'socialCallback',
                'uses' =>
                    'Gzero\Social\Controller\SocialAuthController@socialCallback'
            ]
        );
    }
);
