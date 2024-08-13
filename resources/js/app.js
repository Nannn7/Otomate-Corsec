import "./bootstrap";

import "../metronic/core/index";
import "../metronic/app/layouts/base.js";

import $ from 'jquery';

window.jQuery = $;
window.$ = $;


import TomSelect from "tom-select";

let settings = {
    plugins: ['dropdown_input'],
    create: false,
    createOnBlur: true
};

new TomSelect('.tomselect', settings);
