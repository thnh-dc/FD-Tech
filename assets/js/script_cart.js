//Script xóa sản phẩm
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-delete');
    if (!btn) return;

    const id = btn.dataset.id;
    const row = btn.closest('tr');

    if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
        fetch('action_cart/delete_cart.php', {
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

                // cập nhật tổng tiền → reload
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

//Script tăng/giảm sản phẩm
function updateQty(id, change) {
    fetch('action_cart/update_cart.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/x-www-form-urlencoded' 
        },
        body: `id=${id}&change=${change}`
    })
    .then(res => res.json())
    .then(data => {
        console.log(data);

        if(data.success){
            location.reload();
        }
    })
    .catch(err => console.error(err));
}
document.addEventListener('click', function(e){

    if(e.target.classList.contains('btn-plus')){
        updateQty(e.target.dataset.id, 1);
    }

    if(e.target.classList.contains('btn-minus')){
        updateQty(e.target.dataset.id, -1);
    }

});

// Chọn tất cả
document.getElementById('check-all')?.addEventListener('change', function() {
    const checked = this.checked;
    document.querySelectorAll('.item-check').forEach(cb => {
        cb.checked = checked;
    });
});

// Submit form → gửi list sản phẩm được chọn
document.getElementById('checkout-form')?.addEventListener('submit', function(e) {
    const selected = [];

    document.querySelectorAll('.item-check:checked').forEach(cb => {
        selected.push(cb.value);
    });

    if (selected.length === 0) {
        alert('Vui lòng chọn ít nhất 1 sản phẩm!');
        e.preventDefault();
        return;
    }

    document.getElementById('selected-items').value = selected.join(',');
});