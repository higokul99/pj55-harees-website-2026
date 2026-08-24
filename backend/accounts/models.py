from django.db import models
from django.contrib.auth.models import AbstractBaseUser, BaseUserManager, PermissionsMixin
from datetime import date

class UserManager(BaseUserManager):
    def create_user(self, email, phone, password=None, **extra_fields):
        if not email:
            raise ValueError("The Email field must be set")
        if not phone:
            raise ValueError("The Phone field must be set")
        email = self.normalize_email(email)
        user = self.model(email=email, phone=phone, **extra_fields)
        user.set_password(password)
        user.save(using=self._db)
        return user

    def create_superuser(self, email, phone, password=None, **extra_fields):
        extra_fields.setdefault('is_staff', True)
        extra_fields.setdefault('is_superuser', True)
        extra_fields.setdefault('fullname', 'Administrator')
        extra_fields.setdefault('address1', 'Admin Address')
        extra_fields.setdefault('city', 'Admin City')
        extra_fields.setdefault('state', 'Admin State')
        extra_fields.setdefault('pincode', '000000')
        extra_fields.setdefault('security_question', 'What is your role?')
        extra_fields.setdefault('security_answer', 'admin')

        return self.create_user(email, phone, password, **extra_fields)

class User(AbstractBaseUser, PermissionsMixin):
    fullname = models.CharField(max_length=100)
    name = models.CharField(max_length=255, blank=True, null=True) # Laravel compatibility
    email = models.EmailField(unique=True)
    phone = models.CharField(max_length=15, unique=True)
    security_question = models.CharField(max_length=255)
    security_answer = models.CharField(max_length=255)
    address1 = models.CharField(max_length=255)
    address2 = models.CharField(max_length=255, blank=True, null=True)
    city = models.CharField(max_length=50)
    state = models.CharField(max_length=50)
    pincode = models.CharField(max_length=10)
    dob = models.DateField(default=date(1970, 1, 1))
    anniversary = models.DateField(blank=True, null=True)
    landmark = models.CharField(max_length=100, blank=True, null=True)

    is_active = models.BooleanField(default=True)
    is_staff = models.BooleanField(default=False)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    objects = UserManager()

    USERNAME_FIELD = 'email'
    REQUIRED_FIELDS = ['phone']

    def __str__(self):
        return self.email
