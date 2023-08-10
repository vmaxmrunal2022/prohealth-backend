<html>
<header>
    <title>This is file upload</title>
</header>

<body>
    <form action='{{route("storeFile")}}' method="POST" enctype="multipart/form-data">
        @csrf
        This is an example of file upload</br>
        <input type="file" name="file">
        <button> submit </button>
    </form>
</body>

</html>