# PowerShell API Test Script for AplikasiPinjam
# Make sure the server is running: php artisan serve

$BASE_URL = "http://localhost:8000/api"

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "Testing AplikasiPinjam API" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

# Test 1: Register new user
Write-Host "1. Testing Register..." -ForegroundColor Yellow
$registerBody = @{
    name = "API Test User"
    email = "apitest@example.com"
    password = "password123"
    password_confirmation = "password123"
} | ConvertTo-Json

try {
    $registerResponse = Invoke-RestMethod -Uri "$BASE_URL/register" -Method Post -Body $registerBody -ContentType "application/json"
    Write-Host "Register Response:" -ForegroundColor Green
    $registerResponse | ConvertTo-Json
} catch {
    Write-Host "Register failed (user might already exist)" -ForegroundColor Red
}
Write-Host ""

# Test 2: Login
Write-Host "2. Testing Login..." -ForegroundColor Yellow
$loginBody = @{
    email = "test@example.com"
    password = "password"
} | ConvertTo-Json

$loginResponse = Invoke-RestMethod -Uri "$BASE_URL/login" -Method Post -Body $loginBody -ContentType "application/json"
$TOKEN = $loginResponse.access_token
Write-Host "Access Token: $TOKEN" -ForegroundColor Green
Write-Host ""

# Create headers with token
$headers = @{
    "Authorization" = "Bearer $TOKEN"
    "Content-Type" = "application/json"
}

# Test 3: Get authenticated user
Write-Host "3. Testing Get Authenticated User..." -ForegroundColor Yellow
$meResponse = Invoke-RestMethod -Uri "$BASE_URL/me" -Method Get -Headers $headers
Write-Host "Authenticated User:" -ForegroundColor Green
$meResponse | ConvertTo-Json
Write-Host ""

# Test 4: Get categories (public)
Write-Host "4. Testing Get Categories (Public)..." -ForegroundColor Yellow
$categoriesResponse = Invoke-RestMethod -Uri "$BASE_URL/categories" -Method Get
Write-Host "Categories (first 3):" -ForegroundColor Green
$categoriesResponse.data | Select-Object -First 3 | ConvertTo-Json
Write-Host ""

# Test 5: Get products (public)
Write-Host "5. Testing Get Products (Public)..." -ForegroundColor Yellow
$productsResponse = Invoke-RestMethod -Uri "$BASE_URL/products" -Method Get
Write-Host "Products (first 3):" -ForegroundColor Green
$productsResponse.data | Select-Object -First 3 | ConvertTo-Json
Write-Host ""

# Test 6: Get packages (public)
Write-Host "6. Testing Get Packages (Public)..." -ForegroundColor Yellow
$packagesResponse = Invoke-RestMethod -Uri "$BASE_URL/packages" -Method Get
Write-Host "Packages (first 3):" -ForegroundColor Green
$packagesResponse.data | Select-Object -First 3 | ConvertTo-Json
Write-Host ""

# Test 7: Create category (protected)
Write-Host "7. Testing Create Category (Protected)..." -ForegroundColor Yellow
$categoryBody = @{
    categories = "Test Category from PowerShell API"
} | ConvertTo-Json

try {
    $createCategoryResponse = Invoke-RestMethod -Uri "$BASE_URL/categories" -Method Post -Body $categoryBody -Headers $headers
    Write-Host "Created Category:" -ForegroundColor Green
    $createCategoryResponse | ConvertTo-Json
} catch {
    Write-Host "Create category failed: $_" -ForegroundColor Red
}
Write-Host ""

# Test 8: Get users (protected)
Write-Host "8. Testing Get Users (Protected)..." -ForegroundColor Yellow
$usersResponse = Invoke-RestMethod -Uri "$BASE_URL/users" -Method Get -Headers $headers
Write-Host "Users (first 3):" -ForegroundColor Green
$usersResponse.data | Select-Object -First 3 | ConvertTo-Json
Write-Host ""

# Test 9: Search products
Write-Host "9. Testing Search Products..." -ForegroundColor Yellow
try {
    $searchResponse = Invoke-RestMethod -Uri "$BASE_URL/products?search=tent" -Method Get
    Write-Host "Search Results (first 3):" -ForegroundColor Green
    $searchResponse.data | Select-Object -First 3 | ConvertTo-Json
} catch {
    Write-Host "No results found or error: $_" -ForegroundColor Red
}
Write-Host ""

# Test 10: Logout
Write-Host "10. Testing Logout..." -ForegroundColor Yellow
$logoutResponse = Invoke-RestMethod -Uri "$BASE_URL/logout" -Method Post -Headers $headers
Write-Host "Logout Response:" -ForegroundColor Green
$logoutResponse | ConvertTo-Json
Write-Host ""

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "API Tests Completed!" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
