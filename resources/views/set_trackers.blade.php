<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
    </body>
    <script type="text/javascript">
        localStorage.setItem("trackers", '<?=json_encode($options)?>');
        document.location = "//<?=config('app.app_client_domain')?>";
    </script>
</html>
