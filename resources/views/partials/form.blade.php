<h1>Создать обращение</h1>
<form id="widget-form">
    <fieldset>
        <label>
            Как Вас зовут?
            <input
                type="text"
                name="name"
                placeholder="Ваше имя"
                autocomplete="given-name"
                required
            />
        </label>
        <label>
            E-mail
            <input
                type="email"
                name="email"
                placeholder="Ваш E-mail"
                autocomplete="given-email"
                required
            />
        </label>
        <label>
            Телефон
            <input
                type="tel"
                name="phone"
                placeholder="+78005553535"
                autocomplete="given-phone"
                required
            />
        </label>
        <label>
            Тема обращения
            <input
                type="text"
                name="subject"
                required
            />
        </label>
        <label>
            Описание проблемы
            <textarea name="text" rows="2"></textarea>
        </label>
    </fieldset>
    <button type="submit">Отправить</button>
</form>

@include('partials.modal')
