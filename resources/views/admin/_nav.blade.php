<div class="tabs">
    <a class="filter-btn {{ request()->routeIs('admin') ? 'is-active' : '' }}" href="{{ route('admin') }}">Overview</a>
    <a class="filter-btn {{ request()->routeIs('admin.comments') ? 'is-active' : '' }}" href="{{ route('admin.comments') }}">Comments</a>
    <a class="filter-btn {{ request()->routeIs('admin.suggestions') ? 'is-active' : '' }}" href="{{ route('admin.suggestions') }}">Suggestions</a>
    <a class="filter-btn {{ request()->routeIs('admin.users') ? 'is-active' : '' }}" href="{{ route('admin.users') }}">People</a>
</div>
