<html>
  <head>
    <title>hangman</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <style type="text/css">
      .hangman, .loading{
        display:block;
        margin:0;
        padding:0;
        width:100%;
        height:100%;
        font-family:'Courier New', monospace;
      }
      .hangman p{
        display:inline-block;
        font-size:8px;
        line-height:8px;
        padding:0;
        margin:0;
      }
      a:link,a:hover,a:visited{
        color:#00f;
      }
      a:active{
        color:#0000dd;
      }
      .test-character{
        position:absolute;
        top:-999px;
        left:-999px;
      }
      .center-axis{
        position:absolute;
        top:50%;
        left:50%;
        width:0;
        height:0;
      }
      .loading{
        position:absolute;
        top:0;
        left:0;
        background:#ddd;
      }
      .loading-container{
        display:block;
        position:absolute;
        top:-5px;
        height:10px;
      }
      .loading-bar{
        display:block;
        position:relative;
        height:100%;
        width:0;
        margin:0 auto;
        background:#222;
        border-radius: 5px;
      }
      .hangman .loading-text{
        position:absolute;
        top:-20px;
        font-size:20px;
        text-align: center;
        width:100%;
      }

      .hangman .part{
        text-align:center;
        animation-name:shock;
        animation-iteration-count:infinite;
        animation-duration:.25s;
        animation-play-state:paused;
        animation-timing-function:linear;
      }
      .hangman .part.show{
        z-index:100;
        position:absolute;
        top:0;
        bottom:0;
        left:0;
        right:0;
      }

      .the-word-container{
        display:block;
        position:absolute;
      }
      .the-word-container p{
        font-size:16px;
        text-align:center;
        width:100%;
        padding:10px 0;
        line-height:4px;
        margin-bottom:40px;
      }
      .the-word{
        position:relative;
        display:block;
        width:100%;
        clear:both;
      }
      .parts-list{
        display:block;
        position:relative;
        margin:0 0 40px 0;
        padding:10px 0;
        clear:both;
      }
      .list-item{
        position:relative;
        float:left;
        display:block;
        margin:0 0;
        margin-right:2%;
      }
      .remaining-part{
        color:#00F;
        text-transform:lowercase;
      }
      .failed-attempt{
        color:#F00;
        text-transform:uppercase;
      }
      .letter{
        position:relative;
        float:left;
        display:block;
        margin:0 0;
        margin-right:2%;
        border-bottom:3px solid #000;
        line-height: normal;
        text-align:center;
        font-size:3em;
        height:1em;
        text-transform:uppercase;
      }
      .letter:last-child{
        margin-right:0;
      }
      .letter:first-child{
        margin-left:1%;
      }

      .guess-form{
        display:block;
        width:100%;
        padding:20px 0;
        margin: 0;
        clear:both;
      }
      #attempt-display, #attempt{
        display:block;
        margin:0 auto;
        width:1.2em;
        height:1.2em;
        text-align:center;
        text-transform:uppercase;
        font-size:3em;
        line-height: 1.25em;
        outline:none;
        background:#eee;
        border-radius:0;
        cursor:pointer;
        border-bottom-width: 5px;
        border-bottom-style: solid;
      }
      .unfocused{
        border-bottom:5px solid #000;
      }

      .focused.correct, .focused.incorrect{
        animation-iteration-count:infinite;
        animation-duration:2s;
        animation-play-state:running;
        animation-timing-function:linear;
      }
      .focused.correct{
        color:#00F;
        border-bottom-color:#00F;
        animation-name:caret-correct;
      }
      .focused.incorrect{
        color:#F00;
        border-bottom-color:#F00;
        animation-name:caret-incorrect;
      }

      #attempt{
        height:1px;
        width:1px;
        border:none;
      }

      .hangman .hide{
        display:none;
        position:fixed;
        top:-9999px;
        left:-9999px;
      }
      .hangman .show{
        display:block;
        position:relative;
        top:0;
        left:0;
        animation-play-state:running;
        opacity:1;
        height:100%;
        overflow: hidden;
      }
      .strike p{
        text-decoration: line-through;
      }

      @keyframes shock{
        0%{
          color:#fff;
          background-color:#000;
          text-shadow: 0px 0px 0px #f00;
        }
        50%{
          color:#000;
          background-color:#fff;
        }
        100%{
          color:#fff;
          background-color:#000;
          text-shadow: 5px 5px 0px #f00;
        }
      }
      @keyframes caret-correct{
        0%{
          border-bottom-color: #00F;
        }
        10%{
          border-bottom-color: #ccc;
        }
        50%{
          border-bottom-color: #ccc;
        }
        60%{
          border-bottom-color: #00F;
        }
      }
      @keyframes caret-incorrect{
        0%{
          border-bottom-color: #F00;
        }
        10%{
          border-bottom-color: #ccc;
        }
        50%{
          border-bottom-color: #ccc;
        }
        60%{
          border-bottom-color: #F00;
        }
      }
    </style>
  </head>
  <body>
    <section class="hangman">
      <p class="test-character">#</p>
      <div class="hangman">
        <div class="center-axis">
          <div class="the-word-container">
            <div class="parts-list"></div>
            <p id="progress">type a letter</p>
            <div class="the-word"></div>
            <div class="guess-form">
              <label id="attempt-display" for="attempt" class="focused correct">
                <span id="guess">?</span>
                <input name="attempt" id="attempt" type="text" maxlength="1" value="" autofocus/>
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="loading">
        <div class="center-axis">
          <div class="loading-container">
            <p class="loading-text">Loading...</p>
            <div class="loading-bar"></div>
          </div>
        </div>
      </div>

      <div class="body-parts"></div>

      <script type="text/javascript">
        var ascii;
        var word;
        var progress;
        var attempt = 0;
        var gameover = false;
        var width = window.innerWidth;
        var height = window.innerHeight;
        var char_width = $('p.test-character').width();
        var char_height = $('p.test-character').height();
        var width_bias = width/char_width;
        var height_bias = height/char_height;

        var parts = [
          'head','bod',
          'arm','hand','arm','hand',
          'leg','foot','leg','foot'
        ];

        var init = {
          width_bias:width_bias,
          height_bias:height_bias,
          char_width:char_width,
          char_height:char_height,
          screen_width:width,
          screen_height:height,
          difficulty: 'random'
        };

        function loadGame(){
          // setCenter('.loading-container');

          // $.ajax({
          //   type: "GET",
          //   url: "http://randomword.setgetgo.com/get.php",
          //   dataType: 'jsonp',
          //   jsonpCallback: 'setWord',
          //   error: function(){
          //     setWord(0);
          //   }
          // });

          $.ajax({
            type: "POST",
            url: "ascii.php",
            data: init,
            success: function( data ) {
              // console.log(data);
              var d = JSON.parse(data);
              var ascii = d.ascii;
              var percentage = (100/ascii.length)-2+'%';
              for(var i=0; i<ascii.length; i++){
                $('.body-parts').append('<div id="attempt'+i+'" class="part hide"><p>'+ascii[i]+'</p></div>');
                $('.parts-list').append('<div id="list'+i+'" class="list-item remaining-part" style="width:'+percentage+'"><p>'+parts[i]+'</p></div>');
              }
              word = d.word;
              setupWord();
            },
            error: function(){
              alert('Sorry, could not reach the server. Come back later.');
            },
            complete: function(){
              $('.loading').animate({
                'opacity':0
              },500, function(){
                $('.loading').addClass('hide');
              })
            }
          });
        }
        loadGame();

        function setupWord(){
          progress = word;
          var percentage = (100/word.length)-2 + '%';
          for(var i=0; i<word.length; i++){
            $('.the-word').append('<div id="letter'+i+'" class="letter" style="width:'+percentage+'"></div>');
          }
          setCenter('.the-word-container');
        }

        function setCenter(elem){
          if(width < 960){
            var w = width;
          } else {
            var w = 960;
          }
          var h = parseInt($(elem).css('height'));
          console.log(h/-2);
          $(elem).css({
            'width': w-50,
            'top': h/-2,
            'left': (w-50)/-2
          })
        }
        function replaceAll(str, find, replace) {
          return str.replace(new RegExp(find, 'g'), replace);
        }


        $('#attempt').on('input change', function() {
          if(attempt !== 10 && progress !== ''){
            var letter = $(this).prop('value');
            $('#guess')
              .stop()
              .css('opacity',1)
              .html(letter)
              .animate({'opacity':0},5000);
            //console.log(letter);

            var has_letter = word.indexOf(letter);
            if( has_letter >= 0){
              $('#attempt-display')
                .removeClass('incorrect')
                .addClass('correct');

              var position = 0;
              var split = word.split(letter);
              //console.log(split);

              for(var i = 0; i < split.length-1; i++){
                var ltr = split[i].length;
                var elem = '#letter'+(position+ltr);
                //console.log(elem, letter);
                $(elem).html(letter);
                position = position+ltr+1;
              }
              progress = replaceAll( progress, letter, '' );
              console.log(progress);
            } else {
              $('#attempt-display')
                .removeClass('correct')
                .addClass('incorrect');

              var a = '#attempt'+attempt;
              var b = '#list'+attempt;
              $(a).toggleClass('show hide');
              $(b)
                .toggleClass('failed-attempt remaining-part')
                .html('<p>'+letter+'</p>');

              window.setTimeout(function(){
                console.log('timeout', a);
                $(a).toggleClass('show hide');
              }, 1000);

              window.clearTimeout();
              attempt++;
            }

            if(attempt == 10){
              gameover = true;
              $('#progress').html(word+' / you failed / <a href="/project/hangman">try again?</a>');
            }
            if(progress == ''){
              gameover = true;
              $('#progress').html(word+' / you won / <a href="/project/hangman">play again?</a>');
            }
            $(this).prop('value','');
          }
        });

        $('#attempt-display').focusout(function(){
          $(this).removeClass('focused')
                 .addClass('unfocused');
          if(!gameover){
            $('#progress').html('click the input box to enable your keyboard');
          }
        });
        $('#attempt-display').focusin(function(){
          $(this).removeClass('unfocused')
                 .addClass('focused');
          if(!gameover){
            $('#progress').html('type a letter');
          }
        });
      </script>
    </section>
  </body>
<html>
