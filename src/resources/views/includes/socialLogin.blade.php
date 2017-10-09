@if(config('services.facebook.client_secret') && config('services.facebook.client_id') ||
    config('services.google.client_secret') && config('services.google.client_id') ||
    config('services.twitter.client_secret') && config('services.twitter.client_id'))
    <div class="separator">
        <span>@lang('common.login_with')</span>
    </div>
    <div class="text-center">
        @if(config('services.facebook.client_secret') && config('services.facebook.client_id'))
            <a href="{{ route('socialLogin',['facebook']) }}" class="btn btn-primary connect-btn">
                <i class="fa fa-facebook"></i> Facebook
            </a>
        @endif
        @if(config('services.google.client_secret') && config('services.google.client_id'))
            <a href="{{ route('socialLogin',['google']) }}" class="btn btn-danger connect-btn">
                <i class="fa fa-google"></i> Google
            </a>
        @endif
        @if(config('services.twitter.client_secret') && config('services.twitter.client_id'))
            <a href="{{ route('socialLogin',['twitter']) }}" class="btn btn-info connect-btn">
                <i class="fa fa-twitter"></i> Twitter
            </a>
        @endif
    </div>
@section('footerScripts')
    <script type="text/javascript">
        $(function() {
            $('.connect-btn').click(function(event) {
                Loading.start('#main-container');
            })
        });
    </script>
@append
@endif