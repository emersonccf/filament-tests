export function numberOnly() {
    return {
        value: '',
        onInput(event) {
            // Remove todos os caracteres não numéricos
            this.value = event.target.value.replace(/[^0-9]/g, '');
        }
    }
}
