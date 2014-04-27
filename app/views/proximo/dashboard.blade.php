@extends('layout')

@section('content')
  <h1>Proximity</h1>
<h5>There's always someone to talk to ...</h5>

{{ Form::open(array('route'=>'proximo.postMessage', 'method'=>'POST')) }}
	{{ Form::label('content', 'Post New Message') }}<br/>
	{{ Form::text('content', Input::old('content') ) }}
	{{ Form::submit('Broadcast!') }}
{{ Form::close() }}


<br/>
<br/>
<br/>

@if(count($messages))
	<p>My Messages:</p>
	<ul>
		@foreach($messages as $mess)
			<li>{{ $mess->content }}<br/>-&gt; {{ $mess->created_at }}
			</li>
		@endforeach
	</ul>
@else
	<h3>You have not posted any messages, this is very sad</h3>
@endif



@stop
