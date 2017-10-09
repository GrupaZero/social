<a href="http://www.facebook.com/sharer.php?u={{ $url }}" title="@lang('gzero-social::common.share_button.facebook')"
   class="btn btn-primary btn-xs btn-social">
    <i aria-hidden="true" class="fa fa-facebook-square"></i> Facebook
</a>
<a href="https://plus.google.com/share?url={{ $url }}" title="@lang('gzero-social::common.share_button.google_plus')"
   class="btn btn-danger btn-xs btn-social">
    <i aria-hidden="true" class="fa fa-google-plus"></i> Google+
</a>
<a href="https://pinterest.com/pin/create/bookmarklet/?url={{ $url }}&description={{ urlencode($translation->title) }}"
   title="@lang('gzero-social::common.share_button.pinterest')" class="btn btn-xs btn-default btn-social">
    <i aria-hidden="true" class="fa fa-pinterest"></i> Pinterest
</a>

@section('footerScripts')
    <script type="text/javascript">
        $(function() {
            $(".btn-social").click(function(event) {
                event.preventDefault();
                window.open(event.target, event.text, "height=740,width=770");
            });
        });
    </script>
@append
