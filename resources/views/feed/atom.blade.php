{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/" xml:lang="en">
    <title>{{ $user->username }} — PornTk</title>
    <subtitle>Short videos from {{ '@' . $user->username }} on PornTk</subtitle>
    <link href="{{ route('user.atom', $user->profile_id) }}" rel="self" type="application/atom+xml"/>
    <link href="{{ $user->permalink() }}" rel="alternate" type="text/html"/>
    <id>{{ $user->permalink() }}</id>
    <updated>{{ $videos->first() ? $videos->first()['created_at'] : now()->toAtomString() }}</updated>
    <author>
        <name>{{ $user->username }}</name>
        <uri>{{ url("/@{$user->username}") }}</uri>
    </author>
    <generator uri="{{ config('app.url') }}" version="1.0">PornTk</generator>
    <icon>{{ $account['avatar'] }}</icon>
    <logo>{{ url('/nav-logo.png') }}</logo>

    @foreach($videos as $video)

    <entry>
        <title>{{ $video['captionText'] ?: 'Video by ' . $user->username }}</title>
        <link href="{{ $video['url'] }}" rel="alternate" type="text/html"/>
        <id>{{ $video['url'] }}</id>
        <published>{{ $video['created_at'] }}</published>
        <updated>{{ $video['updated_at'] }}</updated>
        @if($video['captionLinked'])<content type="html">{{ $video['captionLinked'] }}</content>@endif

        <link rel="enclosure" href="{{ $video['media']['src_url'] }}" type="video/mp4" />
        <media:content url="{{ $video['media']['src_url'] }}" type="video/mp4" medium="video" duration="{{ $video['media']['duration'] }}" width="{{ $video['media']['width'] }}" height="{{ $video['media']['height'] }}"/>
        <media:thumbnail url="{{ $video['media']['thumbnail'] }}" width="{{ $video['media']['width'] }}" height="{{ $video['media']['height'] }}"/>
        <media:player url="{{ $video['url'] }}"/>
    </entry>@endforeach

</feed>
