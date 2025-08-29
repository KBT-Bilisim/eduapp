// Ürün durumunu AJAX ile değiştir
$(document).on('change', '.toggle-purchased', function() {
    var itemId = $(this).data('id');
    var isChecked = $(this).is(':checked');
    $.ajax({
        url: '/shopping/items/' + itemId + '/toggle',
        type: 'PATCH',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if(response.success) {
                // İlerleme çubuğunu güncellemek için sayfayı yenile veya dinamik olarak güncelle
                location.reload();
            }
        },
        error: function() {
            alert('Bir hata oluştu!');
        }
    });
});
