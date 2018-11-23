 @extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8">
  <title>Multi-Broadcasters and Many Viewers using RTCMultiConnection</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

  <link rel="stylesheet" href="/demos/stylesheet.css">
  <script src="/demos/menu.js"></script>

</head>
<body>
  <header>
 
        
  </header>

<div class="jumbotron text-center">
  <h1 class="text-center">
 Multiple Vedio Broadcasters with Many Viewers </h1>
    
    <h4> <p class="text-center">
      Now Connect with multiple broadcasters and  many viewers i.e. receivers in realtime.
      And you can switch CAMERA!
    </p> </h2>
  </h4>

</div>

  <section class="make-center">
    <div class="jumbotron text-center">
    <input type="text" id="room-id" value="abcdef" autocorrect=off autocapitalize=off size=20>
    <button id="open-broadcast">Open Broadcast</button>
    <button id="join-broadcast">Join Broadcast</button>
    <button id="switch-camera" disabled>Switch Camera</button>

    </div>

    <div id="videos-container"></div>

    <div id="room-urls" style="text-align: center;display: none;background: #F1EDED;margin: 15px -10px;border: 1px solid rgb(189, 189, 189);border-left: 0;border-right: 0;"></div>
  </section>


<script src="/node_modules/webrtc-adapter/out/adapter.js"></script>
<script src="https://rtcmulticonnection.herokuapp.com/socket.io/socket.io.js"></script>
 
<script src="https://rtcmulticonnection.herokuapp.com/dist/RTCMultiConnection.min.js"></script>
<      

<!-- custom layout for HTML5 audio/video elements -->
<link rel="stylesheet" href="/demos/getHTMLMediaElement.css">
<script src="/demos/getHTMLMediaElement.js"></script>
<script>
// ......................................................
// .......................UI Code........................
// ......................................................
document.getElementById('open-broadcast').onclick = function() {
    disableInputButtons();

    connection.extra.broadcaster = true;
    DetectRTC.load(function() {
        if (DetectRTC.videoInputDevices.length > 1) {
            connection.mediaConstraints = {
                audio: true,
                video: {
                    deviceId: DetectRTC.videoInputDevices[0].deviceId
                }
            };
        }

        connection.openOrJoin(document.getElementById('room-id').value, function(isRoomExist, roomid, error) {
            if (error) {
                console.error('openOrJoin', error, roomid);
                return;
            }

            showRoomURL(roomid);
            afterConnectingSocket();

            if (!connection.isInitiator) {
                console.log('I am creating my own room as well.');

                var initialStatus = connection.dontCaptureUserMedia;
                connection.dontCaptureUserMedia = true;
                // each user must create a separate room as well!
                connection.open(connection.userid, function(isRoomOpened, roomid, error) {
                    if (error) {
                        console.error('open', error, roomid);
                        return;
                    }
                    connection.dontCaptureUserMedia = initialStatus;
                    connection.isInitiator = false;
                });
            }

            if (DetectRTC.videoInputDevices.length > 1) {
                document.getElementById('switch-camera').disabled = false;

                var idx = 0;
                document.getElementById('switch-camera').onclick = function() {
                    var deviceId = DetectRTC.videoInputDevices[0].deviceId;
                    if (idx % 2 == 0) {
                        deviceId = DetectRTC.videoInputDevices[1].deviceId;
                    }
                    idx++;

                    this.disabled = true;
                    navigator.mediaDevices.getUserMedia({
                        video: {
                            deviceId: deviceId
                        }
                    }).then(function(cam) {
                        document.getElementById(connection.userid).media.srcObject = cam;

                        document.getElementById('switch-camera').disabled = false;
                        connection.getAllParticipants().forEach(function(remoteUserId) {
                            var peer = connection.peers[remoteUserId].peer;
                            var sender = peer.getSenders().filter(function(s) {
                                return s.track.kind === 'video'
                            })[0];
                            var track = cam.getTracks().filter(function(s) {
                                return s.kind === 'video'
                            })[0];
                            if (sender && track) {
                                sender.track.stop();
                                sender.replaceTrack(track);
                            }
                        });

                        // for upcoming users
                        var audioTrack = connection.attachStreams[0].getTracks().filter(function(s) {
                            return s.kind === 'audio'
                        })[0];
                            
                        if(audioTrack) {
                            cam.addTrack(audioTrack);
                        }
                            
                        connection.attachStreams = [cam];
                    });
                };
            }
        });
    });
};

document.getElementById('join-broadcast').onclick = function() {
    disableInputButtons();
    joinBroadcastLooper(document.getElementById('room-id').value);
};

// ......................................................
// ..................RTCMultiConnection Code.............
// ......................................................

var connection = new RTCMultiConnection();

connection.autoCloseEntireSession = true;

// by default, socket.io server is assumed to be deployed on your own URL
// connection.socketURL = '/';

// comment-out below line if you do not have your own socket.io server
 connection.socketURL = 'https://rtcmulticonnection.herokuapp.com:443/';

connection.socketMessageEvent = 'multi-broadcasters-demo';

connection.session = {
    audio: true,
    video: true,
    broadcast: true
};

connection.sdpConstraints.mandatory = {
    OfferToReceiveAudio: true,
    OfferToReceiveVideo: true
};

