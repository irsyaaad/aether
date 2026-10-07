<div class="row">
    <div class="col-md-3">
        <label style="font-weight: bold;">
            Dari Tanggal
        </label>
        <input type="date" data-ignore-limit class="form-control" name="dr_tgl" id="dr_tgl"
            value="{{ $filter['dr_tgl'] ?? '' }}">
    </div>

    <div class="col-md-3">
        <label style="font-weight: bold;">
            Sampai Tanggal
        </label>
        <input type="date" data-ignore-limit class="form-control" name="sp_tgl" id="sp_tgl"
            value="{{ $filter['sp_tgl'] ?? '' }}">
    </div>

    <div class="col-md-3">
        <label style="font-weight: bold;">Status</label>
        <div class="form-group" style="margin-bottom: 5px;">
            <label style="font-weight: normal; cursor: pointer; display: block; margin-bottom: 3px;">
                <input type="checkbox" name="status[]" value="draft" style="cursor: pointer;"
                    {{ isset($filter['status']) && is_array($filter['status']) && in_array('draft', $filter['status']) ? 'checked' : '' }}> 
                Dibuat
            </label>
            <label style="font-weight: normal; cursor: pointer; display: block; margin-bottom: 3px;">
                <input type="checkbox" name="status[]" value="kirim" style="cursor: pointer;"
                    {{ isset($filter['status']) && is_array($filter['status']) && in_array('kirim', $filter['status']) ? 'checked' : '' }}> 
                Kirim
            </label>
            <label style="font-weight: normal; cursor: pointer; display: block; margin-bottom: 3px;">
                <input type="checkbox" name="status[]" value="belum lunas" style="cursor: pointer;"
                    {{ isset($filter['status']) && is_array($filter['status']) && in_array('belum lunas', $filter['status']) ? 'checked' : '' }}> 
                Lunas Sebagian
            </label>
            <label style="font-weight: normal; cursor: pointer; display: block; margin-bottom: 3px;">
                <input type="checkbox" name="status[]" value="lunas" style="cursor: pointer;"
                    {{ isset($filter['status']) && is_array($filter['status']) && in_array('lunas', $filter['status']) ? 'checked' : '' }}> 
                Lunas
            </label>
        </div>
    </div>

    <div class="col-md-3">
        <label style="font-weight: bold;">Customer</label>
        <select name="customer_id" id="customer_id" class="form-control" style="width: 100%;">
            <option value="">Semua Customer</option>
            @if(isset($filter['customer_id']) && $filter['customer_id'])
                <option value="{{ $filter['customer_id'] }}" selected>Loading...</option>
            @endif
        </select>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize Select2 for Customer
        if ($('#customer_id').length && typeof $.fn.select2 !== 'undefined') {
            $('#customer_id').select2({
                placeholder: 'Pilih Customer',
                allowClear: true,
                ajax: {
                    url: "{{ url('ajax/get-customer') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.nama + ' - ' + item.alamat
                                };
                            })
                        };
                    }
                }
            });

            // Load selected customer name if there's a pre-selected value
            @if(isset($filter['customer_id']) && $filter['customer_id'])
                $.ajax({
                    url: "{{ url('ajax/get-customer') }}",
                    data: { 
                        search: "{{ $filter['customer_id'] }}",
                        page: 1,
                        per_page: 1
                    },
                    success: function(data) {
                        if (data && data.length > 0) {
                            var option = new Option(data[0].nama + ' - ' + data[0].alamat, {{ $filter['customer_id'] }}, true, true);
                            $('#customer_id').append(option).trigger('change');
                        }
                    }
                });
            @endif
        }
    });
</script>
