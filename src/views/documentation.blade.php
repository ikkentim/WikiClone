<!DOCTYPE html>
<html lang="en">
{{-- This is the default, Bootstrap based, WikiClone documentation view. --}}
{{-- You should customize this page to your own liking. --}}
{{-- 5 variables are passed to this view, $title, $fileName, $content, $sidebar and $footer --}}
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - WikiClone</title>

    <link rel="shortcut icon" href="/favicon.ico">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

    <style>
        body {
            font-family: Roboto;
            background-color: #f0f0f0;
        }

        .content {
            background-color: #fefefe;
        }

        .pad-top {
            padding-top: 15px;
        }

        a.btn.btn-link {
            color: #8f8f8f;
        }

        a.btn.btn-link:hover {
            color: #afafaf;
        }

        a.title {
            text-decoration: none;
        }
        a.title h2 {
            color: black;
        }

        .navbar {
            margin-bottom: 0;
        }

        footer.footer {
            padding: 25px 0 150px;
            color: #777;
            font-style: italic;
        }
    </style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">WikiClone</a>
        </div>

        <div class="collapse navbar-collapse" id="navbar">
            <ul class="nav navbar-nav">
                <li>
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li>
                    <a href="{{\Ikkentim\WikiClone\GitHubUrls::getRepositoryURL(config('wikiclone.repository')) }}">Github</a>
                </li>
                <li>
                    <a href="{{ \Ikkentim\WikiClone\GitHubUrls::getReleasesURL(config('wikiclone.repository')) }}">Download</a>
                </li>
            </ul>

            @if (isset($tags) && count($tags) > 1)
            <ul class="nav navbar-nav navbar-right">
               <li class="dropdown">
                  <a href="#" class="dropdown-toggle" id="version-dropdown" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> {{ $tag }} <span class="caret"></span> </a> 
                  <ul class="dropdown-menu" aria-labelledby="version-dropdown">
                     @foreach ($tags as $value)
                     <li><a href="{{url(config('wikiclone.url_prefix') . "/$value/$fileName")}}">{{ $value }}</a></li>
                     @endforeach
                  </ul>
               </li>
            </ul>
            @endif
        </div>
    </div>
</nav>

<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-3 sidebar">
                {!! $sidebar !!}
            </div>
            <div class="col-sm-9 pad-top">
                <div class="page-header">
                    <h1>
                        {{ $title }}
                        @if($editUrl !== null)
                            <small>
                                <a class="btn btn-link"
                                   href="{{ $editUrl }}">
                                    <i class="fa fa-github"></i> Edit this page on GitHub
                                </a>
                            </small>
                        @endif
                    </h1>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        {!! $content !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                {!! $footer !!}
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>
