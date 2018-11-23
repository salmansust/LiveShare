 @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Contact</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                   <p>Details are coming! </p>
                   <p>If you have any sugession, please feel free to contact <a href="https://facebook.com/salman.k.rifat"> Me</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection