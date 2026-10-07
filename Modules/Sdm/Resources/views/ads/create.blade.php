@extends('template.root')
@section('page-name', 'service')
@section('title-page', isset($ads) ? 'Edit Iklan' : 'Create Iklan')
@section('content')
    <form action="{{ isset($ads) ? route('ads.update', $ads->id) : route('ads.store') }}" method="POST"
        enctype="multipart/form-data">

        @csrf
        @if (isset($ads))
            @method('PUT')
        @endif

        <div class="card-body">

            {{-- Title --}}
            <div class="mb-3">
                <label class="form-label">Title *</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                    value="{{ old('title', $ads->title ?? '') }}">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" rows="4" class="form-control">{{ old('description', $ads->description ?? '') }}</textarea>
            </div>

            {{-- Image --}}
            <div class="mb-3">
                <label class="form-label">Image</label>
                <input type="file" name="image" class="form-control">

                @if (isset($ads) && $ads->image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $ads->image) }}" height="120" class="img-thumbnail">
                    </div>
                @endif
            </div>

            {{-- Start date --}}
            <div class="mb-3">
                <label class="form-label">Start Date *</label>
                <input type="date" name="start_date" value="{{ old('start_date', $ads->start_date ?? '') }}"
                    class="form-control">
            </div>

            {{-- End date --}}
            <div class="mb-3">
                <label class="form-label">End Date *</label>
                <input type="date" name="end_date" value="{{ old('end_date', $ads->end_date ?? '') }}" class="form-control">
            </div>

            {{-- Status --}}
            <div class="mb-3">
                <label class="form-label">Status *</label>
                <select name="status" class="form-select">
                    <option value="active" {{ old('status', $ads->status ?? '') == 'active' ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="inactive" {{ old('status', $ads->status ?? '') == 'inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
            </div>

            {{-- Remarks --}}
            <div class="mb-3">
                <label class="form-label">Tempat Posting (Remarks)</label>
                <input type="text" name="remarks" class="form-control @error('remarks') is-invalid @enderror"
                    value="{{ old('remarks', $ads->remarks ?? '') }}" placeholder="Contoh: Facebook, JobStreet, Instagram, Linkedin, dll">
                <small class="text-muted">Pisahkan dengan koma untuk beberapa tempat</small>
                @error('remarks')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>

        <div class="col-md-12 text-end">
            <a href="{{ url(Request::segment(1)) }}" class="btn btn-danger mt-3">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
            <button type="submit" class="btn btn-primary mt-3">
                <i class="fas fa-save"></i>
                {{ isset($ads) ? 'Update Iklan' : 'Simpan Iklan' }}
            </button>
        </div>

    </form>
@endsection
