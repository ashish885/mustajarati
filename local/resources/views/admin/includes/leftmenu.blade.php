@php 
    $fieldName = getSessionLang() == 'en' ? 'en_name' : 'name';
@endphp

<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <li class="header nav-header">{{ transLang('main_navigation') }}</li>

            @if (count($navMenu))
                @foreach ($navMenu as $navigation)
                    @if ($navigation['show_in_menu'] == 1)
                        @if (isset($navigation['children']) && count($navigation['children']))
                            <li class="treeview">
                                <a href="{{ $navigation['action_path'] != '#' ? route($navigation['action_path']) : 'javascript:void(0);' }}">
                                    <i class="{{ $navigation['icon'] }}"></i> 
                                    <span>{{ $navigation[$fieldName] }}</span>
                                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
                                </a>
                                <ul class="treeview-menu">
                                    @foreach ($navigation['children'] as $sub_menu)
                                        @if ($sub_menu['show_in_menu'] == 1)
                                            <li class="{{ isCurrentRoute("{$sub_menu['action_path']}*") ? 'active' : '' }}">
                                                <a href="{{ $sub_menu['action_path'] == '#' ? '#' : route($sub_menu['action_path']) }}">
                                                    <i class="fa fa-circle-o"></i> {{ $sub_menu[$fieldName] }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li class="{{ isCurrentRoute($navigation['action_path']) ? 'active' : '' }}">
                                <a href="{{ $navigation['action_path'] == '#' ? '#' : route($navigation['action_path']) }}">
                                    <i class="{{ $navigation['icon'] }}"></i> 
                                    <span>{{ $navigation[$fieldName] }}</span>
                                </a>
                            </li>
                        @endif
                    @endif
                @endforeach
            @endif
        </ul>
    </section>
</aside>