<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<li class="nav-title">Магазин</li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('categories') }}'><i class='nav-icon la la-stream'></i> Категории</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('catalog') }}'><i class='nav-icon la la-cookie'></i> Каталог</a></li>
<li class='nav-item'><a class='nav-link' href='javascript:alert("Not implemented")'><i class='nav-icon la la-shopping-basket'></i> Заказы</a></li>
<li class='nav-item'><a class='nav-link' href='javascript:alert("Not implemented")'><i class='nav-icon la la-user'></i> Покупатели</a></li>


<li class="nav-title">Администрирование</li>

<li class='nav-item'><a class='nav-link' href='{{ backpack_url('page') }}'><i class='nav-icon la la-file-alt'></i> Страницы</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('message') }}'><i class='nav-icon la la-keyboard'></i> Тексты</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('telegram/user') }}'><i class='nav-icon la la-telegram'></i> Посетители</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('feedback') }}'><i class='nav-icon la la-headset'></i> Обратная связь</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('setting') }}'><i class='nav-icon la la-cog'></i> Настройки</a></li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-user-shield"></i> Администраторы</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href='javascript:alert("Not implemented")'><i class="nav-icon la la-user-shield"></i> <span>Список</span></a></li>
        <li class="nav-item"><a class="nav-link" href='javascript:alert("Not implemented")'><i class="nav-icon la la-tools"></i> <span>Роли</span></a></li>
    </ul>
</li>
