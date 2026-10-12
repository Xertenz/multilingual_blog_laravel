tinymce.init({
    selector: "#description",
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
            text: "+ Add Image",
            icon: "image",
            tooltip: "Add an image from gallery",
            onAction() {
                let html = `
                    <div style="padding: 10px; font-family: Inter, sans-serif;">
                        <h3 style="font-size: 16px; font-weight: 600; color: #1e293b; margin-bottom: 12px;">Available Images</h3>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 12px; max-height: 350px; overflow-y: auto; padding: 4px;">
                `;

                for (let article_content_img_info of article_content_imgs_info) {
                    html += `
                        <div onclick="addImage('${article_content_img_info["src"]}', '${article_content_img_info["id"]}')" 
                             style="cursor: pointer; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; transition: all 0.2s ease; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"
                             onmouseover="this.style.borderColor='#6366f1'; this.style.transform='translateY(-2px)';"
                             onmouseout="this.style.borderColor='#cbd5e1'; this.style.transform='translateY(0)';">
                            <div style="width: 100%; height: 90px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <img src="${article_content_img_info["src"]}" style="width: 100%; height: 100%; object-fit: cover;" />
                            </div>
                            <div style="padding: 6px; text-align: center; font-size: 11px; color: #64748b; font-weight: 500;">
                                ID: ${article_content_img_info["id"]}
                            </div>
                        </div>
                    `;
                }

                html += `</div></div>`;

                editor.windowManager.open({
                    title: "Article Photo Gallery",
                    size: "normal",
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
                            text: "Close",
                            type: "cancel",
                        },
                    ],
                });
            },
        });
    },
});

window.addImage = function (src, content_img_id) {
    tinymce.activeEditor.insertContent(`
        <figure class="article-image-container my-4 text-center">
            <img src="${src}"
                 data-image-id="${content_img_id}"
                 decoding="async"
                 loading="lazy"
                 class="content_img max-w-full h-auto rounded-lg shadow-sm"
                 alt="Article image"
            />
            <figcaption class="image-caption mt-2 text-sm text-gray-550 italic" contenteditable="true">
                اكتب وصف الصورة هنا...
            </figcaption>
        </figure>
        <p>&nbsp;</p> `);

    tinymce.activeEditor.windowManager.close();
};
