#!/bin/bash
# API Test Script for AplikasiPinjam
# Make sure the server is running: php artisan serve

BASE_URL="http://localhost:8000/api"

echo "========================================="
echo "Testing AplikasiPinjam API"
echo "========================================="
echo ""

# Test 1: Register new user
echo "1. Testing Register..."
REGISTER_RESPONSE=$(curl -s -X POST "$BASE_URL/register" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "API Test User",
    "email": "apitest@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }')

echo "$REGISTER_RESPONSE" | jq '.'
echo ""

# Test 2: Login
echo "2. Testing Login..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password"
  }')

TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.access_token')
echo "Access Token: $TOKEN"
echo ""

# Test 3: Get authenticated user
echo "3. Testing Get Authenticated User..."
curl -s -X GET "$BASE_URL/me" \
  -H "Authorization: Bearer $TOKEN" | jq '.'
echo ""

# Test 4: Get categories (public)
echo "4. Testing Get Categories (Public)..."
curl -s -X GET "$BASE_URL/categories" | jq '.data | .[0:3]'
echo ""

# Test 5: Get products (public)
echo "5. Testing Get Products (Public)..."
curl -s -X GET "$BASE_URL/products" | jq '.data | .[0:3]'
echo ""

# Test 6: Get packages (public)
echo "6. Testing Get Packages (Public)..."
curl -s -X GET "$BASE_URL/packages" | jq '.data | .[0:3]'
echo ""

# Test 7: Create category (protected)
echo "7. Testing Create Category (Protected)..."
curl -s -X POST "$BASE_URL/categories" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "categories": "Test Category from API"
  }' | jq '.'
echo ""

# Test 8: Get users (protected)
echo "8. Testing Get Users (Protected)..."
curl -s -X GET "$BASE_URL/users" \
  -H "Authorization: Bearer $TOKEN" | jq '.data | .[0:3]'
echo ""

# Test 9: Search products
echo "9. Testing Search Products..."
curl -s -X GET "$BASE_URL/products?search=tent" | jq '.data | .[0:3]'
echo ""

# Test 10: Logout
echo "10. Testing Logout..."
curl -s -X POST "$BASE_URL/logout" \
  -H "Authorization: Bearer $TOKEN" | jq '.'
echo ""

echo "========================================="
echo "API Tests Completed!"
echo "========================================="
