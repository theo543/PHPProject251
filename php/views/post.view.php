<MIXIN_NEST base>
    <MIXIN header/>
    <body>
    <h1>{{{$title}}} by <a href="/?author_id={{{$author_id}}}">{{{$author_name}}}</a></h1>
    <main>
        {{{!|$compile_post()}}}
    </main>
    <hr>
    </body>
</MIXIN_NEST>
