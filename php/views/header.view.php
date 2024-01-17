<head>
    <meta charset="UTF-8">
    <IF isset($title)>
        <title>{{{$title}}}</title>
    <ELSE/>
        <title>PHP Project</title>
    </IF>
    <!-- <link rel="stylesheet" href="/css/style.css" /> TODO style website -->
    <IF isset($recaptcha)>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </IF>
    <MIXIN_POINT/>
</head>
