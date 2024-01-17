<head>
    <meta charset="UTF-8">
    <IF isset($title)>
        <title>{{{$title}}}</title>
    <ELSE/>
        <title>PHP Project</title>
    </IF>
    <link rel="icon" href="object book.svg">
    <!-- Source: https://github.com/apancik/public-domain-icons/blob/master/dist/object%20book.svg -->
    <IF isset($recaptcha)>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </IF>
    <MIXIN_POINT/>
</head>
