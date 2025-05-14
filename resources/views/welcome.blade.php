<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ABC</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50 text-gray-900 flex items-center justify-center min-h-screen">
        <div class="max-w-md w-full mx-auto bg-white rounded-xl shadow-md overflow-hidden p-8">
            
            <h1 class="text-2xl font-bold text-center text-gray-800 mb-8">Welcome back from ABC</h1>
            
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            
            <form action="/" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="domain" class="block text-sm font-medium text-gray-700 mb-2">Your organization domain URL</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input type="text" name="domain" id="domain" 
                               class="block w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Enter domain name (e.g., one)" required>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">.localhost</span>
                        </div>
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md transition duration-300 shadow-sm">
                    Continue
                </button>
            </form>
            
            <div class="mt-6 text-center text-xs text-gray-500">
                <p>Need help? Contact <a href="#" class="text-blue-600 hover:text-blue-800">{{ config('abc.support.email') }}</a></p>
            </div>
        </div>
    </body>
</html>