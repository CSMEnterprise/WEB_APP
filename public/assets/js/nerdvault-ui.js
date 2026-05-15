function togglePasswordVisibility(inputId, button) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    if (button) button.textContent = isHidden ? 'Nascondi' : 'Mostra';
}

function toggleIndirizzoForm() {
    const form = document.getElementById('indirizzoForm');
    if (!form) return;
    form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', () => {
    const fileInputs = document.querySelectorAll('input[type="file"][name="immagini[]"], input[type="file"][multiple]');

    fileInputs.forEach((input) => {
        input.addEventListener('change', () => {
            const previewId = input.dataset.preview || 'photoPreview';
            const preview = document.getElementById(previewId) || document.querySelector('.photo-preview');
            if (!preview) return;

            preview.innerHTML = '';
            Array.from(input.files || []).slice(0, 8).forEach((file) => {
                if (!file.type.startsWith('image/')) return;

                const item = document.createElement('div');
                item.className = 'photo-preview-item';

                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = 'Anteprima foto';
                img.onload = () => URL.revokeObjectURL(img.src);

                item.appendChild(img);
                preview.appendChild(item);
            });
        });
    });
});
