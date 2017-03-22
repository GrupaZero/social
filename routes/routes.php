<?php

Route::group(
    ['middleware' => ['web'], 'prefix' => '_hidden'],
    function ($router) {
        /** @var \Illuminate\Routing\Router $router */
        $router->get(
            'social-login/{service}',
            [
                'as'   => 'socialLogin',
                'uses' =>
                    'Gzero\Social\Controller\SocialAuthController@socialLogin'
            ]
        );

        $router->get(
            'social-callback/{service}',
            [
                'as'   => 'socialCallback',
                'uses' =>
                    'Gzero\Social\Controller\SocialAuthController@socialCallback'
            ]
        );
    }
);

Route::group(
    setMultilangRouting(),
    function ($router) {
        /** @var \Illuminate\Routing\Router $router */
        $router->group(
            ['middleware' => ['web'], 'prefix' => 'account', 'before' => 'auth'],
            function ($router) {
                /** @var \Illuminate\Routing\Router $router */
                $router->get(
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