connection.videosContainer = document.getElementById('videos-container');
connection.onstream = function(event) {
    var width = parseInt(connection.videosContainer.clientWidth / 4) - 20;
    var mediaElement = getHTMLMediaElement(event.mediaElement, {
        title: event.userid,
        buttons: ['full-screen'],
        width: width,
        showOnMouseEnter: false
    });

    connection.videosContainer.appendChild(mediaElement);

    setTimeout(function() {
        mediaElement.media.play();
    }, 5000);

    mediaElement.id = event.userid;

    if (event.type === 'remote' && connection.isInitiator) {
        var participants = [];
        connection.getAllParticipants().forEach(function(pid) {
            participants.push({
                pid: pid,
                broadcaster: connection.peers[pid].extra.broadcaster === true
            });
        });
        connection.socket.emit(connection.socketCustomEvent, {
            participants: participants
        });
    } else if (event.type === 'remote' && !connection.extra.broadcaster) {
        connection.socket.emit(connection.socketCustomEvent, {
            giveAllParticipants: true
        });
    }
};

function afterConnectingSocket() {
    connection.socket.on(connection.socketCustomEvent, function(message) {
        console.log('custom message', message);

        if (message.participants && !connection.isInitiator && !connection.extra.broadcaster) {
            message.participants.forEach(function(participant) {
                if (participant.pid === connection.userid) return;
                if (connection.getAllParticipants().indexOf(participant.pid) !== -1) return;
                if (!connection.extra.broadcaster && participant.broadcaster === false) return;

                console.log('I am joining:', participant.pid);
                connection.join(participant.pid, function(isRoomJoined, roomid, error) {
                    if (error) {
                        console.error('join', error, roomid);
                        return;
                    }
                });
            });
        }

        if (message.giveAllParticipants && connection.isInitiator) {
            var participants = [];
            connection.getAllParticipants().forEach(function(pid) {
                participants.push({
                    pid: pid,
                    broadcaster: connection.peers[pid].extra.broadcaster === true
                });
            });
            connection.socket.emit(connection.socketCustomEvent, {
                participants: participants
            });
        }
    });
}

connection.onstreamended = function(event) {
    var mediaElement = document.getElementById(event.userid);
    if (mediaElement) {
        mediaElement.parentNode.removeChild(mediaElement);
    }
};

function disableInputButtons() {
    document.getElementById('open-broadcast').disabled = true;
    document.getElementById('join-broadcast').disabled = true;
    document.getElementById('room-id').disabled = true;
}

// ......................................................
// ......................Handling Room-ID................
// ......................................................

function showRoomURL(roomid) {
    var roomHashURL = '#' + roomid;
    var roomQueryStringURL = '?roomid=' + roomid;

    var html = '<h2>Unique URL for your room:</h2><br>';

    html += 'Hash URL: <a href="' + roomHashURL + '" target="_blank">' + roomHashURL + '</a>';
    html += '<br>';
    html += 'QueryString URL: <a href="' + roomQueryStringURL + '" target="_blank">' + roomQueryStringURL + '</a>';

    var roomURLsDiv = document.getElementById('room-urls');
    roomURLsDiv.innerHTML = html;

    roomURLsDiv.style.display = 'block';
}

(function() {
    var params = {},
        r = /([^&=]+)=?([^&]*)/g;

    function d(s) {
        return decodeURIComponent(s.replace(/\+/g, ' '));
    }
    var match, search = window.location.search;
    while (match = r.exec(search.substring(1)))
        params[d(match[1])] = d(match[2]);
    window.params = params;
})();

var roomid = '';
if (localStorage.getItem(connection.socketMessageEvent)) {
    roomid = localStorage.getItem(connection.socketMessageEvent);
} else {
    roomid = connection.token();
}
document.getElementById('room-id').value = roomid;
document.getElementById('room-id').onkeyup = function() {
    localStorage.setItem(connection.socketMessageEvent, this.value);
};

var hashString = location.hash.replace('#', '');
if (hashString.length && hashString.indexOf('comment-') == 0) {
    hashString = '';
}

var roomid = params.roomid;
if (!roomid && hashString.length) {
    roomid = hashString;
}

function joinBroadcastLooper(roomid) {
    connection.extra.broadcaster = false;
    connection.dontCaptureUserMedia = true;
    connection.session.oneway = true;

    // join-broadcast looper
    (function reCheckRoomPresence() {
        connection.checkPresence(roomid, function(isRoomExist, roomid, extra) {
            // note: last parametr on checkPresence will be changed in future
            // it is expected to return "error" rather than "extra"
            // so you can compare: if(error === connection.errors.ROOM_FULL) {}
            if (extra._room) {
                if (extra._room.isFull) {
                    alert('Room is full.');
                }

                if (extra._room.isPasswordProtected) {
                    alert('Room is password protected');
                }
            }

            if (isRoomExist) {
                connection.join(roomid, function(isRoomJoined, roomid, error) {
                    if (error) {
                        console.error('join', error, roomid);
                        return;
                    }

                    afterConnectingSocket();
                });
                return;
            }

            setTimeout(reCheckRoomPresence, 5000);
        });
    })();

    disableInputButtons();
}

if (roomid && roomid.length) {
    document.getElementById('room-id').value = roomid;
    localStorage.setItem(connection.socketMessageEvent, roomid);
    joinBroadcastLooper(roomid);
}
</script>



</body>
</html>
@endsection