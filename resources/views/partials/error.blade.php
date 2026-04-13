@if ($errors->any())
    <hgroup>
        <h1>Ошибка</h1>
        <p>{{ $errors->first() }}</p>
    </hgroup>
    <a href="{{ route('widget') }}">Назад</a>
@endif
