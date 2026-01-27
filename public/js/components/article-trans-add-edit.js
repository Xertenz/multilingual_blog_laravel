tinymce.init({
    selector: "#meta_description",
    license_key: "gpl",
    plugins: [
        "advlist",
        "autolink",
        "lists",
        "link",
        "image",
        "charmap",
        "preview",
        "anchor",
        "searchreplace",
        "visualblocks",
        "code",
        "fullscreen",
        "insertdatetime",
        "media",
        "table",
        "help",
        "wordcount",
    ],

    toolbar:
        "bold italic underline undo redo | add | fontfamily fontsize table | forecolor backcolor link unlink | image media code | alignleft aligncenter alignright | bullist numlist fullscreen",
    setup: function (editor) {
        editor.ui.registry.addButton("add", {
            text: "+ add",
            icon: "image",
            tooltip: "add an image",
            onAction() {
                let html = "<h1>available images: </h1><div>";
                for (let article_content_img_info of article_content_imgs_info) {
                    html += `<div onclick="addImage('${article_content_img_info["src"]}', '${article_content_img_info["id"]}')"><img src="${article_content_img_info["src"]}" style="width: 100%;" /></div>`;
                }
                html += "</div>";

                editor.windowManager.open({
                    title: "Article Photo Gallery",
                    body: {
                        type: "panel",
                        items: [
                            {
                                type: "htmlpanel",
                                html: html,
                            },
                        ],
                    },
                    buttons: [
                        {
                            text: "close",
                            type: "cancel",
                        },
                    ],
                });
            },
        });
    },
});

function addImage(src, content_img_id) {
    tinymce.activeEditor.insertContent(`
            <div class="content_img-wrapper">
                <img src=${src}
                data-image-id=${content_img_id}
                decoding="async"
                loading="lazy"
                class="content_img"
                />
            </div>`);
    tinymce.activeEditor.windowManager.close();
}
