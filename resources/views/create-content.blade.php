<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="POST" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="mb-3">
                  <textarea name="desc" class="my-editor form-control" id="my-editor"></textarea>
                </div>
              </form>
        </div>
    </div>
</div> 

<script src="//cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>
<script>
  var options = {
    filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
    // filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token={{ csrf_token() }}',
    filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
    // filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token={{ csrf_token() }}'
  };
</script>
<script>
    CKEDITOR.replace('my-editor', options);
    </script>