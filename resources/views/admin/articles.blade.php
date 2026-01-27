<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>All Articles</title>
    <link rel="stylesheet" href="{{asset('css/components/article-gallery.css')}}">
</head>
<body>
    
    <table border="1" class="articles-table">
        <thead>
            <tr>
                <th>#id</th>
                <th>catId#</th>
                <th>featured img</th>
                <th>main article actions</th>
                <th>translations</th>
                <th>article gallery</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($articles as $article)
            <tr>
                <td>{{$article->id}}</td>
                <td>{{$article->category_id}}</td>
                <td>{{$article->featured_img}}</td>
                <td>
                    <div style="display: flex; gap: 1rem">
                        <a href="{{route('articles.edit', $article->id)}}"><button>Edit Main Article</button></a>
                        <form action="{{route('articles.destroy', $article->id)}}" method="POST" onsubmit="return confirm('Are you sure you want to delete this main article with all it\'s translated articles?');">
                            @csrf
                            @method('DELETE')
                            <button>Delete Main Article</button>
                        </form>
                    </div>
                </td>
                <td>
                    <div style="display: flex; flex-direction:row; gap:1rem;">
                    @foreach ($article->translations as $article_translation)
                        <div style="display: flex; flex-direction:column">
                            <a href="{{route('articles-trans.edit', $article_translation->id)}}"><button>Edit {{$article_translation->language_code}}</button></a>
                            <form action="{{route('articles-trans.destroy', $article_translation->id)}}" method="POST" onsubmit="return confirm('Are you sure you want to delete this article translation?');"  >
                                @csrf
                                @method('DELETE')
                                <button>Delete {{$article_translation->language_code}}</button>
                            </form>
                        </div>
                    @endforeach
                    <div style="display: flex; flex-direction:column">
                        <a href="{{route('articles-trans.create', $article->id)}}"><button>Add trans</button></a>
                    </div>
                    </div>
                </td>
                <td>
                    <button class="show-gallery" data-article-id="{{$article->id}}"><span>show gallery</span> <span class="artilce-content_imgs-count">{{$article->media()->count()}}</span></button>
                        {{ $article->getFirstMedia('content_imgs') }}

                    @php
                        $media = $article->getFirstMedia('content_imgs');
                        //dd($media);
                    @endphp
                    
                    @if($media && $media->hasResponsiveImages())
                        <img 
                            src="{{ $media->getUrl() }}" 
                            srcset="{{ $media->getSrcset() }}" 
                            sizes="(max-width: 768px) 100vw, 50vw"
                            alt="{{ $article->title }}"
                            class="img-fluid"
                            loading="lazy"
                        >
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br />
    <a href="{{route('articles.create')}}">
        <button class="add-article" id="add-article">add article</button>
    </a>
    <script>
        // Define a global variable to hold the route URL
        window.upload_url = "{{ route('articles.upload.content') }}";
        window.article_content_imgs_url = "{{ route('articles.get.content') }}";
        window.delete_content_img_url = "{{ route('articles.delete.content_img') }}"
    </script>
    <script type="text/javascript" src="{{asset('js/components/article-gallery.js')}}" defer></script>
</body>
</html>