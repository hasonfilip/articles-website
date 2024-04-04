window.addEventListener('load', () => {
    const utmSourceInput = document.getElementById('custom-utm-source');
    const link = document.getElementById('link-utm-source');
    utmSourceInput.addEventListener('input', () => {
        if (utmSourceInput.value.length < 64) {
            link.href = link.href.split('?')[0] + '?utm_source=' + utmSourceInput.value;
        } else {
            alert('UTM source is too long!')
        }
    })

    // validate if link is in [a-z0-9]{0-64} pattern
    link.addEventListener('click', (e) => {
        let param = link.href.split('?')[1];
        if (param) {
            param = param.split('=')[1];
            if (param && !param.match(/^[a-z0-9]{0,64}$/)) {
                e.preventDefault();
                alert('UTM source must contain only lowercase letters and numbers and must be shorter than 64 characters!')
            }
        }
    })
});