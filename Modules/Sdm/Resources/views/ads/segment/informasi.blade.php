<div class="card-body">

    <div class="mb-3">
        <label class="form-label fw-bold">Title</label>
        <p>{{ $ads->title }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Description</label>
        <p>{{ $ads->description ?? '-' }}</p>
    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Image</label>
        <div>
            @if ($ads->image)
                <img src="{{ asset('storage/' . $ads->image) }}" class="img-thumbnail" style="max-height:200px;">
            @else
                <p class="text-muted">No image uploaded.</p>
            @endif
        </div>
    </div>

    <div class="row">

        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Start Date</label>
            <p>{{ $ads->start_date }}</p>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">End Date</label>
            <p>{{ $ads->end_date }}</p>
        </div>

    </div>

    <div class="mb-3">
        <label class="form-label fw-bold">Status</label>
        <p>
            @if ($ads->status == 'active')
                <span class="badge bg-success">Active</span>
            @else
                <span class="badge bg-danger">Inactive</span>
            @endif
        </p>
    </div>

    @if($ads->remarks)
    <div class="mb-3">
        <label class="form-label fw-bold">Tempat Posting</label>
        <p>{{ $ads->remarks }}</p>
    </div>
    @endif

    @if($ads->uuid)
    <div class="mb-3">
        <label class="form-label fw-bold">Link Recruitment (QR Code)</label>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div id="qrcode" data-url="{{ url('recruitment?uuid=' . $ads->uuid) }}"></div>
                        <small class="text-muted">Scan untuk membuka link</small>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label">URL:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ url('recruitment?uuid=' . $ads->uuid) }}" readonly id="recruitmentUrl">
                        <button class="btn btn-primary" type="button" onclick="copyRecruitmentUrl()">
                            <i class="fa fa-copy"></i> Copy
                        </button>
                    </div>
                </div>
                <div class="mb-3">
                    <button class="btn btn-success" type="button" onclick="downloadQRCode()">
                        <i class="fa fa-download"></i> Download QR Code
                    </button>
                </div>
                <small class="text-muted">Bagikan QR code ini untuk menerima lamaran kerja</small>
            </div>
        </div>
    </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
<script>
function copyRecruitmentUrl() {
    var copyText = document.getElementById("recruitmentUrl");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(copyText.value);
    alert("Link berhasil disalin!");
}

// Generate QR Code
document.addEventListener('DOMContentLoaded', function() {
    var qrContainer = document.getElementById('qrcode');
    if (qrContainer) {
        var url = qrContainer.dataset.url;

        // Create canvas element
        var canvas = document.createElement('canvas');
        qrContainer.appendChild(canvas);

        QRCode.toCanvas(canvas, url, {
            width: 150,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#ffffff'
            }
        }, function(error) {
            if (error) console.error(error);
        });
    }
});

function downloadQRCode() {
    var qrContainer = document.getElementById('qrcode');
    var canvas = qrContainer.querySelector('canvas');

    if (canvas) {
        // Create download link
        var link = document.createElement('a');
        link.download = 'recruitment-qrcode.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    }
}
</script>
