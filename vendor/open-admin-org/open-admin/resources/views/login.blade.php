<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('admin.title') }} | {{ __('admin.login') }}</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    @if (!is_null($favicon = Admin::favicon()))
        <link rel="shortcut icon" href="{{ $favicon }}">
    @endif

    <link rel="stylesheet" href="{{ Admin::asset('open-admin/css/styles.css') }}">
    <script src="{{ Admin::asset('bootstrap5/bootstrap.bundle.min.js') }}"></script>
    <style>
        .bg-custom {
            background-color: transparent;
        }

        .bg-white {
            background-color: whitesmoke;
        }

        .text-vertical {
            writing-mode: vertical-lr;
            text-orientation: mixed;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bolder;
            letter-spacing: .2rem;
        }

        .col-centered {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container-fluid h-100">
        <div class="row h-100">
            <!-- Left Side -->
            <div
                class="col-xs-12 col-sm-12 col-md-10 col-xl-10 col-xxl-10 d-flex justify-content-center align-items-center bg-custom">
                <div class="container-fluid m-4" style="max-width:100%;">
                    <div class="text-center mb-3 h2">
                        <img src="{!! asset('uploads/images/LogotipoImm.png') !!}" alt="Logo" class="img-fluid">
                    </div>


                    <div class="bg-body p-4 shadow-sm rounded-3">
                        @if ($errors->has('attempts'))
                            <div class="alert alert-danger m-0 text-center">{{ $errors->first('attempts') }}</div>
                        @else
                            <form action="{{ admin_url('auth/login') }}" method="post">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="mb-3">
                                    @if ($errors->has('username'))
                                        <div class="alert alert-danger">{{ $errors->first('username') }}</div>
                                    @endif
                                    <label for="username" class="form-label">{{ __('admin.username') }}</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text"><i class="icon-user"></i></span>
                                        <input type="text" class="form-control"
                                            placeholder="{{ __('admin.username') }}" name="username" id="username"
                                            value="{{ old('username') }}" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">{{ __('admin.password') }}</label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text"><i class="icon-eye"></i></span>
                                        <input type="password" class="form-control"
                                            placeholder="{{ __('admin.password') }}" name="password" id="password"
                                            autocomplete="off" required>
                                    </div>
                                    @if ($errors->has('password'))
                                        <div class="alert alert-danger">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>
                                @if (config('admin.auth.remember'))
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" name="remember" id="remember"
                                            value="1" {{ old('remember') ? 'checked="checked"' : '' }}>
                                        <label class="form-check-label"
                                            for="remember">{{ __('admin.remember_me') }}</label>
                                    </div>
                                @endif
                                <div class="clearfix text-center">
                                    <button type="submit" class="btn btn-secondary w-100"
                                        style="background-color: #ca3f2f; color:white;">
                                        <strong>
                                            Ingresar
                                        </strong>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Right Side -->
            <div class="col-xs-2 col-sm-2 col-md-2 col-xl-2 col-xxl-2 d-none d-sm-block">
                <div class="row">
                    <div class="col-12 col-centered" style="background-color: #ca3f2f; color:white;">
                        <h1 class="text-vertical">
                            Immtranet
                        </h1>
                    </div>
                </div>
            </div>


        </div>
    </div>
</body>

</html>
