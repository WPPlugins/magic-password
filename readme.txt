=== Magic Password ===
Contributors: 2fas
Tags: passwordless, password, 2fa, authentication, verification, passwordless wordpress, passwordless authentication, security, token, otp, totp, login, magicpassword
Requires at least: 4.2
Tested up to: 4.8
Stable tag: 1.0.1
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Magic Password is a free security plugin, which allows you to log in by scanning QR code. It’s simple, quick, and highly secure - like magic!

== Description ==

Forget your pass****.

Magic Password is a free security plugin, which allows you to log in to your WordPress website without providing username and password. Just open our mobile application (iOS or Android), scan QR code on your screen, and it’s done. It’s simple, quick, and highly secure - like magic!

We use state-of-the-art hash-based message authentication codes to make sure that the log in process is secure. Under the hood, a cryptographic hash function combines a secret key with current timestamp to generate a unique code every 30 seconds. On top of that, we use end-to-end (e2e) encryption and do not store personal identifiable information (PII) about you or your users.

No registration required. Just download the plugin with our companion mobile app, pair your device, and you are ready to go!

Before installing this plugin, please make sure you don’t use any other plugins which modify log in process.

For more information please check out our website at [https://magicpassword.io](https://magicpassword.io)
If you need our support please contact us at support@magicpassword.io

Magic Password Android App: [https://play.google.com/store/apps/details?id=io.magicpassword](https://play.google.com/store/apps/details?id=io.magicpassword)
Magic Password iOS App: [https://itunes.apple.com/us/app/magic-password-forget-your-password/id1240404220?mt=8](https://itunes.apple.com/us/app/magic-password-forget-your-password/id1240404220?mt=8)

Note that although we do not require any registration we do use third party services in order to make this plugin work:

- [https://2fas.com](https://2fas.com) - for an authentication requests and communication with a mobile app
- [https://pusher.com](https://pusher.com) - for a realtime feedback in a browser

We put very strong emphasis on security and privacy, thus we don’t send or store any personal identifiable information (which includes not sending any e-mail addresses).

== Installation ==

1. From the "Plugins" menu search for "Magic Password", click "Install Now" and then "Activate".
2. Choose Magic Password from menu, download our mobile app.
3. Scan the Magic Code through our app.
4. That's it!

**Plugin requirements**:

- PHP 5.3.3 or newer
- WordPress 4.2 or newer
- cURL extension
- JavaScript enabled

If you have any problems with the installation, please contact us at support@magicpassword.io

== Frequently Asked Questions ==

= What do I need to start using Magic Password? =
All you need to do is to download mobile app on your smartphone. Currently we support only iOS and Android systems.

iOS: [https://itunes.apple.com/us/app/magic-password-forget-your-password/id1240404220?mt=8](https://itunes.apple.com/us/app/magic-password-forget-your-password/id1240404220?mt=8)
Android: [https://play.google.com/store/apps/details?id=io.magicpassword](https://play.google.com/store/apps/details?id=io.magicpassword)

= Is it really safe? =
It might looks too easy to be secure, but in fact we worked really hard to create this service. Please note that we don’t keep any sensitive data (i.e. login or password) on our side. Additionally we pass whole communication through multilevel encryption system.

= Will it always be free? =
Yes! Magic Password will always be free.

== Screenshots ==

1. All you need to configure the plugin is to scan the QR code.
2. Application has been paired with WordPress account.
3. Users can disable log in process through username and password.
4. Logging in with Magic Password.

== Changelog ==

= 1.0.1 (June 13, 2017) =
* Fixed an issue with WP in subdirectory
* Magic Code as a default method to log in
* Minor frontend improvements and bug fixes

= 1.0.0 (May 29, 2017) =
* The first stable release of the plugin