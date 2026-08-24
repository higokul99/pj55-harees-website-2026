import { Component, OnInit, signal, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { AuthService } from '../../services/auth.service';

interface CategoryItem {
  type: string;
  image: string;
  name: string;
}

@Component({
  selector: 'app-home',
  standalone: true,
  imports: [CommonModule, RouterLink],
  templateUrl: './home.html',
  styleUrl: './home.scss'
})
export class HomeComponent implements OnInit {
  userEmail = computed(() => this.authService.currentUser()?.email || '');
  
  // Hero Carousel State
  currentHeroIndex = signal<number>(0);
  heroBanners = [
    { title: 'Royal Gold Collection', subtitle: 'Exquisite designs crafted with pure 22K gold.', image: 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?q=80&w=1200&auto=format&fit=crop' },
    { title: 'The Diamond Brilliance', subtitle: 'Shine bright with certified VVS clarity diamonds.', image: 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?q=80&w=1200&auto=format&fit=crop' },
    { title: 'Sparkling Gemstones', subtitle: 'Timeless grace embedded in rubies and emeralds.', image: 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?q=80&w=1200&auto=format&fit=crop' },
  ];

  // Categories list matching legacy types
  categories: CategoryItem[] = [
    { type: 'rings', image: 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=150&auto=format&fit=crop', name: 'Rings' },
    { type: 'anklets', image: 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=150&auto=format&fit=crop', name: 'Anklets' },
    { type: 'bangles', image: 'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=150&auto=format&fit=crop', name: 'Bangles' },
    { type: 'earrings', image: 'https://images.unsplash.com/photo-1630019852942-f89202989a59?w=150&auto=format&fit=crop', name: 'Earrings' },
    { type: 'nosepins', image: 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=150&auto=format&fit=crop', name: 'Nosepins' },
    { type: 'necklaces', image: 'https://images.unsplash.com/photo-1599643477877-530eb83abc8e?w=150&auto=format&fit=crop', name: 'Necklaces' },
    { type: 'bracelets', image: 'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=150&auto=format&fit=crop', name: 'Bracelets' },
    { type: 'pendants', image: 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=150&auto=format&fit=crop', name: 'Pendants' },
  ];

  // Celebration Popup State
  showCelebration = signal<boolean>(false);
  celebrationType = signal<'birthday' | 'anniversary' | null>(null);
  celebrationMessage = signal<string>('');

  constructor(private authService: AuthService) {}

  ngOnInit(): void {
    this.startHeroTimer();
    this.checkCelebration();
  }

  private startHeroTimer(): void {
    setInterval(() => {
      this.nextHero();
    }, 6000);
  }

  nextHero(): void {
    this.currentHeroIndex.update(idx => (idx + 1) % this.heroBanners.length);
  }

  prevHero(): void {
    this.currentHeroIndex.update(idx => (idx - 1 + this.heroBanners.length) % this.heroBanners.length);
  }

  checkCelebration(): void {
    if (!this.authService.isAuthenticated()) return;
    
    const user = this.authService.currentUser();
    if (!user) return;

    const todayStr = new Date().toISOString().substring(5, 10); // MM-DD
    
    if (user.dob && user.dob.substring(5, 10) === todayStr) {
      this.celebrationType.set('birthday');
      this.celebrationMessage.set("It's your special day! Shine bright like our diamonds with exclusive birthday offers!");
      this.showCelebration.set(true);
    } else if (user.anniversary && user.anniversary.substring(5, 10) === todayStr) {
      this.celebrationType.set('anniversary');
      this.celebrationMessage.set("Cheers to your love story! Celebrate this milestone with our anniversary collection!");
      this.showCelebration.set(true);
    }
  }

  closeCelebration(): void {
    this.showCelebration.set(false);
  }
}
