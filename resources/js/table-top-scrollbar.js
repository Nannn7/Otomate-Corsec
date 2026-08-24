/**
 * Dual scrollbar untuk semua tabel data (.scrollable-x-auto).
 *
 * Tabel yang lebar (kolom banyak) cuma punya scrollbar horizontal di bagian
 * BAWAH tabel - kalau tabelnya panjang (banyak baris), user harus scroll ke
 * bawah dulu buat nemu scrollbar-nya. Modul ini nambahin scrollbar tiruan di
 * ATAS setiap tabel (tepat di bawah <thead>... maksudnya di atas wrapper
 * tabel), yang posisinya selalu ke-sync dua arah sama scrollbar asli.
 *
 * Berlaku otomatis untuk SEMUA tabel di semua module - cukup pasang class
 * "scrollable-x-auto" di wrapper <table> seperti yang udah jadi konvensi di
 * seluruh aplikasi (lihat data-datatable-table="true" di semua index page).
 * Nggak perlu ubah blade satu-satu.
 */

const TOP_BAR_CLASS = "scrollable-x-auto";
const TOP_BAR_MARKER_CLASS = "js-table-top-scrollbar";
const INIT_FLAG = "topScrollbarInit";

function createTopScrollbar(container) {
    const topBar = document.createElement("div");
    topBar.className = `${TOP_BAR_CLASS} ${TOP_BAR_MARKER_CLASS}`;
    topBar.style.height = "14px";
    topBar.style.display = "none";

    const spacer = document.createElement("div");
    spacer.style.height = "1px";
    topBar.appendChild(spacer);

    container.parentNode.insertBefore(topBar, container);

    let syncing = false;

    const syncFromTopBar = () => {
        if (syncing) return;
        syncing = true;
        container.scrollLeft = topBar.scrollLeft;
        syncing = false;
    };

    const syncFromContainer = () => {
        if (syncing) return;
        syncing = true;
        topBar.scrollLeft = container.scrollLeft;
        syncing = false;
    };

    const refreshWidth = () => {
        spacer.style.width = `${container.scrollWidth}px`;
        topBar.style.display = container.scrollWidth > container.clientWidth + 1 ? "block" : "none";
    };

    topBar.addEventListener("scroll", syncFromTopBar);
    container.addEventListener("scroll", syncFromContainer);

    refreshWidth();

    // Tabel di aplikasi ini di-load via AJAX (data-datatable), jadi lebar
    // tabel berubah-ubah setelah data selesai fetch. ResizeObserver otomatis
    // nangkep perubahan itu tanpa perlu tau API reload spesifik tiap module.
    if (typeof ResizeObserver !== "undefined") {
        const observer = new ResizeObserver(() => refreshWidth());
        observer.observe(container);

        const table = container.querySelector("table");
        if (table) observer.observe(table);
    } else {
        // Fallback untuk browser lama: re-check di window resize aja.
        window.addEventListener("resize", refreshWidth);
    }
}

function initTopScrollbars(root = document) {
    const containers = root.querySelectorAll(`.${TOP_BAR_CLASS}`);

    containers.forEach((container) => {
        if (container.classList.contains(TOP_BAR_MARKER_CLASS)) return; // ini top-bar-nya sendiri
        if (container.dataset[INIT_FLAG] === "true") return;
        if (!container.querySelector("table")) return; // cuma target wrapper tabel

        container.dataset[INIT_FLAG] = "true";
        createTopScrollbar(container);
    });
}

document.addEventListener("DOMContentLoaded", () => initTopScrollbars());

// Tangkep wrapper tabel yang baru muncul belakangan (mis. dimuat lewat modal
// atau tab yang di-render setelah DOMContentLoaded).
const tableWatcher = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
            if (node.nodeType !== 1) return;

            if (node.classList?.contains(TOP_BAR_CLASS) && !node.classList.contains(TOP_BAR_MARKER_CLASS)) {
                initTopScrollbars(node.parentElement ?? document);
            } else if (node.querySelector?.(`.${TOP_BAR_CLASS}`)) {
                initTopScrollbars(node);
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", () => {
    tableWatcher.observe(document.body, { childList: true, subtree: true });
});