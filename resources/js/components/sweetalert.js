window.showToast = function ({
    icon = null,
    type = "success",
    title = "",
    text = "",
    message = "",
    timer = 3000,
    position = "bottom-end",
} = {}) {
    if (typeof Swal === "undefined") {
        return;
    }

    const resolvedIcon = icon || type || "success";
    const resolvedText = text || message || "";
    const resolvedTitle = title || resolvedText;

    Swal.fire({
        toast: true,
        position: position,
        icon: resolvedIcon,
        title: resolvedTitle,
        text: title && resolvedText ? resolvedText : "",
        showConfirmButton: false,
        timer: timer,
        timerProgressBar: true,
        background: "#0f1526",
        color: "#f1f5f9",
        didOpen: (toast) => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        },
    });
};

// مستمع عام لأحداث Livewire لتشغيل الـ Toast تلقائياً من أي مكوّن
window.addEventListener("toast", (event) => {
    const payload = Array.isArray(event.detail)
        ? event.detail[0]
        : event.detail;
    if (!payload) {
        return;
    }

    window.showToast(payload);
});
