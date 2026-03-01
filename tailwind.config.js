// tailwind.config.js
module.exports = {
    theme: {
        extend: {
            // إضافة container مطابق لـ Bootstrap
            container: {
                center: true,
                padding: "1rem",
                screens: {
                    sm: "540px", // Bootstrap: 100% عند < 576px
                    md: "720px", // Bootstrap: 100% عند < 768px
                    lg: "960px", // Bootstrap: 960px عند ≥ 992px
                    xl: "1140px", // Bootstrap: 1140px عند ≥ 1200px
                    "2xl": "1320px", // Bootstrap: 1320px عند ≥ 1400px
                },
            },
            screens: {
                sm: "576px", // Bootstrap breakpoint
                md: "768px", // Bootstrap breakpoint
                lg: "992px", // Bootstrap breakpoint
                xl: "1200px", // Bootstrap breakpoint
                "2xl": "1400px", // Bootstrap breakpoint
            },
        },
    },
};
