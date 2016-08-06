<?php

if (!function_exists('fbOgTags')) {

    /**
     * @param string                      $url
     * @param ContentTranslationPresenter $translation
     * @param bool|string                 $imgUrl
     *
     * @return mixed
     */
    function fbOgTags($url, $translation, $imgUrl = false)
    {
        return view(
            'gzero-social::fbOgTags',
            [
                'url'         => $url,
                'translation' => $translation,
                'imgUrl'      => $imgUrl
            ]
        )->render();
    }
}

if (!function_exists('shareButtons')) {

    /**
     * @param string                      $url
     * @param ContentTranslationPresenter $translation
     *
     * @return mixed
     */
    function shareButtons($url, $translation)
    {
        return view(
            'gzero-social::shareButtons',
            [
                'url'         => $url,
                'translation' => $translation,
            ]
        )->render();
    }
}

if (!function_exists('likeButtons')) {

    /**
     * @param string                      $url
     * @param ContentTranslationPresenter $translation
     *
     * @return mixed
     */
    function likeButtons($url, $translation)
    {
        return view(
            'gzero-social::likeButtons',
            [
                'url'         => $url,
                'translation' => $translation,
            ]
        )->render();
    }
}
