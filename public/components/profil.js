const photoInput = document.getElementById('photoInput');
const photoPreview = document.getElementById('photoPreview');
const photoLabel = document.getElementById('photoLabel');
const saveBtn = document.getElementById('saveBtn');

photoInput.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        photoPreview.src = e.target.result;
        photoPreview.style.display = 'block';
        photoLabel.style.display = 'none';
    };
    reader.readAsDataURL(file);
});

saveBtn.addEventListener('click', function () {
    alert('Данные сохранены!');
});