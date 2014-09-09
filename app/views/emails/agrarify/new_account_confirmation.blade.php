<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="utf-8">
  </head>
  <body>
    <h2 style="color:#194719;">Welcome to {{ Config::get('agrarify.app_name') }}</h2>

    <p>
      To verify your email address, please visit: @{{ URL::to('password/reset', array($token)) }}.
    </p>
  </body>
</html>
