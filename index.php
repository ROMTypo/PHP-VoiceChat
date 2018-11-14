<html>
  <head>
    <title>PHP VoiceChat</title>
  </head>
  <body>
    <div class="tools">
      <label>
        <input type="checkbox" id="record" checked>
        Mute
      </label><br>
      <label>
        <input type="range" min="0.00001" max="1" value="0.36" step="0.00001" id="audioDetect"><br>
        <input type="range" min="0.00001" max="1" step="0.00001" id="audioDetector" disabled>
        <div class="activity" style="border-radius:50%;background-color:red;height:20px;width:20px;display:inline-block"></div>
      </label>
    </div>
    <textarea class="log" readonly></textarea>
    <div class="audioElements" style="display:none;">
      
    </div>
    <script src="audioDetect.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script>
      var threshold=0.36;
      var played=[];
      $("#audioDetect").change(()=>{
        $(".audioDetector .max").text($("#audioDetect").val());
        threshold=$("#audioDetect").val();
      });
      setInterval(()=>{
        $.get("api/audio.php",data=>{
          data=data.split("\n");
          data.forEach((url)=>{
            if(played.indexOf(url)===-1){
              $(".audioElements").append("<audio src='"+url+"' controls autoplay></audio>");
              played.push(url);
            }
          })
        })
      },1000);
      var mediaRecorder;
      var recordTimeout;
      window.AudioContext = window.AudioContext || window.webkitAudioContext;
      var audioContext = new AudioContext();
      var stream;
      var checker;
      var mediaStreamSource;
      navigator.mediaDevices.getUserMedia({ audio: true }).then(s=>{
        stream=s;
        mediaStreamSource = audioContext.createMediaStreamSource(stream);
        meter = createAudioMeter(audioContext);
        mediaStreamSource.connect(meter);
      })
      
      var recorder=()=>{
        return new Promise(res=>{
          console.log("starting...");
          clearInterval(checker);
          checker=setInterval(()=>{
            if(meter.volume<threshold){
              mediaRecorder.stop();
            }
          },100);
          clearTimeout(recordTimeout);
          recordTimeout=setTimeout(()=>{
            mediaRecorder.stop();
            res();
          },1000);

          mediaRecorder = new MediaRecorder(stream);
          mediaRecorder.start();

          const audioChunks = [];
          mediaRecorder.addEventListener("dataavailable", event => {
            audioChunks.push(event.data);
          });

          mediaRecorder.addEventListener("stop", () => {
            res();
            clearInterval(checker);
            console.log("stopping...");
            const audioBlob = new Blob(audioChunks);
            const audioUrl = URL.createObjectURL(audioBlob);
            var reader = new window.FileReader();
            reader.readAsDataURL(audioBlob); 
            reader.onloadend = function() {
              base64 = reader.result;
              base64 = base64.split(',')[1];
              $.post("api/audio.php",{
                "data":"data:audio/wav;base64,"+base64
              },data=>{
                $(".log").prepend(data+"\n");
              });
            }
          });
        })
      }
      setInterval(()=>{
        $("#audioDetector").val(meter.volume.toFixed(4));
        if(meter.volume>threshold){
          $(".activity").css("background-color","green");
        }else{
          $(".activity").css("background-color","red");
        }
      },10);
      var keepRecording=()=>{
        if($("#record").is(":checked")||meter.volume<threshold){
          setTimeout(keepRecording,100);
          return;
        }
        recorder().then(keepRecording);
      };
      keepRecording();
    </script>
  </body>
</html>