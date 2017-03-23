<div class="like-buttons">
    <div class="g-plusone" data-size="medium" data-annotation="none"></div>
    <div class="fb-like" data-href="{{ $url }}" data-layout="button" data-action="like" data-show-faces="false"></div>
    <a href="https://twitter.com/share" class="twitter-share-button" data-show-count="true">Tweet</a>
    <a data-pin-do="buttonBookmark" data-pin-save="true" href="https://www.pinterest.com/pin/create/button/"></a>
</div>
@section('gaPlugin')
    ga('require', 'socialWidgetTracker');
@append
@section('footerScripts')
    @parent
    <div id="fb-root"></div>
    <script src="//assets.pinterest.com/js/pinit.js" async defer></script>
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <script src="//platform.twitter.com/widgets.js" charset="utf-8" async defer></script>
    <script type="text/javascript">
        window.fbAsyncInit = function() {
            FB.init({
                appId: '{{config('services.facebook.client_id')}}',
                status : true,
                cookie : true,
                xfbml  : true,
                version: 'v2.7'
            });

            gapi.plusone.go("content");
            FB.Event.subscribe('xfbml.render', function() {
                $(".like-buttons").addClass('ready');
            });
        };

        window.___gcfg = {
            lang: '{{$translation->langCode . '_' . strtoupper($translation->langCode)}}',
            parsetags: 'explicit'
        };

        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s);
            js.async=true;
            js.id = id;
            js.src = "//connect.facebook.net/{{$translation->langCode . '_' . strtoupper($translation->langCode)}}/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>
@append
