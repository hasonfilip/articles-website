window.addEventListener('load', () => {
    const nameInput = document.getElementById('name-edit');
    const contentInput = document.getElementById('content');
    const saveBtn = document.getElementById('save-btn');
    nameInput.addEventListener('input', () => {
        if (nameInput.value === '') {
            saveBtn.disabled = true;
        } else {
            saveBtn.disabled = false;
        }
    });
    contentInput.focus();
    contentInput.select();
});