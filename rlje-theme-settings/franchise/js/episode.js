var setEndCookieTimeMinutes = function(minutes) {
        var endCookie = new Date(),
            mins = (typeof minutes !== 'undefined' ) ? minutes : 2;
        return endCookie.setMinutes(endCookie.getMinutes() + mins);
    };


var goingToNextEpisode = function(url){
    docCookies.setItem('playerOption', 'playFromStart', setEndCookieTimeMinutes(), '/');
    console.log('going to next episode...');
    setTimeout(function() { window.location.href = url; }, 500);
};

var episodePlayer = function(episodeId, setTimePosition) {
  var player = videojs('brightcove-episode-player'),
    isPlayingSet = function(item) {
      var isSet = false;
      console.log('Checking option set.');
      if (
        docCookies.hasItem('playerOption') &&
        docCookies.getItem('playerOption') === item
      ) {
        isSet = true;
        docCookies.removeItem('playerOption');
        docCookies.removeItem('playerOption', '/');
        console.log('Removing player option set.');
      }
      return isSet;
    },
    fromStart = isPlayingSet('playFromStart'),
    resume = isPlayingSet('playResume'),
    initTime = setTimePosition,
    strempositionInterval,
    streamPositionTimer = 30000, // Send stream position every 30 seconds.
    $continueWatchingBlock = jQuery('.continueWatching'),
    $playNextEpisodeBlock = jQuery('.playNextEpisode'),
    showingExtBtn = true,
    showingNextEpisodePrompt = false,
    isCapturingStreamPosition = false,
    $overlay = jQuery('#nextEpisodeOverlay'),
    $overlayCloned = $overlay.clone();

  if (fromStart) {
    initTime = 0;
  }
  if (resume || fromStart) {
    player.autoplay(true);
    $continueWatchingBlock.hide();
    showingExtBtn = false;
  }

  player.on('loadedmetadata', function() {
      //Set the current time once the initial duration is loaded.
      if (setTimePosition) {
        player.currentTime(setTimePosition);
      }
      if (fromStart) {
        player.currentTime(initTime);
        console.log('set current time from start');
        setStreamPosition('STOP');
      }
  });
  player.on('play', function() {
      // console.log('play', event, event.target.player.currentTime(), parseInt(event.target.player.currentTime(), 10));
      if (isPlayingSet('playFromStart')) {
        return;
      }
      ///Only fixes a Safari issue (sometimes the player doesn't load the current time).
      if (initTime > 0) {
        var isEnded = initTime > player.duration() - 10 ? true : false;
        if (isEnded) {
          initTime = 0;
        } ///if the video ended, it plays from beginer.
        if (player.currentTime() !== initTime) {
          player.currentTime(initTime);
          console.log('Player: Init time set.');
        }
        initTime = null;
      }
      ///End Safari fix.

      //Checking if the current User is Active
      var data = {
          action: 'is_user_active',
          token: episode_object.token
        },
        stopPlayer = function() {
          player.pause();
        },
        processData = function(response) {
          var data = response.data;
          if (data.isActive !== true) {
            stopPlayer();
            console.error('User not active');
          }
        };

      // This needs to be done differently
      jQuery.post(episode_object.ajax_url, data, processData).fail(function() {
        console.error('Error checking if the user is active');
      });
  });
  player.on('playing', function(event) {
      // console.log('playing', event, event.target.player.currentTime(), parseInt(event.target.player.currentTime(), 10));
      isPlayingSet('playFromStart');
      if (!isCapturingStreamPosition) {
        isCapturingStreamPosition = true;
        strempositionInterval = setInterval(function() {
          if (parseInt(event.target.player.currentTime(), 10) === 0) {
            setStreamPosition('START');
          } else {
            // console.log('streamPositionTimer',event.target.player.currentTime());
            setStreamPosition('PLAYING');
          }
        }, streamPositionTimer);
      }
      $continueWatchingBlock.hide();
  });
  player.on('pause', function(event) {
      // console.log('pause', event, event.target.player.currentTime(), parseInt(event.target.player.currentTime(), 10));
      clearInterval(strempositionInterval);
      isCapturingStreamPosition = false;
      setStreamPosition('PAUSE');
      if (showingExtBtn) {
        $continueWatchingBlock.show();
        $playNextEpisodeBlock.hide();
      } else {
        showingExtBtn = true;
      }
  });
  player.on('ended', function(event) {
      // console.log('ended', event, event.target.player.currentTime(), parseInt(event.target.player.currentTime(), 10));
      setStreamPosition('STOP');
      $continueWatchingBlock.hide();
      $playNextEpisodeBlock.show();
  });
  player.on('timeupdate', function(event) {
      // console.log('timeupdate', event, event.target.player.currentTime(), parseInt(event.target.player.currentTime(), 10));
      if (!player.paused()) {
        var showNextEpisode = player.currentTime() >= player.duration() - 45,
            goToNextEpisode = player.currentTime() >= player.duration() - 1;
            
        if (showNextEpisode && !showingNextEpisodePrompt) {
          if (typeof $overlayCloned === 'undefined') {
            $overlayCloned = $overlay.clone();
          }
          if (typeof $overlayCloned[0] !== 'undefined') {
            $overlayCloned.show();
            player.el().appendChild($overlayCloned[0]);
            showingNextEpisodePrompt = true;
            console.log('showing nextEpisode prompt!');
          }
        } else if (!showNextEpisode && showingNextEpisodePrompt) {
          player.el().removeChild($overlayCloned[0]);
          showingNextEpisodePrompt = false;
          console.log('removing nextEpisode prompt!');
        }
        if (showingNextEpisodePrompt && goToNextEpisode) {
          var linkToNextEpisode = $overlayCloned.find('a'),
              nextEpisodeURL = (linkToNextEpisode.length  === 1) ? linkToNextEpisode.attr('href') : false;
          if (nextEpisodeURL) {
            player.pause();
            goingToNextEpisode(nextEpisodeURL);
          }
        }
      }
  });
    // player.on('seeking', function(event) {
    //   console.log('event', seeking);
    // });
  player.on('seeked', function(event) {
      // console.log('seeked', event, event.target.player.currentTime(), event.target.player, parseInt(event.target.player.currentTime(), 10));
      if (event.target.player.hasStarted()) {
        setStreamPosition('PLAYING');
      }
  });

  var setStreamPosition = function(lastKnownAction) {
    jQuery.ajax({
      type: 'POST',
      url: episode_object.ajax_url,
      data: {
        action: 'streamposition',
        token: episode_object.token,
        Position: parseInt(player.currentTime(), 10),
        EpisodeID: episodeId,
        LastKnownAction: lastKnownAction
      }
    });
  };

  var onBeforeUnLoadEvent = false;
  window.onunload = window.onbeforeunload = function() {
  if (!onBeforeUnLoadEvent) {
    onBeforeUnLoadEvent = true;
    setStreamPosition('STOP');
    return null;
    }
  };
};

