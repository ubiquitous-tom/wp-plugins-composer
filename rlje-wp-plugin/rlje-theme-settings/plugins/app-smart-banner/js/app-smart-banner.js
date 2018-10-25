/**
 * Smart App Banner used for Android
 *
 * $.smartbanner({
 *  title: null, // What the title of the app should be in the banner (defaults to <title>)
 *  author: null, // What the author of the app should be in the banner (defaults to <meta name="author"> or hostname)
 *  price: 'FREE', // Price of the app
 *  appStoreLanguage: 'us', // Language code for App Store
 *  inAppStore: 'On the App Store', // Text of price for iOS
 *  inGooglePlay: 'In Google Play', // Text of price for Android
 *  inAmazonAppStore: 'In the Amazon Appstore',
 *  inWindowsStore: 'In the Windows Store', // Text of price for Windows
 *  GooglePlayParams: null, // Aditional parameters for the market
 *  icon: null, // The URL of the icon (defaults to <meta name="apple-touch-icon">)
 *  iconGloss: null, // Force gloss effect for iOS even for precomposed
 *  url: null, // The URL for the button. Keep null if you want the button to link to the app store.
 *  button: 'VIEW', // Text for the install button
 *  scale: 'auto', // Scale based on viewport size (set to 1 to disable)
 *  speedIn: 300, // Show animation speed of the banner
 *  speedOut: 400, // Close animation speed of the banner
 *  daysHidden: 15, // Duration to hide the banner after being closed (0 = always show banner)
 *  daysReminder: 90, // Duration to hide the banner after "VIEW" is clicked *separate from when the close button is clicked* (0 = always show banner)
 *  force: null, // Choose 'ios', 'android' or 'windows'. Don't do a browser check, just always show this banner
 *  hideOnInstall: true, // Hide the banner after "VIEW" is clicked.
 *  layer: false, // Display as overlay layer or slide down the page
 *  iOSUniversalApp: true, // If the iOS App is a universal app for both iPad and iPhone, display Smart Banner to iPad users, too.
 *  appendToSelector: 'body', //Append the banner to a specific selector
 *  onInstall: function() {
 *    // alert('Click install');
 *  },
 *  onClose: function() {
 *    // alert('Click close');
 *  }
 * })
 */
(function($) {
  $(document).ready(function() {
    if (!rlje_app_smart_banner) {
      return;
    }
    //Init SmartBanner
    $.smartbanner({
      title: rlje_app_smart_banner.title,
      author: rlje_app_smart_banner.author,
      price: rlje_app_smart_banner.price,
      inAppStore: rlje_app_smart_banner.in_app_store,
      inGooglePlay: rlje_app_smart_banner.in_google_play,
      icon: rlje_app_smart_banner.icon,
      button: 'OPEN'
    });

    if ($('#smartbanner').length) {
      $('html').addClass('smartBanner');
      window.onscroll = changePos;
      window.onresize = checkWindowWidth;
      changePos();

      function changePos() {
        var $navBarFixed = $('.navbar-fixed-top'),
          $sideCollapse = $('.navbar-collapse.side-collapse:not(.in)'),
          containersClasses = [
            '.feature-slide .carousel-inner',
            '.container.franchise',
            '.container.schedule',
            'section.browse',
            'section#contact-hero',
            'body>div.hero'
          ];
        if (window.pageYOffset > 78) {
          $navBarFixed.attr('style', 'position: fixed');
          $sideCollapse.attr('style', '');
          for (var i = 0; i < containersClasses.length; i++) {
            $(containersClasses[i]).removeAttr('style');
          }
        } else {
          $navBarFixed.attr('style', 'margin-bottom:0; position: relative');
          for (var i = 0; i < containersClasses.length; i++) {
            $(containersClasses[i]).attr('style', 'margin-top:0');
          }
          checkWindowWidth();
        }
      }

      function checkWindowWidth() {
        var left = '',
          height = '',
          $sideCollapse = $('.navbar-collapse.side-collapse');
        if (window.screen.width < 720) {
          left = 'left:-18px;';
        } else if (window.screen.width < 1024) {
          left = 'left:-25px;';
        }
        if ($sideCollapse.hasClass('in')) {
          height = 'height:0;';
        }
        $sideCollapse.attr(
          'style',
          'position: relative; top: -2px; display: inline-block; ' +
            left +
            height
        );
      }

      //// Fix issue in the navbar collapse when it is not open.
      $('button.navbar-toggle').bind('click', function() {
        if ($('#smartbanner').length) {
          var $sideCollapse = $('.navbar-collapse.side-collapse');
          if ($sideCollapse.hasClass('in')) {
            $sideCollapse.css('height', '0');
          } else {
            $sideCollapse.css('height', 'auto');
          }
          return false;
        }
      });
    }
  });
})(jQuery);
