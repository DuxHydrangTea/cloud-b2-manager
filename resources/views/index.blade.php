<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/resumablejs@1.1.0/resumable.min.js"></script>
</head>
<body>
    <form action="{{ route('handlePost') }}" enctype="multipart/form-data" method="post" class="mx-auto mt-24 max-w-[500px]">
        @csrf
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white" for="multiple_files">Upload multiple files</label>
        <input name="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="file" type="file">
        <button type="submit" class=" mt-4 focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Submit</button>
    </form>
    <button id="startUpload" disabled
    class=" mt-4 focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
    >Upload</button>
    <div class="progress mt-3 border-2" style="display:none;">
        <div class="progress-bar bg-red-400" role="progressbar" style="width:0%;">0%</div>
    </div>
    <h2 id="fileName">Filename</h2>

    <script>
        let r = new Resumable({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            target: `{{ route('chunkUpload') }}`,
            chunkSize: 1 * 1024 * 1024, // 1MB
            simultaneousUploads: 3,
            testChunks: false,
            throttleProgressCallbacks: 1
        });

        r.assignBrowse(document.getElementById('file'));

        r.on('fileAdded', function(file) {
            document.getElementById('startUpload').disabled = false;
            let fileNameDisplay = document.getElementById('fileName');
            fileNameDisplay.innerText = file.fileName;
        });

        // Khi bấm nút upload
        document.getElementById('startUpload').addEventListener('click', function() {
            r.upload();
            document.querySelector('.progress').style.display = 'block';
        });

        r.on('fileProgress', function(file) {
            let progress = Math.floor(file.progress() * 100);
            document.querySelector('.progress-bar').style.width = progress + '%';
            document.querySelector('.progress-bar').innerText = progress + '%';
        });

    </script>
</body>
</html>
