<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal Logistik dan Kargo Persada Duta Express">
    <meta name="keywords" content="Logistik, Ekspedisi, Kargo Nusantara, Persada Duta Express, Pengiriman Cepat">
    <meta name="author" content="Persada Duta Express">
    <link rel="icon" type="image/png" href="{{ asset('images/logo_persada_duta.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo_persada_duta.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo_persada_duta.png') }}">
    <title>Lupa Password | Persada Duta Express - Solusi Ekspedisi Logistik</title>
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('cuba/css/fontawesome.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('cuba/css/vendors/icofont.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('cuba/css/vendors/themify.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('cuba/css/vendors/flag-icon.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('cuba/css/vendors/feather-icon.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('cuba/css/vendors/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('cuba/css/style.css') }}">
    <link id="color" rel="stylesheet" href="{{ asset('cuba/css/color-1.css') }}" media="screen">
    <link rel="stylesheet" type="text/css" href="{{ asset('cuba/css/responsive.css') }}">
    <style>
        .valid {
            color: green;
            opacity: 0.8;
        }

        .invalid {
            color: red;
            opacity: 0.8;
        }

        .valid::before {
            content: '✔ ';
        }

        .invalid::before {
            content: '✘ ';
        }

        .box {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <!-- login page start-->
    <div class="container-fluid p-0">
        <div class="row m-0">
            <div class="col-12 p-0">
                <div class="login-card login-dark">
                    <div>
                        <div>
                            <a class="logo" href="{{ url('/') }}">
                                <img class="img-fluid" src="{{ asset('images/logo_persada_duta.png') }}"
                                    alt="Persada Duta Express" style="max-height: 52px; width: auto; object-fit: contain;">
                            </a>
                        </div>
                        <div>
                            @include('template.notif')
                        </div>
                        <div class="login-main">
                            @if (isset($user))
                                <form class="theme-form" method="POST" action="{{ url('forgot-password') }}">
                                    {{ method_field('PUT') }}
                                    @csrf
                                    <h4>Lupa Password</h4>
                                    <p>Harap masukkan password yang baru</p>
                                    <input type="hidden" name="id_user" value="{{ encrypt($user->id_user) }}">
                                    <div class="form-group">
                                        <label class="col-form-label">Password</label>
                                        <div class="form-input position-relative">
                                            <input class="form-control" type="password" name="password" required=""
                                                id="password" value="{{ old('password') ?? '' }}" placeholder=""
                                                oninput="validatePassword()">
                                            <div class="show-hide"><span class="show"></span></div>
                                            @error('password')
                                                <span class="text-danger">Password Tidak Valid</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-form-label">Confirmation Password</label>
                                        <div class="form-input position-relative">
                                            <input class="form-control" type="password" name="password" required=""
                                                id="confirmPassword" value="{{ old('password') ?? '' }}" placeholder=""
                                                oninput="checkPassword()">
                                            <span id="error-message"></span>
                                            @error('password')
                                                <span class="text-danger">Password Tidak Valid</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="box">
                                        <ul>
                                            <li id="length" class="invalid">Minimal 12 karakter</li>
                                            <li id="uppercase" class="invalid">Minimal satu huruf besar</li>
                                            <li id="number" class="invalid">Minimal satu angka</li>
                                            <li id="special" class="invalid">Minimal satu karakter spesial
                                                (!@#$%^&*()_+-)</li>
                                        </ul>
                                    </div>
                                    <div class="form-group mb-0">
                                        {{-- <div class="checkbox p-0">
                                        <input id="checkbox1" type="checkbox">
                                        <label class="text-muted" for="checkbox1">Setuju<a class="ms-2"
                                                href="#">Kebijakan Privasi</a></label>
                                    </div> --}}
                                        <button class="btn btn-primary btn-block w-100" type="submit"
                                            id="submit-button">Submit</button>
                                    </div>
                                    <p class="mt-4 mb-0">Sudah Punya akun?<a class="ms-2"
                                            href="{{ url('auth/login') }}">Login</a>
                                    </p>
                                </form>
                            @else
                                <form class="theme-form" method="POST" action="{{ url('forgot-password') }}">
                                    @csrf
                                    <h4>Lupa Password</h4>
                                    <p>Harap masukkan email Anda</p>
                                    <div class="form-group">
                                        <label class="col-form-label">Email</label>
                                        <input class="form-control @error('email') is-invalid @enderror"
                                            type="email" name="email" placeholder="test@gmail.com"
                                            value="{{ old('email') ?? '' }}">
                                        @error('email')
                                            <span class="text-danger">Email Tidak Valid</span>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-0">
                                        {{-- <div class="checkbox p-0">
                                        <input id="checkbox1" type="checkbox">
                                        <label class="text-muted" for="checkbox1">Setuju<a class="ms-2"
                                                href="#">Kebijakan Privasi</a></label>
                                    </div> --}}
                                        <button class="btn btn-primary btn-block w-100" type="submit"
                                            id="submit-button">Submit</button>
                                    </div>
                                    <p class="mt-4 mb-0">Sudah Punya akun?<a class="ms-2"
                                            href="{{ url('auth/login') }}">Login</a>
                                    </p>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('cuba/js/jquery.min.js') }}"></script>
        <script src="{{ asset('cuba/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('cuba/js/icons/feather-icon/feather.min.js') }}"></script>
        <script src="{{ asset('cuba/js/icons/feather-icon/feather-icon.js') }}"></script>
        <script src="{{ asset('cuba/js/config.js') }}"></script>
        <script src="{{ asset('cuba/js/script.js') }}"></script>
        <script src="{{ asset('cuba/js/script1.js') }}"></script>
        <script src="{{ asset('cuba/js/custom.js') }}"></script>
        <script>
            function refreshCaptcha() {
                document.getElementById('captcha_image').src = "{{ captcha_src('default') }}" + "?" + Math.random();
            }

            function validatePassword() {
                const password = document.getElementById('password').value;

                document.getElementById('length').className = password.length >= 12 ? 'valid' : 'invalid';
                document.getElementById('uppercase').className = /[A-Z]/.test(password) ? 'valid' : 'invalid';
                document.getElementById('number').className = /[0-9]/.test(password) ? 'valid' : 'invalid';
                document.getElementById('special').className = /[!@#$%^&*()_+\-]/.test(password) ? 'valid' : 'invalid';
            }

            function checkPassword() {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                const errorMessage = document.getElementById('error-message');
                const submitButton = document.getElementById('submit-button');

                if (password !== confirmPassword) {
                    errorMessage.textContent = 'Password tidak sama!';
                    errorMessage.style.color = 'red';
                    submitButton.disabled = true;
                    return false;
                }
                errorMessage.textContent = '';
                submitButton.disabled = false;
                return true;
            }
        </script>
    </div>
</body>

</html>
