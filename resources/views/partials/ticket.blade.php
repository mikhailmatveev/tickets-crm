<hgroup>
    <h1>
        {{ $ticket->subject }}
    </h1>
    <p>
        <mark>{{ $ticket->status }}</mark>
        Создано: {{ $ticket->created_at  }}
    </p>
</hgroup>
<hr>
@if($ticket->customer)
<article>E-mail: {{ $ticket->customer->email }}</article>
<article>Телефон: {{ $ticket->customer->phone }}</article>
<h3>Описание проблемы</h3>
<blockquote>
    {{ $ticket->text }}
    <footer>
        <cite>{{ $ticket->customer->name }}</cite>
    </footer>
</blockquote>
@endif
@if($ticket->replies && count($ticket->replies) > 0)
<h3>Ответы менеджера</h3>
@foreach($ticket->replies as $reply)
<blockquote>
    {{ $reply->text }}
    <footer>
        <cite>{{ $reply->updated_at }}</cite>
    </footer>
</blockquote>
@endforeach
@endif
