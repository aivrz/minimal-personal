class MinimalPersonalLightbox {
    constructor() {
        this.lightbox = document.getElementById('lightbox');
        this.lightboxImage = document.getElementById('lightboxImage');
        this.lightboxCaption = document.getElementById('lightboxCaption');
        this.lightboxClose = document.getElementById('lightboxClose');
        this.lightboxPrev = document.getElementById('lightboxPrev');
        this.lightboxNext = document.getElementById('lightboxNext');
        
        this.images = [];
        this.currentIndex = 0;
        
        this.init();
    }
    
    init() {
        // 收集所有图片
        this.collectImages();
        
        // 绑定事件
        this.bindEvents();
    }
    
    collectImages() {
        const gridImages = document.querySelectorAll('.grid-image[data-image-src]');
        this.images = Array.from(gridImages).map(img => ({
            src: img.dataset.imageSrc,
            caption: img.alt,
            postId: img.dataset.postId
        }));
    }
    
    bindEvents() {
        // 图片点击事件
        document.querySelectorAll('.grid-image[data-image-src]').forEach((img, index) => {
            img.addEventListener('click', () => {
                this.open(index);
            });
        });
        
        // 灯箱控制事件
        this.lightboxClose.addEventListener('click', () => this.close());
        this.lightboxPrev.addEventListener('click', () => this.prev());
        this.lightboxNext.addEventListener('click', () => this.next());
        
        // 键盘控制
        document.addEventListener('keydown', (e) => {
            if (!this.lightbox.classList.contains('active')) return;
            
            switch(e.key) {
                case 'Escape':
                    this.close();
                    break;
                case 'ArrowLeft':
                    this.prev();
                    break;
                case 'ArrowRight':
                    this.next();
                    break;
            }
        });
        
        // 点击背景关闭
        this.lightbox.addEventListener('click', (e) => {
            if (e.target === this.lightbox) {
                this.close();
            }
        });
    }
    
    open(index) {
        this.currentIndex = index;
        this.updateImage();
        this.lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    close() {
        this.lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    prev() {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this.updateImage();
    }
    
    next() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
        this.updateImage();
    }
    
    updateImage() {
        const image = this.images[this.currentIndex];
        
        // 显示加载状态
        this.lightbox.classList.add('lightbox-loading');
        
        const img = new Image();
        img.onload = () => {
            this.lightboxImage.src = image.src;
            this.lightboxImage.alt = image.caption;
            this.lightboxCaption.textContent = image.caption;
            this.lightbox.classList.remove('lightbox-loading');
        };
        img.src = image.src;
        
        // 更新导航按钮状态
        this.lightboxPrev.style.display = this.images.length > 1 ? 'block' : 'none';
        this.lightboxNext.style.display = this.images.length > 1 ? 'block' : 'none';
    }
}

// 初始化灯箱
document.addEventListener('DOMContentLoaded', () => {
    new MinimalPersonalLightbox();
});