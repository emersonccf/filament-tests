import './bootstrap';
import '../css/app.css';
import Alpine from 'alpinejs';
import { cpfMask } from './masks/cpf-mask';
import { numberOnly } from './functions/only-number';

// window.Alpine = Alpine;
// Alpine.start();

// Torna a função de máscara disponível globalmente
window.cpfMask = cpfMask;
// Torna a função disponível globalmente
window.numberOnly = numberOnly;
