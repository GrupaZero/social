@if($imgUrl)
<meta property="og:image" content="{{ $imgUrl }}"/>
@endif
<meta property="og:title" content="{{ $translation->title }}"/>
<meta property="og:url" content="{{ $url }}"/>
<meta property="og:site_name" content="{{ option('general', 'siteName') }}"/>
