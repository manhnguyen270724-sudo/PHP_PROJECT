$(document).ready(function() {
    
    // Hàm định dạng tiền tệ (VNĐ)
    function formatCurrency(number) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(number).replace('₫', 'đ');
    }

    // 1. Xử lý cập nhật số lượng
    $(document).on('change', '.update-qty', function() {
        var pid = $(this).data('id');
        var qty = $(this).val();
        var row = $(this).closest('tr');

        if(qty < 1) {
            alert("Số lượng phải lớn hơn 0");
            $(this).val(1);
            return;
        }

        $.ajax({
            url: 'ajax-cart.php',
            type: 'POST',
            dataType: 'json',
            data: { action: 'update', id: pid, quantity: qty },
            success: function(response) {
                if (response.status === 'success') {
                    // Cập nhật thành tiền của dòng đó
                    $('.item-subtotal-' + pid).text(formatCurrency(response.item_subtotal));
                    // Cập nhật tổng tiền giỏ hàng
                    $('#cart-total').text(formatCurrency(response.total));
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // 2. Xử lý xóa sản phẩm
    $(document).on('click', '.delete-item', function() {
        var pid = $(this).data('id');
        var row = $('#item-' + pid);

        if(confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
            $.ajax({
                url: 'ajax-cart.php',
                type: 'POST',
                dataType: 'json',
                data: { action: 'delete', id: pid },
                success: function(response) {
                    if (response.status === 'success') {
                        // Xóa dòng html với hiệu ứng mờ dần
                        row.fadeOut(300, function(){ $(this).remove(); });
                        
                        // Cập nhật tổng tiền
                        $('#cart-total').text(formatCurrency(response.total));

                        // Nếu giỏ hàng trống thì reload lại trang để hiện thông báo trống
                        if(response.empty) {
                            location.reload();
                        }
                    }
                }
            });
        }
    });
});