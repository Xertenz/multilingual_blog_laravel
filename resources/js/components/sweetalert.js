function showToast(icon = "success", title = "", position = "bottom-end") {
    const Toast = Swal.mixin({
        toast: true,
        position: position, // مكان الظهور (أعلى اليمين)
        showConfirmButton: false,
        timer: 3000, // يختفي بعد 3 ثوانٍ
        timerProgressBar: true,

        didOpen: (toast) => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        },
    });

    Toast.fire({
        icon: icon,
        title: title,
    });
}

window.showToast = showToast;
