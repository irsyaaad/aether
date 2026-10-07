<div class="row">
    <div class="col-md-4">
        <label style="font-weight: bold;">
            Dari Tanggal
        </label>
        <input type="date" data-ignore-limit class="form-control" name="dr_tgl" id="dr_tgl"
            value="{{ $filter['dr_tgl'] ?? '' }}">
    </div>

    <div class="col-md-4">
        <label style="font-weight: bold;">
            Sampai Tanggal
        </label>
        <input type="date" data-ignore-limit class="form-control" name="sp_tgl" id="sp_tgl"
            value="{{ $filter['sp_tgl'] ?? '' }}">
    </div>

    <div class="col-md-4">
        <label style="font-weight: bold;">Customer</label>
        <select name="customer_id" id="customer_id" class="form-control" style="width: 100%;">
            <option value="">Semua Customer</option>
            @if(isset($filter['customer_id']) && $filter['customer_id'] && isset($filter['customer_name']))
                <option value="{{ $filter['customer_id'] }}" selected>{{ $filter['customer_name'] }}</option>
            @endif
        </select>
    </div>
</div>

<script>
    $(document).ready(function() {
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
        }
    });
</script>
