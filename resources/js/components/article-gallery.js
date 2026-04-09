let showGalleryBtns = document.querySelectorAll(".show-gallery");

window.closing = function (article_id) {
    document.getElementById("article-gallery-wrapper").remove();
};

const upload_url = window.upload_url;
window.choosingFile = async function (fileInput) {
    const content_img = fileInput.files[0];
    const article_id = fileInput.dataset.articleId;

    const formData = new FormData();
    formData.append("content_img", content_img);
    formData.append("article_id", article_id);

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

    rerendering_the_gallery(article_id);
};

const article_content_imgs_url = window.article_content_imgs_url;
window.getArticleContentImgs = async function (article_id) {
    const formData = new FormData();
    formData.append("article_id", article_id);

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
window.removeImage = async function (element, article_id) {
    //removing the selected image (not yet)
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
    //console.log(data);
    rerendering_the_gallery(article_id);
};

window.rerendering_the_gallery = async function (article_id) {
    //rerendering the gallery
    let template = document.createElement("template");
    template.innerHTML = await createGalleryHTML(article_id);
    let fragment = template.content;
    let showGalleryBtn = document.querySelector(
        `.show-gallery[data-article-id="${article_id}"]`
    );
    showGalleryBtn.after(fragment);
    document.getElementsByClassName("article-gallery-wrapper")[1].remove();
    //console.log(document.getElementsByClassName("article-gallery-wrapper")[0]);

    //updating 'showGelleryBtn' count of content_imgs
    let article_content_img = await getArticleContentImgs(article_id);
    showGalleryBtn.querySelector(".artilce-content_imgs-count").textContent =
        article_content_img.length.toString();
};

window.createGalleryHTML = async function (article_id) {
    let article_content_imgs = await getArticleContentImgs(article_id);

    let html = `
<div class="article-gallery-wrapper" id="article-gallery-wrapper">
    <div class="popup">
        <div class="top-bar">
            <label for="content-img" class="cursor-pointer px-3 py-1 border-2 border-indigo-400 text-base font-medium rounded-md text-indigo-800 bg-indigo-400 hover:bg-indigo-700">Upload +</label>
            <input type="file" name="content-img" id="content-img" class="hidden" data-article-id="${article_id}" onchange="choosingFile(this)">
            <button class="close-icon" id="close-icon" onclick="closing(this)">
                <span>X</span>
            </button>
        </div>
        <div class="images-wrapper">
            <div class="images images-container">`;

    if (article_content_imgs.length > 0) {
        article_content_imgs.forEach((article_content_img) => {
            html += `<div class="image">
                            <img src="${article_content_img.original_url}" alt="" loading="lazy" />
                            <button class="delete-icon" id="delete-icon" data-content-image-id="${article_content_img.id}" onclick="removeImage(this, ${article_id})">X</button>
                    </div>`;
        });
    } else {
        html += "<p class='no-images'>no images</p>";
    }

    html += `</div>
        </div>
    </div>
</div>`;
    return html;
};
showGalleryBtns.forEach(function (showGalleryBtn) {
    showGalleryBtn.addEventListener("click", async function () {
        let template = document.createElement("template");
        template.innerHTML = await createGalleryHTML(
            showGalleryBtn.dataset.articleId
        );
        let fragment = template.content;
        this.after(fragment);
    });
});
