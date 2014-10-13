//
//
// TODO - What exactly are jquery deferred objects, and can/should they be used here?



ProximoConfig = {
	webserviceHost: 'http://dubdub.jakegub.com/',
}


// Local config
//ProximoConfig.webserviceHost = 'http://local.proximo.com:8000/';



function Proximo(options)
{
	// Initialization
	options = options || {};

	// If username was not passed in, use what's in the session
	if (!options.username) {
		if (Proximo.Session.isLoggedIn()) {
			options.username = Proximo.Session.getUsername();
		}
	}

	// Pull in options, set defaults
	this.options = {
		pollInterval: 5,
		webserviceHost: ProximoConfig.webserviceHost,
		username: null,
	};
	$.extend(this.options, options);


	// Setup instance state trackers

	this.username = this.options.username;

	if (this.username) {
		this.loggedIn = true;
		this._ensureLoggedIn(this.username);
	} else {
		this.loggedIn = false;
		this._ensureLoggedOut();
	}

	// Initialize Geolocation Watching
 	this.geoWatchId = $.geolocation.watch({
		win:  $.proxy(this._updatePosition, this),
		fail: $.proxy(this._updatePositionFail, this),
	});

	// Initialize Geolocation State
	this.positionUpdateStamp = null;
	this.latitude = null;
	this.longitude = null;
	// TODO - prime above values with stored cookie values ?

}

Proximo.prototype = {

	// is logged in instance
	isLoggedIn: function() {
		return this.loggedIn;
	},
	isGuest: function() {
		return !this.loggedIn;
	},
	// get username [if logged in]
	getUsername: function() {
		if (this.isLoggedIn) {
			return this.username;
		}
		return false;
	},

	// Post Message with Text, callback optional
	postMessage: function(messageText, callback) {
//
	},

	// When new message(s), call me back
	// If there are already messages, return them right away
	listen: function(callback) {
		if (!this.messageFetchCallbacks) {
			this.messageFetchCallbacks = [];
		}
		this.messageFetchCallbacks.push(callback);
	},

	// destroy [stop listening]
	kill: function() {
		this._destructCleanup();
	},

	// ?? register location processing events ??
	getMessageFetchRequestUrl: function() {
		if (this.isLoggedIn())
			return this.options.webserviceHost + 'webservice/user-messages';
		else
			return this.options.webserviceHost + 'webservice/guest-messages';
	},
	getMessageFetchRequestParams: function() {
		var data = {};
		data.longitude = this.longitude;
		data.latitude = this.latitude;
		if (this.isLoggedIn()) {
			data.username = this.username;
		}
		return data;
	},
	// Internal Things

	_updatePosition: function(position) {
		console.log("New Geolocation tracked...");

		this.latitude = position.coords.latitude;
		this.longitude = position.coords.longitude;

		// TODO -- figure out a better way to initiate the polling/listening cycle
		// If this is the first time we're being updated, start polling cycle
		if (this.positionUpdateStamp == null) {
			this._startPollingCycle();
		}

		this.positionUpdateStamp = $.now();

		// TODO -- callback (if registered) to notify client of event
	},
	_updatePositionFail: function(error) {
		// TODO -- callback (if registered) to notify client of event
		console.log("No locationinfo available: Error Code:");
		console.log(error);
		console.log("[end of error]");
	},

	_destructCleanup: function() {
		// Stop watching to geo location
		$.geolocation.stop(this.geoWatchId);
		// Stop Polling
		this._stopPollingCycle();
		// TODO -- is there a better way to handle polling timers?
	},

	// --- Fetching and Receiving Messages ---

	_startPollingCycle: function() {
		if (this.intervalTimerId) {
			return false;
		}
		this.intervalTimerId = setInterval($.proxy(this._initiateMessageFetch, this),this.options.pollInterval * 1000);
		return this.intervalTimerId;
	},
	_stopPollingCycle: function() {
		clearInterval(this.intervalTimerId);
	},
	// Start a single Request to get Messages
	_initiateMessageFetch: function() {
		$.ajax({
			url:  this.getMessageFetchRequestUrl(),
			data: this.getMessageFetchRequestParams(),
			success: $.proxy(this._messageFetchSuccess, this),
			error: $.proxy(this._messageFetchError, this),
		});
		// TODO -- Add other callbacks for other issues
	},
	// Callback for api call to get messages
	_messageFetchSuccess: function(response) {
		// TODO - Process and Merge with exising set
		this.lastMessageFetchResponse = response.response;
		// Callback client with new message set
		if (this.messageFetchCallbacks) {
			$.each(this.messageFetchCallbacks, function(callback) {
				// TODO - make sure callback is valid
				callback(this.lastMessageFetchResponse);
			});
		}
		console.log("Request for messages returned..");
	},
	// Callback (error) for api call to get messages
	_messageFetchError: function() {
		// Callback client with event
		console.log("request for messages failed");
	},

	// --- Sending and Processing Submission Requests ---

	// --- Session Stuff ---

	_ensureLoggedIn: function() {
		Proximo.Session.ensureLoggedIn(this.username);
	},

	_ensureLoggedOut: function() {
		Proximo.Session.ensureLoggedOut();
	},

};





// Simple JS Session Shit

Proximo.Session = {};

Proximo.Session.cookieName = 'proximo.logged-in.username';

Proximo.Session.isLoggedIn = function() {
	var username = Cookie.read(Proximo.Session.cookieName);
	if (username == null) {
		return false;
	} else {
		return true;
	}
}

Proximo.Session.getUsername = function() {
	var username = Cookie.read(Proximo.Session.cookieName);
	if (username != null) {
		return username;
	}
	return false;
}

Proximo.Session.ensureLoggedIn = function(username) {
	Cookie.create(Proximo.Session.cookieName, username);
}

Proximo.Session.ensureLoggedOut = function(username) {
	Cookie.erase(Proximo.Session.cookieName);
}

var Cookie = {};

Cookie.create = function(name, value, days) {
    var expires;

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
}

Cookie.read = function(name) {
    var nameEQ = escape(name) + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return unescape(c.substring(nameEQ.length, c.length));
    }
    return null;
}

Cookie.erase = function(name) {
    Cookie.create(name, "", -1);
}

