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

<p>Messages:</p>


@stop
