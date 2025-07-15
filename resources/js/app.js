import './bootstrap';
import Alpine from 'alpinejs';
import "/node_modules/select2/dist/css/select2.css";
import 'select2/dist/js/select2.min.js';

window.Alpine = Alpine;

Alpine.start();

import { Mask, MaskInput } from "maska"

new MaskInput(".maska");

console.log('test')