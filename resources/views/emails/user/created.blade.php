{{-- emails/user/created.blade.php --}}
@extends('emails.layouts.base')

@section('content')
    <p>Здравствуйте, <strong>{{ $name }}</strong>!</p>
    <p>Для вас был создан аккаунт. Ваши данные для входа:</p>

    <div class="credentials">
        <p>Email: <span>{{ $email }}</span></p>
        <p>Пароль: <span>{{ $password }}</span></p>
    </div>

    <p>Для активации аккаунта подтвердите ваш email, нажав на кнопку ниже:</p>

    @include('emails.components.verify')

    <p class="notice">Ссылка действительна 60 минут. Если вы не ожидали этого письма — просто проигнорируйте его.</p>
@endsection
