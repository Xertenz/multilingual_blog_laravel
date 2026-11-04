let showGalleryBtns = document.querySelectorAll(".show-gallery");

window.closing = function (media_summary_id) {
    document.getElementById("media-gallery-wrapper").remove();
};

const upload_url = window.upload_url;
window.choosingFile = async function (fileInput) {
    const content_img = fileInput.files[0];
    const media_summary_id = fileInput.dataset.mediaSummaryId;

    const formData = new FormData();
    formData.append("content_img", content_img);
    formData.append("media_summary_id", media_summary_id); // 👈 إرسال اسم الحقل الصحيح المتوافق مع الـ Controller

    fileInput.value = null;

    let response = await fetch(upload_url, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            Accept: "application/json",
        },
        body: formData,
    });
    let data = await response.json();

    rerendering_the_gallery(media_summary_id);
};

const article_content_imgs_url = window.article_content_imgs_url;
window.getMediaContentImgs = async function (media_summary_id) {
    const formData = new FormData();
    formData.append("media_summary_id", media_summary_id); // 👈 طلب صور الملخص الحالي

    let request = await fetch(article_content_imgs_url, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
            Accept: "application/json",
        },
        body: formData,
    });
    let json = await request.json();
    return json;
};

const delete_content_img_url = window.delete_content_img_url;
window.removeImage = async function (element, media_summary_id) {
    if (!confirm("Are you sure you want to delete this image?")) {
        return;
    }
    const contentImageId = element.dataset["contentImageId"];

    const request = await fetch(delete_content_img_url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
        },
        body: JSON.stringify({ content_img_id: contentImageId }),
    });
    const data = await request.json();

    rerendering_the_gallery(media_summary_id);
};

window.rerendering_the_gallery = async function (media_summary_id) {
    let template = document.createElement("template");
    template.innerHTML = await createGalleryHTML(media_summary_id);
    let fragment = template.content;
    let showGalleryBtn = document.querySelector(
        `.show-gallery[data-media-summary-id="${media_summary_id}"]`
    );

    showGalleryBtn.after(fragment);

    const wrappers = document.getElementsByClassName("media-gallery-wrapper");
    if (wrappers.length > 1) {
        wrappers[1].remove();
    }

    let media_content_imgs = await getMediaContentImgs(media_summary_id);
    showGalleryBtn.querySelector(".media-content-imgs-count").textContent =
        media_content_imgs.length.toString();
};

window.createGalleryHTML = async function (media_summary_id) {
    let media_content_imgs = await getMediaContentImgs(media_summary_id);

    let html = `
<div class="media-gallery-wrapper fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 animate-fade-in" id="media-gallery-wrapper">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-3xl overflow-hidden flex flex-col max-h-[85vh]">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-b border-slate-200">
            <div class="flex items-center gap-2">
                <span class="text-base font-bold text-slate-800">Media Content Gallery</span>
                <span class="bg-indigo-100 text-indigo-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">${media_content_imgs.length} Images</span>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Upload Button -->
                <label for="content-img-${media_summary_id}" class="cursor-pointer inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-xs px-3 py-2 rounded-lg shadow-sm transition-all">
                    <span>+ Upload Image</span>
                </label>
                <input type="file" name="content-img" id="content-img-${media_summary_id}" class="hidden" data-media-summary-id="${media_summary_id}" onchange="choosingFile(this)" accept="image/*">
                
                <!-- Close Button -->
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-200 hover:bg-red-100 text-slate-600 hover:text-red-600 transition-colors cursor-pointer text-sm font-bold" id="close-icon" onclick="closing(${media_summary_id})">
                    &times;
                </button>
            </div>
        </div>

        <!-- Images Container -->
        <div class="p-6 overflow-y-auto flex-1 bg-slate-50/50">`;

    if (media_content_imgs.length > 0) {
        html += `<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">`;
        media_content_imgs.forEach((img) => {
            html += `
                <div class="group relative bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs aspect-square flex items-center justify-center">
                    <img src="${img.original_url}" alt="Gallery Image" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" />
                    
                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <button class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-lg shadow-md transition-transform hover:scale-110 cursor-pointer text-xs font-medium flex items-center gap-1" 
                                data-content-image-id="${img.id}" 
                                onclick="removeImage(this, ${media_summary_id})"
                                title="Delete Image">
                            🗑️ Delete
                        </button>
                    </div>
                </div>`;
        });
        html += `</div>`;
    } else {
        html += `
            <div class="flex flex-col items-center justify-center py-12 text-slate-400">
                <span class="text-4xl mb-2">🎬</span>
                <p class="text-sm font-medium">No images uploaded for this media summary yet.</p>
                <p class="text-xs text-slate-400 mt-1">Upload images to use them inside your translated content.</p>
            </div>`;
    }

    html += `
        </div>
    </div>
</div>`;
    return html;
};

showGalleryBtns.forEach(function (btn) {
    btn.addEventListener("click", async function () {
        let template = document.createElement("template");
        template.innerHTML = await createGalleryHTML(
            btn.dataset.mediaSummaryId
        );
        let fragment = template.content;
        this.after(fragment);
    });
});
