    @extends('layouts.auth')

    @section('content')
    @if ($errors->has('login'))
        <div class="alert alert-danger">
            {{ $errors->first('login') }}
        </div>
    @endif
    <form action="{{route('login')}}" method="POST">
        @csrf
        <div class="form-group text-left">
            <label for="username">Username</label>
            <input id="username" type="username" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" autofocus>
            @error('username')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group text-left">
            <label for="password" class="control-label">Password</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="current-password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-lg btn-block text-white" style="background-color: #6C46A1; border-radius: 8px;">
                Login
            </button>
        </div>
    </form>
    @endsection
