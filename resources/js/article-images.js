/*
const art_desc_container = document.getElementById("description-container");
const art_desc_imgs = art_desc_container.getElementsByTagName("img");
const srcurl = window.responsive_content_path;

window.get_content_img_info = async function (content_img_id) {
    const formData = new FormData();
    formData.append("content_img_id", content_img_id);

    let request = await fetch("/articles/content_img_info", {
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

Array.from(art_desc_imgs).forEach(async (art_desc_img) => {
    const art_desc_img_id = parseInt(art_desc_img.dataset.imageId);
    const content_img_info = await get_content_img_info(art_desc_img_id);
    const img_responsive_names =
        content_img_info["responsive_images"]["responsive"]["urls"];

    const year = new Date(content_img_info["created_at"])
        .getFullYear()
        .toString();
    let month = (
        new Date(content_img_info["created_at"]).getMonth() + 1
    ).toString();
    month = month < 10 ? `0${month}` : month;

    let srcset = "";
    const widths = [1200, 1003, 839, 702, 587, 491, 411, 344, 288, 241];
    let i = 0;
    for (const img_responsive_name of img_responsive_names) {
        const img_responsive_url = `${srcurl}/${year}/${month}/responsive/${img_responsive_name} `;
        srcset += `${img_responsive_url} ${widths[i]}w, `;
        i += 1;
    }
    console.log(srcset.toString());
    art_desc_img.srcset = `${srcset}`;
});
*/
