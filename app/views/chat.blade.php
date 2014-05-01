@extends('layout')

@section('content')
	<h1>Broadcast</h1>

<br/>


    {{ Form::open(array('route'=>'post.postMessage', 'method'=>'POST')) }}
<h5>HTML5 GeoLocation</h5>
Latitude: {{ Form::text('latitude', null, array('id'=>'form.post.latitude')) }}<br/>
Longitude: {{ Form::text('longitude', null, array('id'=>'form.post.longitude')) }}<br/>
<input type="button" onclick="getLocation();" value="Recalculate" />

	<p>Now you can say whatever you want</p>

 
        {{ Form::text('message', Input::old('message'), array('size'=>'100') ) }}
        <br />
        {{ Form::submit(ucfirst($username) . " Says...", array('id'=>'form.post.submit')) }}

    {{ Form::close() }}


	@if ($messages)

		@foreach($messages as $message)

			{{ $message->content }}
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
