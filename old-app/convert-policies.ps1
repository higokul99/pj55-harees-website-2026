# Convert old PHP policy pages to Laravel Blade
$policies = @(
    'privacy-policy',
    'refund-policy',
    'terms-and-conditions',
    'return-or-exchange-policy',
    'shipping-policy',
    'cancellation-policy'
)

foreach ($policy in $policies) {
    $oldFile = "resources\views\harees\$policy.php"
    $newFile = "resources\views\pages\$policy.blade.php"
    
    if (Test-Path $oldFile) {
        Write-Host "Converting $policy..."
        
        # Read old file
        $content = Get-Content $oldFile -Raw
        
        # Remove PHP session and includes
        $content = $content -replace '<\?php[\s\S]*?\?>', ''
        
        # Remove DOCTYPE and html tags (will use layout)
        $content = $content -replace '<!DOCTYPE html>', ''
        $content = $content -replace '<html[^>]*>', ''
        $content = $content -replace '</html>', ''
        $content = $content -replace '<head>[\s\S]*?</head>', ''
        
        # Remove body tags
        $content = $content -replace '<body[^>]*>', ''
        $content = $content -replace '</body>', ''
        
        # Remove footer include
        $content = $content -replace '<\?php include.*?footer.*?\?>', ''
        
        # Wrap in Blade layout
        $bladeContent = "@extends('layouts.app')

@push('styles')
<style>
@keyframes fadeIn {
    0% { opacity: 0; transform: translateY(10px); }
    100% { opacity: 1; transform: translateY(0); }
}
@keyframes slideUp {
    0% { transform: translateY(10px); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
}
.animate-fade-in {
    animation: fadeIn 0.5s ease-in-out;
}
.animate-slide-up {
    animation: slideUp 0.3s ease-out;
}
</style>
@endpush

@section('content')
$content
@endsection"
        
        # Replace PHP date with Blade
        $bladeContent = $bladeContent -replace '<\?php echo date\([^)]+\); \?>', '{{ date("F j, Y") }}'
        
        # Replace hardcoded email/phone with config
        $bladeContent = $bladeContent -replace 'hareesjewelleryinfo@gmail\.com', '{{ config("shop.contact.email") }}'
        $bladeContent = $bladeContent -replace '\+91 8921387392', '{{ config("shop.contact.phone") }}'
        $bladeContent = $bladeContent -replace '8921387392', '{{ str_replace(["+", " "], "", config("shop.contact.phone")) }}'
        
        # Fix route links
        $bladeContent = $bladeContent -replace 'href="([a-z-]+)"', 'href="{{ route("$1") }}"'
        
        # Save
        Set-Content $newFile $bladeContent
        Write-Host "✓ Created $newFile"
    }
}

Write-Host "`nAll policy pages converted!"
