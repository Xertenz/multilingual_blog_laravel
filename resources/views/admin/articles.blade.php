<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>All Articles</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th>#id</th>
                <th>catId#</th>
                <th>featured img</th>
                <th>main article actions</th>
                <th>translations</th>
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
                        <form action="{{route('articles.destroy', $article->id)}}" method="POST">
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
                            <form action="{{route('articles-trans.destroy', $article_translation->id)}}" method="POST">
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
            </tr>
            @endforeach
        </tbody>
    </table>
    <br />
    <a href="{{route('articles.create')}}">
        <button>add article</button>
    </a>
</body>
</html>