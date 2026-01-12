<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit article</title>
</head>
<body>
    <form action="{{route('articles-trans.update', $article->id)}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @error('language_exists')
            <h1>{{$message}}</h1>
        @enderror
        <div>
            <select name="language_code" id="">
                <option value="ar" {{$article->language_code == 'ar' ? 'selected' : ''}}>Arabic</option>
                <option value="en" {{$article->language_code == 'en' ? 'selected' : ''}}>English</option>
            </select>
            @error('lang')
                <span>{{$message}}</span>
            @enderror
        </div>
        <div>
            <span>slug</span>
            <input type="text" name="slug" name="slug" value="{{$article->slug}}">
            @error('slug')
                <span>{{$message}}</span>
            @enderror
        </div>
        <div>
            <span>title</span>
            <input type="text" name="title" name="title" value="{{$article->title}}">
            @error('title')
                <span>{{$message}}</span>
            @enderror
        </div>
        <div>
            <span>description</span>
            <textarea name="description" id="description" cols="30" rows="10">{{$article->description}}</textarea>
            @error('description')
                <span>{{$message}}</span>
            @enderror
        </div>
        <div>
            <span>meta title</span>
            <input type="text" name="meta_title" name="meta_title" value="{{$article->meta_title}}">
            @error('meta_title')
                <span>{{$message}}</span>
            @enderror
        </div>
        <div>
            <span>meta description</span>
            <textarea name="meta_description" id="meta_description" cols="30" rows="10">{{$article->meta_description}}</textarea>
            @error('meta_description')
                <span>{{$message}}</span>
            @enderror
        </div>
        <input type="hidden" name="article_id" value="{{$article->article_id}}" />
        <div>
            <button>update article</button>
        </div>
    </form>

    <br />
    <br />
    <br />
    <a href="{{route('articles-trans.index')}}">
        <button>show article trans</button>
    </a>
</body>
</html>