jQuery('#continueWatching, #brightcove-episode-player')
  .on('click', '.js-play-start', function() {
    docCookies.setItem('playerOption', 'playFromStart', setEndCookieTimeMinutes());
  })
  .on('click', '.js-play-resume', function() {
    docCookies.setItem('playerOption', 'playResume', setEndCookieTimeMinutes());
  });

jQuery('.js-player-start').on('click', function(e) {
  e.preventDefault();
  scrollToPlayer(this);
  docCookies.setItem('playerOption', 'playFromStart', setEndCookieTimeMinutes());
  var player = videojs('brightcove-episode-player')
  player.currentTime(0)
  player.play();
});

jQuery('.js-player-resume').on('click', function(e) {
  e.preventDefault();
  scrollToPlayer(this);
  videojs('brightcove-episode-player').play();
});

jQuery('.js-play-next').on('click', function(e) {
  goingToNextEpisode(jQuery(this).data('next'));
});

jQuery('.js-go-to').on('click', function(e) {
  window.location = jQuery(this).data('url');
});

jQuery('#brightcove-episode-player').on(
  'click',
  '.js-episode-loading',
  function(e) {
    e.preventDefault();
    jQuery('.imgOverlayContainer #play-episodes').hide();
    jQuery('.imgOverlayContainer .loading').show();
    goingToNextEpisode(jQuery(this).attr('href'));
    setTimeout(function() {
      jQuery('.imgOverlayContainer #play-episodes').show();
      jQuery('.imgOverlayContainer .loading').hide();
    }, 60000);
  }
);
var scrollToPlayer = function(elm) {
  if (typeof jQuery(elm).data('no-scroll') === 'undefined') {
    var position = jQuery('#episode').position();
    window.scroll(0, position.top);
  }
};

(function($) {
  $(document).ready(function() {
    $('.acorntv-slogan button.js-play').on('click', function(e) {
      e.preventDefault();
        $('#brightcove-episode-trailer-player').removeClass('hidden');
        var player = videojs('brightcove-episode-trailer-player');
        player.play();
    });
  });
})(jQuery);
