let showGalleryBtns = document.querySelectorAll(".show-gallery");

window.closing = function (game_id) {
    document.getElementById("game-gallery-wrapper").remove();
};

const upload_url = window.upload_url;
window.choosingFile = async function (fileInput) {
    const content_img = fileInput.files[0];
    const game_id = fileInput.dataset.gameId;

    const formData = new FormData();
    formData.append("content_img", content_img);
    formData.append("game_id", game_id); // 👈 إرسال معرف اللعبة للـ Controller

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

    rerendering_the_gallery(game_id);
};

const game_content_imgs_url = window.game_content_imgs_url;
window.getGameContentImgs = async function (game_id) {
    const formData = new FormData();
    formData.append("game_id", game_id);

    let request = await fetch(game_content_imgs_url, {
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
window.removeImage = async function (element, game_id) {
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

    rerendering_the_gallery(game_id);
};

window.rerendering_the_gallery = async function (game_id) {
    let template = document.createElement("template");
    template.innerHTML = await createGalleryHTML(game_id);
    let fragment = template.content;
    let showGalleryBtn = document.querySelector(
        `.show-gallery[data-game-id="${game_id}"]`
    );
    showGalleryBtn.after(fragment);

    const wrappers = document.getElementsByClassName("game-gallery-wrapper");
    if (wrappers.length > 1) {
        wrappers[1].remove();
    }

    let game_content_imgs = await getGameContentImgs(game_id);
    showGalleryBtn.querySelector(".game-content-imgs-count").textContent =
        game_content_imgs.length.toString();
};

window.createGalleryHTML = async function (game_id) {
    let game_content_imgs = await getGameContentImgs(game_id);

    let html = `
<div class="game-gallery-wrapper fixed inset-0 z-50 flex items-center justify-center bg-slate-900/65 backdrop-blur-xs p-4 animate-fade-in" id="game-gallery-wrapper">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-3xl overflow-hidden flex flex-col max-h-[85vh]">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-b border-slate-200">
            <div class="flex items-center gap-2">
                <span class="text-base font-bold text-slate-800">Game Content Gallery</span>
                <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">${game_content_imgs.length} Images</span>
            </div>
            
            <div class="flex items-center gap-3">
                <label for="content-img-${game_id}" class="cursor-pointer inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs px-3 py-2 rounded-lg shadow-sm transition-all">
                    <span>+ Upload Image</span>
                </label>
                <input type="file" name="content-img" id="content-img-${game_id}" class="hidden" data-game-id="${game_id}" onchange="choosingFile(this)" accept="image/*">
                
                <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-200 hover:bg-red-100 text-slate-600 hover:text-red-600 transition-colors cursor-pointer text-sm font-bold" onclick="closing(${game_id})">
                    &times;
                </button>
            </div>
        </div>

        <!-- Images Grid -->
        <div class="p-6 overflow-y-auto flex-1 bg-slate-50/50">`;

    if (game_content_imgs.length > 0) {
        html += `<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">`;
        game_content_imgs.forEach((img) => {
            html += `
                <div class="group relative bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs aspect-square flex items-center justify-center">
                    <img src="${img.original_url}" alt="Gallery Image" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" />
                    
                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <button class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-lg shadow-md transition-transform hover:scale-110 cursor-pointer text-xs font-medium flex items-center gap-1" 
                                data-content-image-id="${img.id}" 
                                onclick="removeImage(this, ${game_id})"
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
                <span class="text-4xl mb-2">🎮</span>
                <p class="text-sm font-medium">No images uploaded for this game yet.</p>
                <p class="text-xs text-slate-400 mt-1">Upload images to use them inside your game review content.</p>
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
        template.innerHTML = await createGalleryHTML(btn.dataset.gameId);
        let fragment = template.content;
        this.after(fragment);
    });
});
