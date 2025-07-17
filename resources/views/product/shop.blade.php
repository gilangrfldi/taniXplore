<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight mt-11">
            {{ __('Shop') }}
        </h2>
    </x-slot>

    <style>
        #modalMap {
            height: 400px;
            width: 100%;
            z-index: 1000;
            border-radius: 0.5rem;
        }

        .leaflet-container {
            z-index: 1000;
        }
    </style>
    <div class="py-4">
        <div class="max-w-7xl mx-auto">
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            <div class="overflow-hidden">
                <div class="p-6 text-gray-900">
                    @if ($products->isEmpty())
                        <p class="text-center text-gray-500">Tidak ada produk yang tersedia.</p>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach ($products as $product)
                            <div class="rounded-lg shadow-md p-4 cursor-pointer
                                        shadow-light dark:shadow-[2px_2px_2px_rgba(0,0,0,1),-2px_-2px_2px_rgba(64,64,64,0.63)] 
                                        transition duration-300 ease-in-out"
                                onclick="showProductModal({{ json_encode($product) }})">
                                @if ($product->image)
                                    <img src="{{ asset('storage/images/' . $product->image) }}"
                                        alt="{{ $product->name }}"
                                        class="w-full h-48 rounded-lg mb-4 bg-white dark:bg-[#1A1A1A]">
                                @else
                                    <p
                                        class="w-full h-48 rounded-lg mb-4 text-black dark:text-white bg-white dark:bg-[#1A1A1A]">
                                        Gambar tidak tersedia</p>
                                @endif
                                <h3 class="text-lg font-semibold text-black dark:text-white">{{ $product->name }}</h3>
                                <p class="text-gray-600 dark:text-gray-300">Rp.
                                    {{ number_format($product->price, 0, ',', '.') }}</p>
                                <div class="flex items-center gap-1.5 mt-1.5">
                                    <div class="flex items-center gap-0.5">
                                        @php
                                            $rating = round($product->ratings()->avg('rating') ?? 0);
                                        @endphp
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $rating)
                                                <span class="text-yellow-400 text-base">★</span>
                                            @else
                                                <span class="text-gray-300 text-base">★</span>
                                            @endif
                                        @endfor
                                    </div>
                                    <span
                                        class="text-sm text-gray-700 dark:text-gray-300">{{ number_format($product->ratings()->avg('rating') ?? 0, 1) }}</span>
                                    <div class="flex items-center text-sm">
                                        <span
                                            class="text-gray-500 dark:text-gray-400">{{ $product->ratings()->count() }}</span>
                                        <span class="text-gray-500 dark:text-gray-400 ml-1">Penilaian</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Product Modal -->
    <div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div
            class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md sm:max-w-lg md:max-w-2xl lg:max-w-3xl mx-4 overflow-auto max-h-[80vh] mt-14">
            <div
                class="sticky top-0 bg-white dark:bg-gray-800 z-10 flex justify-between items-center px-6 py-4 border-b dark:border-gray-700">
                <div class="flex flex-row items-center gap-2">
                    <h2 id="modalTitle" class="text-xl font-bold text-gray-800 dark:text-white"></h2>
                    <span id="modalNewLabel"
                        class="inline-block bg-green-500 text-white text-xs font-semibold px-2 py-1 rounded-full">
                        New
                    </span>
                </div>
                <button onclick="closeProductModal()"
                    class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 p-2">
                    <i class="fa-solid fa-x"></i>
                </button>
            </div>
            <div class="p-6 space-y-6 overflow-y-auto max-h-[calc(100vh-16rem)]">
                <img id="modalImage" class="w-full h-64 object-cover rounded-lg" src="" alt="foto produk">
                <span id="modalDate" class="text-gray-600 dark:text-gray-400">{{ $product->date_info }}</span>

                <div class="space-y-1">
                    <span class="text-lg font-semibold text-gray-800 dark:text-white">Seller:</span>
                    <p id="modalUser" class="text-green-600 dark:text-green-400 font-medium"></p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <span class="font-medium text-gray-800 dark:text-white">Price:</span>
                        <p id="modalPrice" class="text-gray-600 dark:text-gray-400"></p>
                    </div>
                    <div class="space-y-1">
                        <span class="font-medium text-gray-800 dark:text-white">Stock:</span>
                        <p id="modalStock" class="text-gray-600 dark:text-gray-400"></p>
                    </div>
                </div>
                <div class="space-y-1">
                    <span class="font-medium text-gray-800 dark:text-white">Description:</span>
                    <p id="modalDescription" class="text-gray-600 dark:text-gray-400"></p>
                </div>
                <div class="space-y-1">
                    <span class="font-medium text-gray-800 dark:text-white">Grade:</span>
                    <p id="modalGrade" class="text-gray-600 dark:text-gray-400"></p>
                </div>
                <div class="space-y-2">
                    <div class="space-y-1">
                        <span class="font-medium text-gray-800 dark:text-white">Address:</span>
                        <p id="modalAddress" class="text-gray-600 dark:text-gray-400"></p>
                        <p>
                            <span class="font-medium text-gray-800 dark:text-white">Detailed Address:</span>
                            <span id="modalAddresDetail" class="text-gray-600 dark:text-gray-400"></span>
                        </p>
                    </div>
                    <div id="modalMap" class="w-full h-64 rounded-lg overflow-hidden"></div>
                </div>
                <div class="flex justify-center space-x-4 mt-4">
                    <a id="editButton" href="#"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center transition-colors h-10">Edit</a>
                    <form id="deleteForm" action="#" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confirmDelete()"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg flex items-center  transition-colors h-10">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let map = null;
        let marker = null;
        let currentProduct = null;

        function showProductModal(product) {
            if (!product || !product.id) {
                console.error('Data produk tidak valid.');
                return;
            }
            document.getElementById('modalTitle').textContent = product.name;
            document.getElementById('modalImage').src = `/storage/images/${product.image}`;
            document.getElementById('modalUser').textContent = product.user ? product.user.name : '-';
            document.getElementById('modalPrice').textContent = 'Rp. ' + new Intl.NumberFormat('id-ID').format(product
                .price);
            document.getElementById('modalStock').textContent = `${product.stock} kg`;
            document.getElementById('modalDescription').textContent = product.description || '-';
            document.getElementById('modalGrade').textContent = product.grade || '-';
            document.getElementById('modalAddress').textContent = product.address || '-';
            document.getElementById('modalAddresDetail').textContent = product.addres_detail || '-';

            const modal = document.getElementById('productModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');

            const latitude = parseFloat(product.latitude);
            const longitude = parseFloat(product.longitude);

            if (!isNaN(latitude) && !isNaN(longitude)) {
                const map = new google.maps.Map(document.getElementById('modalMap'), {
                    center: {
                        lat: latitude,
                        lng: longitude
                    },
                    zoom: 15,
                });

                marker = new google.maps.Marker({
                    position: {
                        lat: latitude,
                        lng: longitude
                    },
                    map: map,
                });
                document.getElementById('modalMap').style.display = 'block';
            } else {
                console.error('Koordinat tidak valid:', latitude, longitude);
                document.getElementById('modalMap').style.display = 'none';
            }

            const editButton = document.getElementById('editButton');
            editButton.href = `/product/${product.id}/edit?latitude=${product.latitude}&longitude=${product.longitude}`;

            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/product/${product.id}`;
        }

        function closeProductModal() {
            const modal = document.getElementById('productModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
            if (marker && marker.setMap) {
                marker.setMap(null);
            }
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeProductModal();
            }
        });

        function confirmDelete() {
            if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
</x-app-layout>
