<head>
    <meta charset="UTF-8">
    <title>{{{$title}}}</title>
    <link rel="stylesheet" href="css/style.css">
    ###IF(isset($recaptcha))###
        {{{$recaptcha}}}
    ###ENDIF###
    ###MIXIN_POINT###
</head>
