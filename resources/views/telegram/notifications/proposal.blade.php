Получено новое предложение от пользователя <a href="tg://user?id={{$feedback->user->user_id }}">{{ $feedback->user->full_name }}</a>

{{ strip_tags($feedback->message) }}

---
Открыть в CMS: <a href="{{ backpack_url(sprintf('feedback/%d/show', $feedback->id)) }}">Обратная Связь</a>
