

    var canvas = document.getElementById('canvas');
    var context = canvas.getContext('2d');
    var video = document.querySelector('video');
    
    document.getElementById('capture-btn').addEventListener('click', function(event){ 
        document.getElementById('canvas').style.display = 'inline';
        document.getElementById('save-btn').style.display = "inline";
    });
    
    function add_sticker(sticker_src)
    {
        var imageObj = new Image();
        
        imageObj.src = sticker_src;
        context.drawImage(imageObj, 0, 0, canvas.width, canvas.height);
        document.getElementById('image').value = canvas.toDataURL('image/png');
    }   
    /**
     *  generates a still frame image from the stream in the <video>
     *  appends the image to the <body>
     */
    function takeSnapshot() {
        canvas.height = video.offsetHeight;
        canvas.width = video.offsetWidth;
        context.drawImage(video, 0, 0, canvas.width,canvas.height);
        document.getElementById('image').value = canvas.toDataURL('image/png');
    }
  
    // use MediaDevices API
    // docs: https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia
    if (navigator.mediaDevices)
    {
        navigator.mediaDevices.getUserMedia({video: true})
        .then(function(stream) {
            /* use the stream */
            video.srcObject = stream;
            document.getElementById('capture-btn').addEventListener("click", takeSnapshot);
        })
        .catch(function(err) {
            /* handle the error */
            alert("ERROR: could not access camera " + error.name);
        });
    }

    
// } 
