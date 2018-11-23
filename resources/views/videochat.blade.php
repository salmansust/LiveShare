 @extends('layouts.app')

@section('content')
<div class="jumbotron text-center">
  <h1 class="text-center">
 Simple Vedio Chat feature over the browser </h1>
    
    <h4> <p class="text-center">
      Now chat with your friend over browser
    </p> </h2>
  </h4>


<script src="https://rtcmulticonnection.herokuapp.com/dist/RTCMultiConnection.min.js"></script>
<script src="https://rtcmulticonnection.herokuapp.com/socket.io/socket.io.js"></script>

<script>
var connection = new RTCMultiConnection();


connection.socketURL = 'https://rtcmulticonnection.herokuapp.com:443/';

connection.session = {
    audio: true,
    video: true
};

connection.openOrJoin('your-room-id');
</script>
    


@endsection