document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-delete');
    if (!btn) return;

    const id = btn.dataset.id;
    const row = btn.closest('tr');

    if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
        fetch('/TEST/user/delete_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + encodeURIComponent(id)
        })
        .then(res => res.text())
        .then(data => {
            if (data.trim() === 'success') {
                row.remove();

                // Nếu muốn cập nhật tổng tiền → reload
                location.reload();
            } else {
                alert('Xóa thất bại!');
                console.log(data);
            }
        })
        .catch(err => {
            console.error('Lỗi:', err);
        });
    }
});