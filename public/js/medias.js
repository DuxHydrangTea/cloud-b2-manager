const mediaGrid = document.getElementById('mediaGrid');
const mediaPaginate = document.getElementById('media-paginate');
const startUploadButton = document.getElementById('startUpload');
const fileInput = document.getElementById('fileInput');
const labelFileName = document.getElementById('fileName');
let countPage = 0;
const activeClasses = 'active'
const loadingMedias = document.getElementById('loadingMedias');
const previewImage = document.getElementById('preview-image');
const loadingIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="16" stroke-dashoffset="16" d="M12 3c4.97 0 9 4.03 9 9"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.3s" values="16;0"/><animateTransform attributeName="transform" dur="1.5s" repeatCount="indefinite" type="rotate" values="0 12 12;360 12 12"/></path><path stroke-dasharray="64" stroke-dashoffset="64" stroke-opacity="0.3" d="M12 3c4.97 0 9 4.03 9 9c0 4.97 -4.03 9 -9 9c-4.97 0 -9 -4.03 -9 -9c0 -4.97 4.03 -9 9 -9Z"><animate fill="freeze" attributeName="stroke-dashoffset" dur="1.2s" values="64;0"/></path></g></svg>
        `
const resumable = new Resumable({
    headers: {
        'X-CSRF-TOKEN': window.csrf_token,
    },
    target: window.route('chunkUpload'),
    chunkSize: 5 * 1024 * 1024,
    simultaneousUploads: 3,
    testChunks: false,
    throttleProgressCallbacks: 1
});

resumable.assignBrowse(fileInput);

resumable.on('fileAdded', function (file) {
    labelFileName.innerText = file.fileName;
});

startUploadButton.addEventListener('click', function () {
    if(resumable.files.length === 0){
        alert('Vui lòng chọn file!');
        return;
    }
    this.disabled = true;
    startUploadButton.innerHTML = loadingIcon;
    resumable.upload();
});

resumable.on('fileProgress', function (file) {
    let progress = Math.floor(file.progress() * 100);

    if(progress === 100){
        resumable.cancel();
        fileInput.value = '';
        labelFileName.innerText = 'Chọn file';
        startUploadButton.disabled = false;
        startUploadButton.innerHTML = ' <i class="fas fa-upload mr-2"></i> Tải lên ';
        fetchMedia();
    }
});

const fetchMedia = (page = 1) => {
    loadingMedias.classList.remove('hidden');
    mediaGrid.classList.add('hidden')
    axios.get(window.route('apiGetAll'), {
        params: {
            page: page,
            per_page: 30
        }
    }).then(res => {
        const medias = res.data.data;
        const pagination = {
            currentPage: res.data.current_page,
            lastPage: res.data.last_page,
            total: res.data.total
        };

        if (!countPage) {
            countPage = pagination.lastPage;
            loadPaginate(pagination);
        }
        loadMedias(medias);
        loadingMedias.classList.add('hidden')
        mediaGrid.classList.remove('hidden')
    })
}

const loadMedias = (medias) => {
    let itemHtml = ``;
    for (let i = 0; i <= medias.length; i++) {
        itemHtml += templateItemHtml(medias[i]);
    }

    mediaGrid.innerHTML = itemHtml
}

const loadPaginate = () => {
    let pagianteHtml = ``;

    for (let i = 1; i <= countPage; i++) {
        pagianteHtml += templatePageHtml(i);
    }

    mediaPaginate.innerHTML = pagianteHtml;
}

const templateItemHtml = (item) => {
    if (item)
        return `
                    <div class="media-item transition-all duration-500 relative bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <form action="${window.route('deleteFile')}" method="post" class="hidden delete-form">
                            <input hidden name="method" value="PUT" />
                            <input hidden name="_token" value="${window.csrf_token}" />
                            <input hidden name="id" value="${item.id}" />
                        </form>
                        <img src="${item.url}" alt="${item.file_name}" class="w-full h-24 object-cover media-item-img">
                        <div class="p-2">
                            <p class="text-xs text-gray-600 truncate">${item.file_name}</p>
                        </div>
                        <div class="media-actions absolute top-1 right-1 flex space-x-1">
                            <a class="download-btn w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-blue-600 transition-colors" href="${ window.route('download', {path: item.file_name}) }">
                                <i class="fas fa-download"></i>
                            </a>
                            <button class="delete-btn w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 transition-colors" data-id="${item.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `
    return '';
}

const templatePageHtml = (item) => {

    return `
                <li> <button data-page=${item} class="${item === 1 ? activeClasses : ''} paginate-button border-2 border-dashed flex items-center justify-center w-[30px] h-[30px] rounded hover:bg-blue-400 hover:border-blue-600 hover:text-white duration-100">${item}</button> </li>
            `;
}

mediaPaginate.addEventListener('click', function (event) {

    const button = event.target.closest('.paginate-button');

    this.querySelectorAll('.paginate-button').forEach( el => el.classList.remove(activeClasses))

    if (button && !button.classList.contains(activeClasses)) {
        const pageNumber = button.getAttribute('data-page');
        fetchMedia(pageNumber);
        button.classList.add(activeClasses);
    }
});

mediaGrid.addEventListener('click', function (event) {
    handleDeleteItem(event.target.closest('.delete-btn'));
    handlePreviewItem(event.target.closest('.media-item'));
});

const handleDeleteItem = (button) => {
    if(button === null)
        return;
    button.disabled = true;
    button.innerHTML = loadingIcon;
    const mediaItem = button.closest('.media-item');
    const id = button.getAttribute('data-id');

    axios.delete(window.route('deleteFile'),{
        params: {
            _token: window.csrf_token,
            id: id
        }
    }).then(res => {
        if(res.data){
            mediaItem.classList.add('scale-[0]');

            setTimeout(function(){
                mediaItem.remove();
            }, 500);
        }
    });

}

const handlePreviewItem = (mediaItem) => {
    if(mediaItem === null)
        return;
    const mediaItemImg = mediaItem.querySelector('.media-item-img');
    previewImage.src = mediaItemImg.getAttribute('src');
}

fetchMedia();
