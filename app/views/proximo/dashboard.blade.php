@extends('layout')

@section('content')
  <h1>Proximity</h1>
<h5>There's always someone to talk to ...</h5>

<br/>

<h3>HTML5 GeoLocation</h3>
<p id="geolocation_preview">...calculating...</p>
<br/>
<input type="button" onclick="getLocation();" value="Recalculate"></input>
<br/>
<br/>

{{ Form::open(array('route'=>'proximo.postMessage', 'method'=>'POST')) }}

<?php /*
	{{ Form::hidden('latitude', null, array('id'=>'form.post.latitude')) }}<br/>
	{{ Form::hidden('longitude', null, array('id'=>'form.post.longitude')) }}<br/>
*/ ?>

	{{ Form::label('latitude', 'latitude') }}<br/>
	{{ Form::text('latitude', null, array('id'=>'form.post.latitude')) }}<br/>
	{{ Form::label('longitude', 'longitude') }}<br/>
	{{ Form::text('longitude', null, array('id'=>'form.post.longitude')) }}<br/>

	{{ Form::label('content', 'Post New Message') }}<br/>
	{{ Form::textArea('content', Input::old('content') ) }}

	{{ Form::submit('Broadcast!') }}
{{ Form::close() }}


<br/>
<br/>
<br/>

@if(count($messages))
	<p>My Messages:</p>
	<ul>
		@foreach($messages as $mess)
			<li>{{ $mess->content }}<br/>&nbsp;&nbsp;&nbsp; -&gt; {{ $mess->created_at }}<br/>&nbsp;&nbsp;&nbsp; -&gt; {{ $mess->loc['coordinates'][0] }}&nbsp;&nbsp;{{ $mess->loc['coordinates'][1] }}
			</li>
		@endforeach
	</ul>
@else
	<h3>You have not posted any messages, this is very sad</h3>
@endif



<script type="text/javascript">

	var x = document.getElementById("geolocation_preview");
	function getLocation() {
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(showPosition);
		} else {
			x.innerHTML = "Geolocation is not supported by this browser.";
		}
	}

	function showPosition(position) {
		x.innerHTML = "Latitude: " + position.coords.latitude + "<br>Longitude: " + position.coords.longitude; 

		var elemLong = document.getElementById('form.post.longitude');
		var elemLat = document.getElementById('form.post.latitude');

		elemLat.value = position.coords.latitude;
		elemLong.value = position.coords.longitude;

	}

	getLocation();

</script>






@stop
