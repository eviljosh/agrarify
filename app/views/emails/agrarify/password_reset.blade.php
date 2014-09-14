<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="utf-8">
  </head>
  <body>
    <h2 style="color:#194719;">{{ Config::get('agrarify.app_name') }} Password Reset</h2>

    <p>
      We received a request to reset the password for this email address.
      If you did not make this request, we apologize for the inconvenience.
    </p>
    <p>
      Your new password is {{{ $password }}}. You can change this in the settings section of your
      {{ Config::get('agrarify.app_name') }} mobile app.
    </p>
  </body>
</html>
