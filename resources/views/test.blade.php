<!doctype html>
<html>
<head>
<title> Test laravel</title>
</head>
<body>
<div>
<h1> test page </h1>
<div>
<form action="webhook" method="post">
    <input type="text" name="name" value="vasjya">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="submit" value="save">
</form>
</div>
</div>
</body>
</html>
