@extends('layouts.courier')

@section('title', 'Peta Rute - Courier Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Peta Rute Pengiriman & Penjemputan</h1>
    <p class="text-gray-600 mt-2">Kelola rute pengiriman dan penjemputan Anda dengan efisien berdasarkan area</p>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">Total Pengiriman</p>
                <h3 class="text-3xl font-bold mt-1" id="total-deliveries">0</h3>
            </div>
            <div class="bg-white/20 rounded-full p-3">
                <x-heroicon-o-truck class="h-8 w-8" />
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm font-medium">Total Penjemputan</p>
                <h3 class="text-3xl font-bold mt-1" id="total-returns">0</h3>
            </div>
            <div class="bg-white/20 rounded-full p-3">
                <x-heroicon-o-arrow-uturn-left class="h-8 w-8" />
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Area Berbeda</p>
                <h3 class="text-3xl font-bold mt-1" id="total-areas">0</h3>
            </div>
            <div class="bg-white/20 rounded-full p-3">
                <x-heroicon-o-map-pin class="h-8 w-8" />
            </div>
        </div>
    </div>
</div>

<!-- Filter & View Toggle -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <div class="flex flex-wrap items-center gap-4">
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700">Tampilkan:</label>
            <select id="filter-type" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <option value="all">Semua Task</option>
                <option value="delivery">Pengiriman Saja</option>
                <option value="return">Penjemputan Saja</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700">Prioritas:</label>
            <select id="filter-priority" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <option value="all">Semua</option>
                <option value="high">Prioritas Tinggi</option>
                <option value="normal">Normal</option>
            </select>
        </div>

        <button id="btn-refresh" class="ml-auto bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <x-heroicon-o-arrow-path class="h-5 w-5" />
            <span>Refresh Data</span>
        </button>
    </div>
</div>

<!-- Main Content: Map + List -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Map View (2/3) -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-500 text-white px-6 py-4">
                <h2 class="text-xl font-bold">Peta Lokasi</h2>
                <p class="text-green-100 text-sm">Klik marker untuk melihat detail lokasi</p>
            </div>
            <div id="map" class="h-[600px] bg-gray-100"></div>
        </div>
    </div>

    <!-- Area List (1/3) -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-500 text-white px-6 py-4">
                <h2 class="text-xl font-bold">Daftar Area</h2>
                <p class="text-green-100 text-sm">Grup berdasarkan lokasi</p>
            </div>
            <div id="area-list" class="divide-y divide-gray-200 max-h-[600px] overflow-y-auto">
                <!-- Area items will be inserted here -->
            </div>
        </div>
    </div>
</div>

<!-- Task Details Modal -->
<div id="task-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-green-600 to-emerald-500 text-white px-6 py-4 flex items-center justify-between">
            <h3 class="text-xl font-bold" id="modal-title">Detail Lokasi</h3>
            <button onclick="closeModal()" class="text-white hover:bg-white/20 rounded-lg p-2">
                <x-heroicon-o-x-mark class="h-6 w-6" />
            </button>
        </div>
        <div id="modal-content" class="p-6">
            <!-- Content will be inserted here -->
        </div>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let map;
let markers = [];
let allTasksData = [];
let groupedData = [];

// Initialize Map
function initMap() {
    // Center on Indonesia (Jakarta)
    map = L.map('map').setView([-6.2088, 106.8456], 11);
    
    // Add OpenFreeMap tile layer (Hot style - more colorful)
    L.tileLayer('https://tiles.openfreemap.org/hot/{z}/{x}/{y}.png', {
        attribution: '© OpenFreeMap contributors',
        maxZoom: 19
    }).addTo(map);
}

// Load data from API
async function loadData() {
    try {
        const response = await fetch('{{ route('courier.route.map.data') }}');
        const data = await response.json();
        
        allTasksData = data.all_tasks;
        groupedData = data.grouped_by_area;
        
        // Update statistics
        document.getElementById('total-deliveries').textContent = data.total_deliveries;
        document.getElementById('total-returns').textContent = data.total_returns;
        document.getElementById('total-areas').textContent = data.grouped_by_area.length;
        
        // Render markers and list
        renderMarkers();
        renderAreaList();
    } catch (error) {
        console.error('Error loading data:', error);
        alert('Gagal memuat data. Silakan refresh halaman.');
    }
}

// Render markers on map
function renderMarkers() {
    // Clear existing markers
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    
    const filterType = document.getElementById('filter-type').value;
    const filterPriority = document.getElementById('filter-priority').value;
    
    // Filter data
    let filteredData = allTasksData;
    if (filterType !== 'all') {
        filteredData = filteredData.filter(task => task.type === filterType);
    }
    if (filterPriority !== 'all') {
        filteredData = filteredData.filter(task => task.priority === filterPriority);
    }
    
    // Group filtered data by address
    const groups = {};
    filteredData.forEach(task => {
        if (!groups[task.address]) {
            groups[task.address] = [];
        }
        groups[task.address].push(task);
    });
    
    // Since we don't have lat/lng, we'll use a simple grid layout
    // In production, you should geocode addresses to get real coordinates
    const centerLat = -6.2088;
    const centerLng = 106.8456;
    const addresses = Object.keys(groups);
    
    addresses.forEach((address, index) => {
        const tasks = groups[address];
        
        // Create a pseudo-location based on address hash for demo
        // In production, use real geocoding
        const hash = address.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0);
        const offsetLat = ((hash % 200) - 100) * 0.001; // ±0.1 degrees
        const offsetLng = ((Math.floor(hash / 200) % 200) - 100) * 0.001;
        
        const lat = centerLat + offsetLat;
        const lng = centerLng + offsetLng;
        
        // Determine marker color
        const hasDelivery = tasks.some(t => t.type === 'delivery');
        const hasReturn = tasks.some(t => t.type === 'return');
        let markerColor = 'blue';
        if (hasDelivery && hasReturn) markerColor = 'purple';
        else if (hasDelivery) markerColor = 'green';
        else if (hasReturn) markerColor = 'orange';
        
        // Create custom icon
        const icon = L.divIcon({
            className: 'custom-marker',
            html: `<div class="relative">
                    <div class="absolute -top-10 -left-5 bg-${markerColor}-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold shadow-lg">
                        ${tasks.length}
                    </div>
                   </div>`,
            iconSize: [40, 40]
        });
        
        const marker = L.marker([lat, lng], { icon }).addTo(map);
        marker.on('click', () => showTaskDetails(address, tasks));
        markers.push(marker);
    });
    
    // Fit bounds if markers exist
    if (markers.length > 0) {
        const group = L.featureGroup(markers);
        map.fitBounds(group.getBounds().pad(0.1));
    }
}

