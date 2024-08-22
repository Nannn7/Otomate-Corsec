import "./bootstrap";

import "../metronic/core/index";
import "../metronic/app/layouts/base.js";

import $ from "jquery";

window.jQuery = $;
window.$ = $;

import TomSelect from "tom-select";

document.querySelectorAll(".tomselect").forEach((el) => {
    let settings = {
        plugins: ["dropdown_input"],
        create: false,
        createOnBlur: true,
    };

    new TomSelect(el, settings);
});

import toast from "toastr";
import "toastr/build/toastr.css";

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
