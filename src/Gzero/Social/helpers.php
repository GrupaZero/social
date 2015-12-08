<?php

if (!function_exists('fbOgTags')) {

    /**
     * @param $url
     * @param $translation
     *
     * @return mixed
     */
    function fbOgTags($url, $translation)
    {
        return view(
            'gzero-social::fbOgTags',
            [
                'url'         => $url,
                'translation' => $translation
            ]
        )->render();
    }
}

if (!function_exists('shareButtons')) {

    /**
     * @param $url
     * @param $translation
     *
     * @return mixed
     */
    function shareButtons($url, $translation)
    {
        return view(
            'gzero-social::shareButtons',
            [
                'url'         => $url,
                'translation' => $translation
            ]
        )->render();
    }
}
