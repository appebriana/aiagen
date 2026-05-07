<div id="skeleton-loader" class="fixed inset-0 z-[9999] bg-secondary-200 flex overflow-hidden">
    {{-- Sidebar Skeleton --}}
    <div class="w-64 bg-primary-900 h-full hidden lg:block shrink-0">
        <div class="h-16 border-b border-primary-800 flex items-center px-4">
            <div class="w-8 h-8 bg-primary-800 rounded-lg animate-pulse"></div>
            <div class="ml-3 w-24 h-5 bg-primary-800 rounded animate-pulse"></div>
        </div>
        <div class="p-4 space-y-4 mt-4">
            <div class="h-10 bg-primary-800 rounded-lg animate-pulse w-full"></div>
            <div class="h-10 bg-primary-800 rounded-lg animate-pulse w-full"></div>
            <div class="h-10 bg-primary-800 rounded-lg animate-pulse w-full"></div>
        </div>
    </div>

    {{-- Main Content Skeleton --}}
    <div class="flex-1 flex flex-col">
        {{-- Header Skeleton --}}
        <div class="h-16 bg-white border-b border-secondary-300 flex items-center justify-between px-8">
            <div class="w-32 h-6 bg-secondary-200 rounded animate-pulse"></div>
            <div class="flex items-center gap-4">
                <div class="w-20 h-8 bg-secondary-200 rounded-lg animate-pulse"></div>
                <div class="w-20 h-8 bg-secondary-200 rounded-lg animate-pulse"></div>
                <div class="w-10 h-10 bg-secondary-200 rounded-full animate-pulse"></div>
            </div>
        </div>

        {{-- Body Skeleton --}}
        <div class="p-8 space-y-6">
            <div class="h-8 bg-secondary-300 rounded w-1/4 animate-pulse"></div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="h-32 bg-white rounded-2xl border border-secondary-300 animate-pulse shadow-sm"></div>
                <div class="h-32 bg-white rounded-2xl border border-secondary-300 animate-pulse shadow-sm"></div>
                <div class="h-32 bg-white rounded-2xl border border-secondary-300 animate-pulse shadow-sm"></div>
            </div>
            <div class="h-64 bg-white rounded-2xl border border-secondary-300 animate-pulse shadow-sm w-full"></div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', function() {
        const loader = document.getElementById('skeleton-loader');
        if (loader) {
            loader.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        }
    });
</script>
