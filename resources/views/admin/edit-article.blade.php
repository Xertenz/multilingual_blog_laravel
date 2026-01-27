<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit article</title>
</head>
<body>
    <form action="{{route('articles.update', $article->id)}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div>
            <span>category</span>
            <select name="category_id" id="category">
                @foreach ($cats as $cat)
                <option value="{{$cat['category_id']}}" {{$cat->category_id == $article->category_id ? 'selected' : ''}}>{{$cat['name']}}</option>
                @endforeach($cats as $cat)
            </select>
            @error('category')
                <span>{{$message}}</span>
            @enderror
        </div>
        <div>
            <span>feature image</span>
            <input type="file" name="featured_img" >
            @error('featured_img')
                <span>{{$message}}</span>
            @enderror
        </div>
        <div>
            <button>update article</button>
        </div>
    </form>
    <br />
    <br />
    <br />
    <a href="{{route('articles.index')}}">
        <button>show article</button>
    </a>
</body>
</html>