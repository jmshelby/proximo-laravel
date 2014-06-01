@extends('layout')

@section('content')

    	<h1>The Beginning</h1>
    	<p>Give us your handle, so you can get started...</p>

		{{ Form::open(array('route'=>'post.login', 'method'=>'POST')) }}
 
			{{ Form::text('handle', Input::old('handle') ) }}
			<br />
			{{ Form::submit("Take Me There") }}

		{{ Form::close() }}


@stop
