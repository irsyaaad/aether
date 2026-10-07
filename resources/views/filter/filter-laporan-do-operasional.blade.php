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
        <label style="font-weight: bold;">
            Sopir
        </label>
        <select name="sopir_id" id="sopir_id" class="form-control" style="width: 100%;">
            <option value="">Pilih Sopir</option>
            @if (isset($filter['sopir_id']) && $filter['sopir_id'])
                <option value="{{ $filter['sopir_id'] }}" selected>Loading...</option>
            @endif
        </select>
    </div>

    <div class="col-md-3">
        <label style="font-weight: bold;">
            Armada
        </label>
        <select name="armada_id" id="armada_id" class="form-control" style="width: 100%;">
            <option value="">Pilih Armada</option>
            @if(isset($filter['armada_id']) && $filter['armada_id'])
                <option value="{{ $filter['armada_id'] }}" selected>Loading...</option>
            @endif
        </select>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Wait for DOM to be fully loaded
        setTimeout(function() {
            // Initialize Select2 for Sopir
            if ($('#sopir_id').length && typeof $.fn.select2 !== 'undefined') {
                $('#sopir_id').select2({
                    placeholder: 'Pilih Sopir',
                    allowClear: true,
                    ajax: {
                        url: '/ajax/get-sopir',
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.nama_lengkap
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });

                // Load selected sopir name if there's a pre-selected value
                @if (isset($filter['sopir_id']) && $filter['sopir_id'])
                    $.ajax({
                        url: '/ajax/get-sopir',
                        dataType: 'json',
                        data: {
                            id: {{ $filter['sopir_id'] }}
                        },
                        success: function(data) {
                            if (data && data.length > 0) {
                                var sopir = data.find(item => item.id ==
                                    {{ $filter['sopir_id'] }});
                                if (sopir) {
                                    var option = new Option(sopir.nama_lengkap, sopir.id,
                                        true, true);
                                    $('#sopir_id').append(option).trigger('change');
                                }
                            }
                        }
                    });
                @endif
            }

            // Initialize Select2 for Armada
            if ($('#armada_id').length && typeof $.fn.select2 !== 'undefined') {
                $('#armada_id').select2({
                    placeholder: 'Pilih Armada',
                    allowClear: true,
                    ajax: {
                        url: '/ajax/get-armada',
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.id,
                                        text: item.nopol
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });

                @if (isset($filter['armada_id']) && $filter['armada_id'])
                    $.ajax({
                        url: '/ajax/get-armada',
                        dataType: 'json',
                        data: {
                            id: {{ $filter['armada_id'] }}
                        },
                        success: function(data) {
                            if (data && data.length > 0) {
                                var armada = data.find(item => item.id ==
                                    {{ $filter['armada_id'] }});
                                if (armada) {
                                    var option = new Option(armada.nopol, armada.id,
                                        true, true);
                                    $('#armada_id').append(option).trigger('change');
                                }
                            }
                        }
                    });
                @endif
            }
        }, 500); // Wait 500ms for other scripts to load
    });
</script>
