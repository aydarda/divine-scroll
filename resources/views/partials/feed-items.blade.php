@foreach ($posts as $passage)
    @include('partials.post', ['passage' => $passage])
@endforeach
