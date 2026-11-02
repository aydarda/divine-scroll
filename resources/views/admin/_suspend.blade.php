@if ($user && ! $user->is_admin && ! $user->suspended_at)
    <form method="POST" action="{{ route('admin.user.suspend', $user) }}" style="margin-top: 8px;"
          onsubmit="return confirm('Suspend this person and hide all their comments?');">
        @csrf
        <input type="hidden" name="reason" value="Broke the community guidelines">
        <input type="hidden" name="remove_comments" value="1">
        <button class="link-btn" type="submit">Suspend {{ $user->name }}</button>
    </form>
@elseif ($user && $user->suspended_at)
    <p class="muted" style="margin-top: 8px;">{{ $user->name }} is suspended.</p>
@endif
