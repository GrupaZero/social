<div class="separator">
    <span>@lang('common.loginWith')</span>
</div>
<div class="text-center">
    <a href="{{ route('socialLogin',['facebook']) }}" class="btn btn-primary connect-btn">
        <i class="fa fa-facebook"></i> Facebook
    </a>
    <a href="{{ route('socialLogin',['google']) }}" class="btn btn-danger connect-btn">
        <i class="fa fa-google"></i> Google
    </a>
    <a href="{{ route('socialLogin',['twitter']) }}" class="btn btn-info connect-btn">
        <i class="fa fa-twitter"></i> Twitter
    </a>
</div>
@section('footerScripts')
    <script type="text/javascript">
        $(function () {
            $('.connect-btn').click(function (event) {
                Loading.start('body');
            })
        });
    </script>
@append
