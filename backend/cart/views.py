from rest_framework import viewsets, permissions, status
from rest_framework.response import Response
from rest_framework.decorators import action
from django.shortcuts import get_object_or_404
from .models import CartItem, WishlistItem
from products.models import Product
from .serializers import CartItemSerializer, WishlistItemSerializer

class CartViewSet(viewsets.ModelViewSet):
    serializer_class = CartItemSerializer
    permission_classes = [permissions.IsAuthenticated]

    def get_queryset(self):
        return CartItem.objects.filter(user=self.request.user).order_by('-id')

    @action(detail=False, methods=['post'])
    def add(self, request):
        product_id = request.data.get('product_id')
        quantity = int(request.data.get('quantity', 1))

        if not product_id:
            return Response({"success": False, "message": "Product ID is required"}, status=status.HTTP_400_BAD_REQUEST)

        product = get_object_or_404(Product, pk=product_id)

        # Check if item already exists in cart
        cart_item, created = CartItem.objects.get_or_create(
            user=request.user,
            product=product,
            defaults={
                "product_code": product.product_code,
                "table_name": "products",
                "quantity": quantity
            }
        )

        if not created:
            cart_item.quantity += quantity
            cart_item.save()

        return Response({
            "success": True,
            "message": "Item added to cart successfully.",
            "data": CartItemSerializer(cart_item).data
        })

    @action(detail=False, methods=['post'])
    def remove(self, request):
        product_id = request.data.get('product_id')
        quantity = int(request.data.get('quantity', 1))

        if not product_id:
            return Response({"success": False, "message": "Product ID is required"}, status=status.HTTP_400_BAD_REQUEST)

        cart_item = get_object_or_404(CartItem, user=request.user, product_id=product_id)

        if cart_item.quantity > quantity:
            cart_item.quantity -= quantity
            cart_item.save()
            return Response({
                "success": True,
                "message": "Cart item quantity updated.",
                "data": CartItemSerializer(cart_item).data
            })
        else:
            cart_item.delete()
            return Response({
                "success": True,
                "message": "Item removed from cart completely."
            })

class WishlistViewSet(viewsets.ModelViewSet):
    serializer_class = WishlistItemSerializer
    permission_classes = [permissions.IsAuthenticated]

    def get_queryset(self):
        return WishlistItem.objects.filter(user=self.request.user).order_by('-id')

    @action(detail=False, methods=['post'])
    def toggle(self, request):
        product_id = request.data.get('product_id')

        if not product_id:
            return Response({"success": False, "message": "Product ID is required"}, status=status.HTTP_400_BAD_REQUEST)

        product = get_object_or_404(Product, pk=product_id)

        # Toggle presence in wishlist
        wishlist_item = WishlistItem.objects.filter(user=request.user, product=product).first()
        if wishlist_item:
            wishlist_item.delete()
            action_performed = 'removed'
        else:
            WishlistItem.objects.create(
                user=request.user,
                product=product,
                table_name='products'
            )
            action_performed = 'added'

        count = WishlistItem.objects.filter(user=request.user).count()
        return Response({
            "success": True,
            "action": action_performed,
            "count": count,
            "message": f"Product successfully {action_performed} from wishlist."
        })
