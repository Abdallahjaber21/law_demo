<?php

use yii\base\View;
use yii\helpers\Url;

/* @var $this View */
$asset = Yii::$app->getAssetManager()->publish("@static/js/notification");
?>

<script src="https://www.gstatic.com/firebasejs/6.5.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/6.5.0/firebase-messaging.js"></script>
<script>
  // Your web app's Firebase configuration
  var firebaseConfig = <?= yii\helpers\Json::encode(Yii::$app->params['firebaseConfig']) ?>;
  // Initialize Firebase
  firebase.initializeApp(firebaseConfig);
</script>
<script type="text/javascript">
<?php ob_start() ?>
  function sendTokenToServer(currentToken) {
    $.ajax({
      url: "<?= Url::to(['/notification/update-regid']) ?>",
      type: "POST",
      data: {regid: currentToken},
      complete: function (a, b, c) {
        console.log(a, b, c);
      }
    })
  }
  // Retrieve Firebase Messaging object.
  const messaging = firebase.messaging();
  navigator.serviceWorker.register('<?= $asset[1] ?>/sw.js')
          .then(function (registration) {
            messaging.useServiceWorker(registration);
            // Add the public key generated from the console here.
            messaging.usePublicVapidKey("<?= Yii::$app->params['publicVapidKey'] ?>");
            messaging.requestPermission()
                    .then(function () {
                      console.log('Notification permission granted.');
                      messaging.getToken()
                              .then(function (currentToken) {
                                if (currentToken) {
                                  console.log(currentToken);
                                  sendTokenToServer(currentToken);
                                  //updateUIForPushEnabled(currentToken);
                                } else {
                                  // Show permission request.
                                  console.log('No Instance ID token available. Request permission to generate one.');
                                  // Show permission UI.
                                  //updateUIForPushPermissionRequired();
                                  //setTokenSentToServer(false);
                                  sendTokenToServer(null);
                                }
                              })
                              .catch(function (err) {
                                console.log('An error occurred while retrieving token. ', err);
                                //showToken('Error retrieving Instance ID token. ', err);
                                //setTokenSentToServer(false);
                              });
                    })
                    .catch(function (err) {
                      console.log('Unable to get permission to notify.', err);
                      sendTokenToServer(null);
                    });
            messaging.onTokenRefresh(function () {
              messaging.getToken()
                      .then(function (refreshedToken) {
                        console.log('Token refreshed.');
                        // Indicate that the new Instance ID token has not yet been sent to the
                        // app server.
                        //setTokenSentToServer(false);
                        // Send Instance ID token to app server.
                        sendTokenToServer(refreshedToken);
                        // ...
                      })
                      .catch(function (err) {
                        console.log('Unable to retrieve refreshed token ', err);
                        //showToken('Unable to retrieve refreshed token ', err);
                      });
            });
            // Handle incoming messages. Called when:
            // - a message is received while the app has focus
            // - the user clicks on an app notification created by a sevice worker
            //   `messaging.setBackgroundMessageHandler` handler.
            messaging.onMessage(function (payload) {
              console.log("Message received. ", payload);
              // Customize notification here
              const notificationTitle = payload['notification']['title'];
              const notificationOptions = {
                body: payload['notification']['body'],
                icon: payload['notification']['icon'],
                //click_action: payload['notification']['click_action']
              };
              toastr["success"](payload['notification']['title'], payload['notification']['body'], {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-bottom-right",
                "preventDuplicates": false,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "0",
                "extendedTimeOut": "60000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut",
                onclick: function () {
                  window.open(payload['notification']['click_action'], '_blank');
                  //console.log('clicked');
                }
              });
//              return registration.showNotification(notificationTitle,
//                      notificationOptions);
              // ...
            });
          });
<?php $js = ob_get_clean() ?>
<?php $this->registerJs($js) ?>
</script>
