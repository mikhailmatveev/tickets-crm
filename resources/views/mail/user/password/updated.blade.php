{{-- mail/user/password/updated.blade.php --}}
@extends('mail.layouts.base')

@section('content')
    <p>Здравствуйте, <strong>{{ $name }}</strong>!</p>
    <p>Ваш пароль был изменён. Ваши новые данные для входа:</p>

    <div class="credentials">
        <p>Email: <span>{{ $email }}</span></p>
        <p>Пароль: <span>{{ $password }}</span></p>
    </div>
@endsection
