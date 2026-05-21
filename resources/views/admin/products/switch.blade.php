<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Switch Serial Number</h2>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="p-8 bg-white dark:bg-dark-eval-1 rounded-2xl border-2 border-orange-100 dark:border-orange-800">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">Instructions:</h3>
                <ol class="list-decimal list-inside space-y-1 text-gray-600 dark:text-gray-400">
                    <li>Scan the OLD serial number (product to be replaced)</li>
                    <li>Scan the NEW serial number (replacement product)</li>
                    <li>Click Submit to switch</li>
                </ol>
            </div>

            <form id="switchForm" method="POST" action="{{ route('products.switch.submit') }}">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Old Serial Number *
                    </label>
                    <input 
                        type="text" 
                        id="old_serial" 
                        name="old_serial" 
                        required
                        autofocus
                        class="w-full px-4 py-3 border-2 border-orange-300 dark:border-orange-700 rounded-lg focus:ring-4 focus:ring-orange-500 dark:bg-dark-eval-2 text-lg font-mono"
                        placeholder="Scan old serial number">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        New Serial Number *
                    </label>
                    <input 
                        type="text" 
                        id="new_serial" 
                        name="new_serial" 
                        required
                        class="w-full px-4 py-3 border-2 border-green-300 dark:border-green-700 rounded-lg focus:ring-4 focus:ring-green-500 dark:bg-dark-eval-2 text-lg font-mono"
                        placeholder="Scan new serial number">
                </div>

                <div id="info_box" class="hidden mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded">
                    <p class="text-sm text-blue-700 dark:text-blue-400" id="info_text"></p>
                </div>

                <div class="flex gap-3">
                    <button 
                        type="submit" 
                        id="submit_btn"
                        class="flex-1 px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-lg transition-colors">
                        Switch Serial Number
                    </button>
                    <a href="{{ route('products.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 font-bold rounded-lg transition-colors">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const oldSerialInput = document.getElementById('old_serial');
        const newSerialInput = document.getElementById('new_serial');
        const infoBox = document.getElementById('info_box');
        const infoText = document.getElementById('info_text');

        // Auto focus next field after scan
        oldSerialInput.addEventListener('change', async function() {
            if (this.value) {
                // Check if old serial exists
                try {
                    const response = await fetch(`/api/check-serial/${this.value}`);
                    const data = await response.json();
                    
                    if (!data.exists) {
                        alert('Old serial number not found in database!');
                        this.value = '';
                        this.focus();
                        return;
                    }
                    
                    infoBox.classList.remove('hidden');
                    infoText.textContent = `Old serial "${this.value}" found. Now scan the new serial number.`;
                    newSerialInput.focus();
                } catch (error) {
                    console.error('Error checking serial:', error);
                }
            }
        });

        newSerialInput.addEventListener('change', async function() {
            if (this.value) {
                // Check if new serial already exists
                try {
                    const response = await fetch(`/api/check-serial/${this.value}`);
                    const data = await response.json();
                    
                    if (data.exists) {
                        alert('New serial number already exists in database!');
                        this.value = '';
                        this.focus();
                        return;
                    }
                    
                    infoText.textContent = `Ready to switch "${oldSerialInput.value}" to "${this.value}". Click Submit.`;
                } catch (error) {
                    console.error('Error checking serial:', error);
                }
            }
        });
    </script>
</x-app-layout>
