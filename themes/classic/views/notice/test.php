<!DOCTYPE html>
<html>
<head>
    <title>Replace Textarea by Code — CKEditor Sample</title>
    <meta charset="utf-8">
    <script src="/ckeditor/ckeditor.js"></script>
    <!--引入相关js-->
    <script src="/ckeditor/ckfinder/ckfinder.js"></script>
    <script src="/ckeditor/config.js"></script>
    <!--引入ckeditor配置-->
    <link href="/ckeditor/content.css" rel="stylesheet">
</head>
<body>
<form action="sample_posteddata.php" method="post">
    <textarea cols="80" id="editor1" name="editor1" rows="10">
    </textarea>
    <script>
        CKEDITOR.replace('editor1');
    </script>
    <p>
        <input type="submit" value="Submit">
    </p>
</form>
</body>
</html>