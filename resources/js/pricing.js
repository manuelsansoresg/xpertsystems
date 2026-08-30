import Alpine from 'alpinejs';
import checkoutModal from './components/checkout-modal';

Alpine.data('checkoutModal', checkoutModal);

window.Alpine = Alpine;
Alpine.start();
