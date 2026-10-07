@extends('template.root')
@section('page-name', Request::segment(1))
@section('title-page', 'Ganti Password')
@section('content')
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
    <div class="col-md-6">
        <div class="login-main">
            <form class="theme-form" method="POST" action="{{ url('change-password') }}">
                @csrf
                <div class="form-group">
                    <label class="col-form-label">Password Lama</label>
                    <input class="form-control" type="password" name="old_password" required="" id="password"
                        placeholder="Masukkan Password Lama" value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label class="col-form-label">Password</label>
                    <div class="form-input position-relative">
                        <input class="form-control" type="password" name="password" required="" id="new_password"
                            value="{{ old('password') ?? '' }}" placeholder="" oninput="validatePassword()">
                        @error('password')
                            <span class="text-danger">Password Tidak Valid</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-form-label">Confirmation Password</label>
                    <div class="form-input position-relative">
                        <input class="form-control" type="password" name="password" required="" id="confirmPassword"
                            value="{{ old('password') ?? '' }}" placeholder="" oninput="checkPassword()">
                        <span id="error-message"></span>
                        @error('password')
                            <span class="text-danger">Password Tidak Valid</span>
                        @enderror
                    </div>
                </div>
                <input type="checkbox" id="togglePassword" onclick="togglePasswordVisibility()" /> Show Password
                <div class="box">
                    <ul>
                        <li id="length" class="invalid">Minimal 12 karakter</li>
                        <li id="uppercase" class="invalid">Minimal satu huruf besar</li>
                        <li id="number" class="invalid">Minimal satu angka</li>
                        <li id="special" class="invalid">Minimal satu karakter spesial
                            (!@#$%^&*()_+-)</li>
                    </ul>
                </div>
                <br>
                <div class="form-group mb-0">
                    {{-- <div class="checkbox p-0">
                        <a class="ms-2 mb-0 text-center" href="{{ url('register') }}">Lupa
                            Password?</a>
                    </div><a class="link" href="{{ url('/') }}"></a> --}}
                    <div class="text-end mt-3">
                        <button class="btn btn-primary btn-block w-100" type="submit">Ganti Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('script')
    <script>
        function validatePassword() {
            const password = document.getElementById('new_password').value;

            document.getElementById('length').className = password.length >= 12 ? 'valid' : 'invalid';
            document.getElementById('uppercase').className = /[A-Z]/.test(password) ? 'valid' : 'invalid';
            document.getElementById('number').className = /[0-9]/.test(password) ? 'valid' : 'invalid';
            document.getElementById('special').className = /[!@#$%^&*()_+\-]/.test(password) ? 'valid' : 'invalid';
        }

        function checkPassword() {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const errorMessage = document.getElementById('error-message');
            const submitButton = document.getElementById('submit-button');

            if (password !== confirmPassword) {
                errorMessage.textContent = 'Password tidak sama!';
                errorMessage.style.color = 'red';
                return false;
            }
            errorMessage.textContent = '';
            return true;
        }

        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirmPassword');
            const newPasswordField = document.getElementById('new_password');
            const toggleCheckbox = document.getElementById('togglePassword');

            passwordField.type = toggleCheckbox.checked ? 'text' : 'password';
            confirmPasswordField.type = toggleCheckbox.checked ? 'text' : 'password';
            newPasswordField.type = toggleCheckbox.checked ? 'text' : 'password';
        }
    </script>
@endsection
