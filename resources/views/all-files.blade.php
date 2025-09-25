<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Media Files</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/resumablejs@1.1.0/resumable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        window.csrf_token = '{{ csrf_token() }}'
    </script>
    <style>
        .media-item {
            transition: all 0.2s ease;
        }
        .media-item:hover {
            transform: scale(1.05);
        }
        .media-actions {
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .media-item:hover .media-actions {
            opacity: 1;
        }
        .active {
            background-color: rgb(38, 100, 235);
            color: white;
        }
    </style>
    @routes
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-[400px]  bg-white h-[400px] mr-2 rounded shadow p-2">
        <img src="{{ $files }}" width="100%" alt="" srcset="" class="rounded" id="preview-image">
    </div>
    <div class="bg-white rounded-xl shadow-lg w-full max-w-6xl flex flex-col md:flex-row h-[80vh]">
        <div class="md:w-3/4 p-6 overflow-y-auto flex flex-col justify-between">
            <div class="">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Thư viện Media</h2>
                <div class="w-full h-full flex justify-center items-center hidden" id="loadingMedias">
                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24"><circle cx="12" cy="2" r="0" fill="currentColor"><animate attributeName="r" begin="0" calcMode="spline" dur="1s" keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8" repeatCount="indefinite" values="0;2;0;0"/></circle><circle cx="12" cy="2" r="0" fill="currentColor" transform="rotate(45 12 12)"><animate attributeName="r" begin="0.125s" calcMode="spline" dur="1s" keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8" repeatCount="indefinite" values="0;2;0;0"/></circle><circle cx="12" cy="2" r="0" fill="currentColor" transform="rotate(90 12 12)"><animate attributeName="r" begin="0.25s" calcMode="spline" dur="1s" keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8" repeatCount="indefinite" values="0;2;0;0"/></circle><circle cx="12" cy="2" r="0" fill="currentColor" transform="rotate(135 12 12)"><animate attributeName="r" begin="0.375s" calcMode="spline" dur="1s" keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8" repeatCount="indefinite" values="0;2;0;0"/></circle><circle cx="12" cy="2" r="0" fill="currentColor" transform="rotate(180 12 12)"><animate attributeName="r" begin="0.5s" calcMode="spline" dur="1s" keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8" repeatCount="indefinite" values="0;2;0;0"/></circle><circle cx="12" cy="2" r="0" fill="currentColor" transform="rotate(225 12 12)"><animate attributeName="r" begin="0.625s" calcMode="spline" dur="1s" keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8" repeatCount="indefinite" values="0;2;0;0"/></circle><circle cx="12" cy="2" r="0" fill="currentColor" transform="rotate(270 12 12)"><animate attributeName="r" begin="0.75s" calcMode="spline" dur="1s" keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8" repeatCount="indefinite" values="0;2;0;0"/></circle><circle cx="12" cy="2" r="0" fill="currentColor" transform="rotate(315 12 12)"><animate attributeName="r" begin="0.875s" calcMode="spline" dur="1s" keySplines="0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8;0.2 0.2 0.4 0.8" repeatCount="indefinite" values="0;2;0;0"/></circle></svg>
                </div>
                <div id="mediaGrid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-4">
                </div>
            </div>
            <div class="">
                <ul class="mx-auto w-fit flex gap-2 p-1 mt-2 rounded bg-white border-2 border-gray-200" id="media-paginate">
                </ul>
            </div>
        </div>

        <div class="md:w-1/4 bg-gray-50 p-6 border-l border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-4" >Tải lên</h2>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2" id="fileName">Chọn file</label>
                <div class="flex items-center justify-center w-full">
                    <label for="fileInput" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-500">Kéo thả file hoặc click để chọn</p>
                        </div>

                        <input id="fileInput" type="file" class="hidden" multiple accept=".jpg,.jpeg,.png,.gif,.webp">
                    </label>
                </div>
            </div>

            <button id="startUpload" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors flex items-center justify-center">
                <i class="fas fa-upload mr-2"></i>
                Tải lên
            </button>

            <div class="mt-6 text-sm text-gray-600">
                <p class="mb-2"><i class="fas fa-info-circle mr-2"></i>Hỗ trợ: JPG, PNG, GIF</p>
                <p><i class="fas fa-desktop mr-2"></i>Kích thước: 100x100px</p>
            </div>

            <button id="test-time-upload" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors flex items-center justify-center">
                Upload
            </button>
        </div>
    </div>
    <script>
        $('#test-time-upload').on('click', function () {
            const startTime = Date.now();
            timer = setInterval(() => {
                const elapsed = ((Date.now() - startTime) / 1000).toFixed(1);
                console.log(`⏱️ Đang upload... ${elapsed} giây`);
                $('#test-time-upload').text(`Đang upload... ${elapsed} giây`);
            }, 1000); // Cập nhật mỗi giây
            axios.get(window.route('res'))
                .then(response => {
                    clearInterval(timer);
                    const duration = ((Date.now() - startTime) / 1000).toFixed(2);
                    console.log(`✅ Upload hoàn tất sau ${duration} giây`);
                    $('#test-time-upload').text(`Upload hoàn tất sau ${duration} giây`);
                })
                .catch(error => {
                    clearInterval(timer);
                    console.error('❌ Upload lỗi:', error);
                    $('#test-time-upload').text('Upload lỗi. Thử lại');
            });
        });
    </script>
    <script src="{{ asset('js/medias.js') }}"></script>
</body>
</html>
