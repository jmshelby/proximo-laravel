# Proximo
=========

Proximo prototype built on top of laravel

**[TEST Environment](http://dubdub.jakegub.com/)**

**[Demo Integrated MapView](http://dubdub.jakegub.com/map-view/)**

**[Demo Web Client](http://jmshelby.github.io/proximo-laravel/)**



# APIs
=========

GET /webservice/user
  Get user info.
  Params: username
GET /webservice/user-post
  Update user info
  Params: username, (any other field)

ANY /webservice/messages
  Get messages for yourself, in proximity to passed latitude/longitude
  Params: username, latitude, longitude
ANY /webservice/post-message
  Post message for yourself, at latitude/longitude
  Params: username, latitude, longitude, content

