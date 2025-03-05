import "./bootstrap";
import "../metronic/core/index";
import "../metronic/app/layouts/base.js";
import $ from "jquery";
import Swal from "sweetalert2";
import TomSelect from "tom-select";
import toast from "toastr";
import "toastr/build/toastr.css";
import IMask from "imask";
// import FilerobotImageEditor from "filerobot-image-editor";

window.jQuery = $;
window.$ = $;

window.Swal = Swal;
window.swal = Swal;

window.IMask = IMask;
// window.FilerobotImageEditor = FilerobotImageEditor;

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

document.querySelectorAll(".toastr").forEach((el) => {
    toast.options = {
        closeButton: true,
        timeOut: 5000,
        showMethod: "slideDown",
        closeMethod: "slideUp",
        preventDuplication: true,
        newestOnTop: true,
        closeDuration: 300,
    };
    toast[el.dataset.type](el.dataset.message);
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
