document.addEventListener('DOMContentLoaded', () => {
  const closeBtn = document.getElementById('close-modal');
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
  const form = document.getElementById('widget-form');
  const link = document.getElementById('widget-modal-link');
  const message = document.getElementById('widget-modal-message');
  const modal = document.getElementById('widget-modal');
  const title = document.getElementById('widget-modal-title');

  function showSuccess(ticketId) {
    const url = `/widget?ticket_id=${ticketId}`;
    title.textContent = 'Тикет создан';
    message.textContent = 'Ваш тикет успешно создан';
    link.href = url;
    link.textContent = `Перейти к тикету #${ticketId}`;
    link.style.display = 'inline-block';
    // Очистка формы при успехе
    form.reset();
    // Показать модалку
    modal.showModal();
  }

  function showError(text) {
    title.textContent = 'Ошибка';
    message.textContent = text;
    link.style.display = 'none';
    modal.showModal();
  }

  // очищаем старые ошибки
  form.querySelectorAll('input, textarea').forEach(input => {
    input.addEventListener('input', () => {
      input.setCustomValidity('');
    });
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const button = form.querySelector('[type="submit"]');

    // Блокируем кнопку до запроса
    button.disabled = true;
    button.textContent = 'Отправка...';

    // очищаем старые ошибки
    form.querySelectorAll('input, textarea').forEach(input => {
      input.setCustomValidity('');
    });

    const formData = new FormData(form);

    try {
      const response = await fetch('/api/ticket/create', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrfToken
        },
        body: formData,
      });

      let data = {};

      try {
        data = await response.json();
      } catch (e) {
        console.error(e)
      }

      if (!response.ok) {
        throw { status: response.status, data };
      }

      showSuccess(data.id);
    } catch (error) {
      if (error.status === 422) { // Обработка ошибок при валидации параметров
        const errors = error.data?.errors || {};

        Object.keys(errors).forEach(field => {
          const input = form.querySelector(`[name="${field}"]`);
          if (input) {
            input.setCustomValidity(errors[field][0]);
            input.reportValidity();
          }
        });

        const firstField = Object.keys(errors)[0];
        const firstInput = form.querySelector(`[name="${firstField}"]`);
        firstInput?.focus();
      } else if (error.status === 429) { // Обработка для RateLimiter
        showError(error.data.message)
      } else if (error.status) {
        showError('Ошибка сервера. Попробуйте позже.');
      } else {
        showError('Ошибка сети. Проверьте соединение.');
      }
    } finally {
      // Разблокируем кнопку
      button.disabled = false;
      button.textContent = 'Отправить';
    }
  });
  closeBtn.addEventListener('click', () => {
    modal.close();
  });
});