// Render area list
function renderAreaList() {
    const container = document.getElementById('area-list');
    const filterType = document.getElementById('filter-type').value;
    const filterPriority = document.getElementById('filter-priority').value;
    
    // Filter grouped data
    let filtered = groupedData.map(group => {
        let tasks = group.tasks;
        if (filterType !== 'all') {
            tasks = tasks.filter(t => t.type === filterType);
        }
        if (filterPriority !== 'all') {
            tasks = tasks.filter(t => t.priority === filterPriority);
        }
        return { ...group, tasks, count: tasks.length };
    }).filter(group => group.count > 0);
    
    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="p-8 text-center text-gray-500">
                <svg class="h-16 w-16 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="font-medium">Tidak ada task yang sesuai filter</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = filtered.map(group => {
        const deliveryCount = group.tasks.filter(t => t.type === 'delivery').length;
        const returnCount = group.tasks.filter(t => t.type === 'return').length;
        
        return `
            <div class="p-4 hover:bg-gray-50 cursor-pointer transition" onclick='showTaskDetails(${JSON.stringify(group.address)}, ${JSON.stringify(group.tasks)})'>
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800 mb-1">${group.address}</h4>
                        <div class="flex items-center gap-3 text-sm">
                            ${deliveryCount > 0 ? `<span class="text-green-600 flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                ${deliveryCount} Kirim
                            </span>` : ''}
                            ${returnCount > 0 ? `<span class="text-orange-600 flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                ${returnCount} Jemput
                            </span>` : ''}
                        </div>
                    </div>
                    <div class="bg-green-100 text-green-800 rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm">
                        ${group.count}
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Show task details in modal
function showTaskDetails(address, tasks) {
    const modal = document.getElementById('task-modal');
    const content = document.getElementById('modal-content');
    
    const deliveryTasks = tasks.filter(t => t.type === 'delivery');
    const returnTasks = tasks.filter(t => t.type === 'return');
    
    content.innerHTML = `
        <div class="mb-6">
            <h4 class="font-bold text-lg text-gray-800 mb-2">📍 Alamat Tujuan</h4>
            <p class="text-gray-700 bg-gray-50 p-3 rounded-lg">${address}</p>
        </div>
        
        ${deliveryTasks.length > 0 ? `
            <div class="mb-6">
                <h4 class="font-bold text-lg text-green-700 mb-3 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Pengiriman (${deliveryTasks.length})
                </h4>
                <div class="space-y-3">
                    ${deliveryTasks.map(task => `
                        <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-gray-800">${task.booking_code}</span>
                                <span class="px-2 py-1 text-xs rounded-full ${task.priority === 'high' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'}">
                                    ${task.status_label}
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 mb-1">👤 ${task.customer_name}</p>
                            <p class="text-sm text-gray-700 mb-1">📦 ${task.item_name}</p>
                            <p class="text-sm text-gray-600">📞 ${task.customer_phone}</p>
                            <a href="{{ route('courier.deliveries.show', ['type' => 'product', 'id' => '__ID__']) }}".replace('__ID__', task.id).replace('product', task.booking_type) 
                               class="inline-block mt-2 text-green-600 hover:text-green-700 text-sm font-medium">
                                Lihat Detail →
                            </a>
                        </div>
                    `).join('')}
                </div>
            </div>
        ` : ''}
        
        ${returnTasks.length > 0 ? `
            <div class="mb-6">
                <h4 class="font-bold text-lg text-orange-700 mb-3 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                    </svg>
                    Penjemputan (${returnTasks.length})
                </h4>
                <div class="space-y-3">
                    ${returnTasks.map(task => `
                        <div class="border border-orange-200 rounded-lg p-4 bg-orange-50">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-semibold text-gray-800">${task.booking_code}</span>
                                <span class="px-2 py-1 text-xs rounded-full ${task.priority === 'high' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'}">
                                    ${task.status_label}
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 mb-1">👤 ${task.customer_name}</p>
                            <p class="text-sm text-gray-700 mb-1">📦 ${task.item_name}</p>
                            <p class="text-sm text-gray-600">📞 ${task.customer_phone}</p>
                            <a href="{{ route('courier.deliveries.show', ['type' => 'product', 'id' => '__ID__']) }}".replace('__ID__', task.id).replace('product', task.booking_type) 
                               class="inline-block mt-2 text-orange-600 hover:text-orange-700 text-sm font-medium">
                                Lihat Detail →
                            </a>
                        </div>
                    `).join('')}
                </div>
            </div>
        ` : ''}
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-800">
                💡 <strong>Tips:</strong> Kunjungi lokasi ini untuk menyelesaikan ${deliveryTasks.length + returnTasks.length} task sekaligus dan hemat waktu perjalanan.
            </p>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('task-modal').classList.add('hidden');
}

// Event Listeners
document.getElementById('filter-type').addEventListener('change', () => {
    renderMarkers();
    renderAreaList();
});

document.getElementById('filter-priority').addEventListener('change', () => {
    renderMarkers();
    renderAreaList();
});

document.getElementById('btn-refresh').addEventListener('click', loadData);

// Initialize on load
document.addEventListener('DOMContentLoaded', () => {
    initMap();
    loadData();
});
</script>

<style>
.custom-marker {
    background: transparent;
    border: none;
}

/* Custom scrollbar */
#area-list::-webkit-scrollbar {
    width: 6px;
}

#area-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#area-list::-webkit-scrollbar-thumb {
    background: #10b981;
    border-radius: 3px;
}

#area-list::-webkit-scrollbar-thumb:hover {
    background: #059669;
}
</style>
@endpush
