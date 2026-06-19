@extends('layouts.app')

@section('title', 'Chỉnh Sửa Tin Đăng | BDS Rental')

@section('content')
<div class="bg-slate-50 pt-28 pb-16 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        
        <!-- Breadcrumbs -->
        <nav class="flex text-xs font-semibold text-slate-500 mb-6 space-x-2" aria-label="Breadcrumb">
            <a href="/" class="hover:text-primary transition">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('profile.index', ['tab' => 'properties']) }}" class="hover:text-primary transition">Quản lý tin đăng</a>
            <span>/</span>
            <span class="text-slate-800">Chỉnh sửa tin đăng</span>
        </nav>

        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden p-6 sm:p-8 text-left">
            @include('owner.properties.edit_form')
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- MapLibre GL JS SDK -->
<script src="https://unpkg.com/maplibre-gl@^4.0.0/dist/maplibre-gl.js"></script>

<script>
    function propertyForm() {
        return {
            lat: {{ old('lat', $property->lat) }},
            lng: {{ old('lng', $property->lng) }},
            mainPreview: '',
            galleryPreviews: [],
            deletedImages: [], // Holds paths of images to be deleted
            map: null,
            marker: null,

            init() {
                this.$nextTick(() => {
                    this.initMap();
                });
            },

            initMap() {
                this.map = new maplibregl.Map({
                    container: 'picker-map',
                    style: 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json',
                    center: [this.lng, this.lat],
                    zoom: 13.5
                });

                // Create draggable marker
                this.marker = new maplibregl.Marker({
                    draggable: true,
                    color: '#ff4433' // Red marker
                })
                .setLngLat([this.lng, this.lat])
                .addTo(this.map);

                // Update inputs on drag end
                this.marker.on('dragend', () => {
                    const lngLat = this.marker.getLngLat();
                    this.lat = parseFloat(lngLat.lat.toFixed(6));
                    this.lng = parseFloat(lngLat.lng.toFixed(6));
                });

                // Update marker position on click
                this.map.on('click', (e) => {
                    const lngLat = e.lngLat;
                    this.lat = parseFloat(lngLat.lat.toFixed(6));
                    this.lng = parseFloat(lngLat.lng.toFixed(6));
                    this.marker.setLngLat(lngLat);
                });
            },

            previewMainImage(event) {
                const file = event.target.files[0];
                if (file) {
                    this.mainPreview = URL.createObjectURL(file);
                }
            },

            previewGalleryImages(event) {
                const files = event.target.files;
                this.galleryPreviews = [];
                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    this.galleryPreviews.push(URL.createObjectURL(file));
                }
            },

            toggleDeleteImage(path) {
                if (!this.deletedImages.includes(path)) {
                    this.deletedImages.push(path);
                } else {
                    this.deletedImages = this.deletedImages.filter(item => item !== path);
                }
            }
        }
    }
</script>
@endpush
