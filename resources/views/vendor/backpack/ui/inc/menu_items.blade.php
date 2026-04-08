{{-- This file is used for menu items by any Backpack v7 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-cut"></i> Barbershops</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('barbershop') }}"><i class="nav-icon la la-store"></i> Barbershops</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('service-category') }}"><i class="nav-icon la la-tags"></i> Service Categories</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('service') }}"><i class="nav-icon la la-list"></i> Services</a></li>
    </ul>
</li>
