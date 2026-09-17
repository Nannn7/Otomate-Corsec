import "./bootstrap";
import "../metronic/core/index";
import "../metronic/app/layouts/base.js";
import "./table-top-scrollbar";
import $ from "jquery";
import Swal from "sweetalert2";
import TomSelect from "tom-select";
import toast from "toastr";
import "toastr/build/toastr.css";
import IMask from "imask";
// import FilerobotImageEditor from "filerobot-image-editor";

window.jQuery = $;
window.$ = $;

window.IMask = IMask;
// window.FilerobotImageEditor = FilerobotImageEditor;
window.TomSelect = TomSelect;

const CORSEC_NOTIFICATION_TIMEOUT = 15000;
window.corsecNotificationTimeout = CORSEC_NOTIFICATION_TIMEOUT;
const wrappedSwalInstances = new WeakSet();

const resolveSwalIcon = (args) => {
    if (typeof args[0] === "object" && args[0] !== null) {
        return args[0].icon;
    }

    return args[2];
};

const withSuccessSwalDefaults = (args) => {
    if (resolveSwalIcon(args) !== "success") {
        return args;
    }

    if (typeof args[0] === "object" && args[0] !== null) {
        return [
            {
                timer: CORSEC_NOTIFICATION_TIMEOUT,
                timerProgressBar: true,
                ...args[0],
            },
            ...args.slice(1),
        ];
    }

    return [
        {
            title: args[0],
            text: args[1],
            icon: args[2],
            timer: CORSEC_NOTIFICATION_TIMEOUT,
            timerProgressBar: true,
        },
    ];
};

const wrapSwal = (swalInstance) => {
    if (!swalInstance || typeof swalInstance.fire !== "function") {
        return swalInstance;
    }

    if (wrappedSwalInstances.has(swalInstance)) {
        return swalInstance;
    }

    const swalFire = swalInstance.fire.bind(swalInstance);
    swalInstance.fire = (...args) => swalFire(...withSuccessSwalDefaults(args));
    wrappedSwalInstances.add(swalInstance);

    return swalInstance;
};

let currentSwal = wrapSwal(Swal);
Object.defineProperty(window, "Swal", {
    configurable: true,
    get: () => currentSwal,
    set: (value) => {
        currentSwal = wrapSwal(value);
    },
});
Object.defineProperty(window, "swal", {
    configurable: true,
    get: () => currentSwal,
    set: (value) => {
        currentSwal = wrapSwal(value);
    },
});

document.querySelectorAll(".tomselect").forEach((el) => {
    let settings = {
        plugins: ["dropdown_input", "remove_button", "clear_button"],
        create: false,
        createOnBlur: true,
        closeButton: true,
        html: function (data) {
            return `<div class="${data.className}" title="${data.title}">&times;</div>`;
        },
    };

    new TomSelect(el, settings);
});

window.toast = toast;
toast.options = {
    closeButton: true,
    timeOut: CORSEC_NOTIFICATION_TIMEOUT,
    extendedTimeOut: 5000,
    showMethod: "slideDown",
    closeMethod: "slideUp",
    preventDuplicates: true,
    newestOnTop: true,
    closeDuration: 300,
};

window.corsecAjaxMessage = function (error, fallback = "Aksi gagal diproses.") {
    if (error?.responseJSON?.message) {
        return error.responseJSON.message;
    }

    if (error?.responseJSON?.error) {
        return error.responseJSON.error;
    }

    if (typeof error?.responseText === "string" && error.responseText.trim() !== "") {
        try {
            const payload = JSON.parse(error.responseText);

            if (payload?.message) {
                return payload.message;
            }
        } catch {
            return fallback;
        }
    }

    return fallback;
};

document.querySelectorAll(".toastr").forEach((el) => {
    toast[el.dataset.type](el.dataset.message);
});

const cssEscape = (value) => {
    if (window.CSS && typeof window.CSS.escape === "function") {
        return window.CSS.escape(value);
    }

    return String(value).replace(/["\\]/g, "\\$&");
};

const validationFieldName = (key) => {
    const parts = String(key).split(".");

    return parts.slice(1).reduce((name, part) => `${name}[${part}]`, parts[0]);
};

const findValidationFields = (key) => {
    const names = new Set([key, validationFieldName(key)]);
    const fields = [];

    names.forEach((name) => {
        document
            .querySelectorAll(`[name="${cssEscape(name)}"]`)
            .forEach((field) => fields.push(field));
    });

    return fields;
};

const showValidationMessage = (field, message) => {
    const wrapper =
        field.closest(".flex.flex-col") ||
        field.closest(".form-group") ||
        field.parentElement;

    if (!wrapper || wrapper.querySelector("[data-validation-error]")) {
        return;
    }

    const feedback = document.createElement("em");
    feedback.className = "mt-1 text-sm alert text-danger";
    feedback.dataset.validationError = "true";
    feedback.textContent = message;
    wrapper.appendChild(feedback);
};

Object.entries(window.corsecValidationErrors || {}).forEach(([key, messages]) => {
    const message = Array.isArray(messages) ? messages[0] : messages;

    findValidationFields(key).forEach((field) => {
        field.classList.add("border-danger", "bg-danger-light");

        if (field.tomselect?.wrapper) {
            field.tomselect.wrapper.classList.add("border-danger", "bg-danger-light");
        }

        showValidationMessage(field, message);
    });
});

// Fungsi untuk memformat tanggal ke format Indonesia
window.formatTanggalIndonesia = function (date) {
    const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    };
    return new Date(date).toLocaleDateString("id-ID", options);
};

// Fungsi untuk memformat tanggal dan waktu ke format Indonesia
window.formatTanggalWaktuIndonesia = function (date) {
    const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: false,
    };
    return new Date(date).toLocaleString("id-ID", options);
};

// Fungsi untuk memformat angka ke format Rupiah
window.formatRupiah = function (angka) {
    const formatter = new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
    return formatter.format(angka);
};

document.querySelectorAll(".currency").forEach((el) => {
    IMask(el, {
        mask: Number, // enable number mask

        // other options are optional with defaults below
        scale: 2, // digits after point, 0 for integers
        thousandsSeparator: ".", // any single char
        padFractionalZeros: false, // if true, then pads zeros at end to the length of scale
        normalizeZeros: true, // appends or removes zeros at ends
        radix: ",", // fractional delimiter
        mapToRadix: ["."], // symbols to process as radix

        autofix: true,
    });
});

document.querySelectorAll(".persen").forEach((el) => {
    IMask(el, {
        mask: Number, // enable number mask
        min: 0,
        max: 100,
        // other options are optional with defaults below
        scale: 2, // digits after point, 0 for integers
        thousandsSeparator: ".", // any single char
        padFractionalZeros: false, // if true, then pads zeros at end to the length of scale
        normalizeZeros: true, // appends or removes zeros at ends
        radix: ",", // fractional delimiter
        mapToRadix: ["."], // symbols to process as radix

        autofix: true,
    });
});
