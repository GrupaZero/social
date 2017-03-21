@extends('layouts.sidebarLeft')

@section('title')
    @lang('gzero-social::common.connectedServices')
@stop

@section('sidebarLeft')
    @include('account.menu', ['menu' => $menu])
@stop

@section('content')
    <h1 class="page-header">@lang('gzero-social::common.connectedServices')</h1>

    @foreach($services as $key => $service)
        @if(isset($service['client_secret']))
            <div class="row">
                <div class="col-xs-3 col-sm-2 col-md-2">
                    <h5>
                        <strong>{{ title_case($key) }}</strong>
                    </h5>
                </div>
                <div class="col-xs-2 col-sm-1 col-md-1">
                    <i class="fa fa-{{ $key }} fa-2x"></i>
                </div>
                <div class="col-xs-7 col-sm-9 col-md-9">
                    @if(empty(preg_grep('/'.$key.'/', $activeServices)))
                        <p>
                            @lang(
                                'gzero-social::common.connectInfo.' . $key,
                                [
                                'siteName' => config('app.name'),
                                'domain'   => config('app.name')
                                ]
                            )
                        </p>
                        <a class="btn btn-default connect-btn" href="{{ Url::route('socialLogin', [$key]) }}">
                            @lang('gzero-social::common.connect')
                        </a>
                    @else
                        <h3 class="mt0 text-success">
                            @lang('gzero-social::common.connected') <i class="fa fa-check-circle-o"></i>
                        </h3>
                    @endif
                </div>
            </div>
            <hr/>
        @endif
    @endforeach
@stop
@section('footerScripts')
    <script type="text/javascript">
        $(function() {
            $('.connect-btn').click(function(event) {
                Loading.start('#main-container');
            })
        });
    </script>
@append
