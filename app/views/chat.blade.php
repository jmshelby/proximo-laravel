@extends('layout')

@section('content')
	<h1>Broadcast</h1>

<br/>


    {{ Form::open(array('url'=>'message', 'method'=>'POST')) }}

		<h5>HTML5 GeoLocation</h5>
		Latitude: {{ Form::text('latitude', null, array('id'=>'form.post.latitude')) }}<br/>
		Longitude: {{ Form::text('longitude', null, array('id'=>'form.post.longitude')) }}<br/>
		<input type="button" onclick="getLocation();" value="Recalculate" />


		<br />
		{{ HTML::link('/map-view', 'Demo Map View') }}
		<br />

		<p>Now you can say whatever you want</p>

 
        {{ Form::text('message', Input::old('message'), array('size'=>'100') ) }}
        <br />
        {{ Form::submit(ucfirst($username) . " Says...", array('id'=>'form.post.submit')) }}

    {{ Form::close() }}


	@if ($messages)

			<br />
			<hr />
			<h2>Messages:</h2>
			<hr />
			<br />
			<br />
			<hr />

		@foreach($messages as $message)

			{{ $message->content }}
			<div style="font-size: 10px"> - {{ $message->user->username }}
			<span style="color: blue;">({{ $message->created_at->diffForHumans() }})</span></div>
			<div> {{ var_dump($message->loc) }} </div>
			<hr />

		@endforeach

	@else

		<p>There are no messages around you, this is very sad</p>

	@endif



<script type="text/javascript">
 
	function getLocation() {
		var elemLong = document.getElementById('form.post.longitude');
		var elemLat = document.getElementById('form.post.latitude');
		elemLat.value = "---calculating---";
		elemLong.value = "---calculating---";

		var elemSubmit = document.getElementById('form.post.submit');
		elemSubmit.disabled = true;

		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(showPosition);
		} else {
			alert("Geolocation is not supported by this browser.");
		}
	}
 
	function showPosition(position) {
		var elemLong = document.getElementById('form.post.longitude');
		var elemLat = document.getElementById('form.post.latitude');
 
		elemLat.value = position.coords.latitude;
		elemLong.value = position.coords.longitude;

		var elemSubmit = document.getElementById('form.post.submit');
		elemSubmit.disabled = false;

	}
 
	getLocation();
 
</script>


@stop
