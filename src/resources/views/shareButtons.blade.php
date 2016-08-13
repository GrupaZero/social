<a href="http://www.facebook.com/sharer.php?u={{ $url }}" class="btn btn-primary btn-xs btn-social">
    <i class="fa fa-facebook-square"></i> Facebook
</a>
<a href="https://plus.google.com/share?url={{ $url }}" class="btn btn-danger btn-xs btn-social">
    <i class="fa fa-google-plus"></i> Google+
</a>
<a href="https://pinterest.com/pin/create/bookmarklet/?url={{ $url }}&description={{ $translation->title }}}"
   class="btn btn-xs btn-default btn-social">
    <i class="fa fa-pinterest"></i> Pinterest
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
