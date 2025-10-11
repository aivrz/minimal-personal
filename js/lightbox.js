class MinimalPersonalLightbox {
    constructor() {
        this.lightbox = document.getElementById('lightbox');
        this.lightboxImage = document.getElementById('lightboxImage');
        this.lightboxCaption = document.getElementById('lightboxCaption');
        this.lightboxClose = document.getElementById('lightboxClose');
        this.lightboxPrev = document.getElementById('lightboxPrev');
        this.lightboxNext = document.getElementById('lightboxNext');
        
        this.images = [];
        this.gridImages = []; // 新增实例属性存储网格图片
        this.currentIndex = 0;
        
        this.init();
    }
    
    init() {
        this.collectImages();
        this.bindEvents();
    }
    
    collectImages() {
        // 保存为实例属性而非局部变量
        this.gridImages = document.querySelectorAll('.grid-image[data-image-src]');
        const featuredImages = document.querySelectorAll('.article-featured-image img');
        const contentImages = document.querySelectorAll('.article-content img');
        
        this.images = [
            ...Array.from(this.gridImages).map(img => ({
                src: img.dataset.imageSrc,
                caption: img.alt,
                postId: img.dataset.postId
            })),
            ...Array.from(featuredImages).map(img => ({
                src: img.src,
                caption: img.alt,
                postId: img.closest('article')?.id?.replace('post-', '')
            })),
            ...Array.from(contentImages).map(img => ({
                src: img.dataset.imageSrc || img.src,
                caption: img.alt,
                postId: img.closest('article')?.id?.replace('post-', '')
            }))
        ];
    }
    
    bindEvents() {
        // 修复文章图片点击事件绑定
        document.querySelectorAll('.article-featured-image img, .article-content img').forEach(img => {
            img.addEventListener('click', () => {
                // 通过图片src查找在images数组中的索引
                const imageIndex = this.images.findIndex(
                    item => item.src === (img.dataset.imageSrc || img.src)
                );
                if (imageIndex !== -1) {
                    this.open(imageIndex);
                }
            });
        });
        
        // 网格图片点击事件
        this.gridImages.forEach(img => {
            img.addEventListener('click', () => {
                const index = parseInt(img.dataset.index || 0);
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
    
    // 其他方法保持不变...
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
        
        this.lightbox.classList.add('lightbox-loading');
        
        const img = new Image();
        img.onload = () => {
            this.lightboxImage.src = image.src;
            this.lightboxImage.alt = image.caption;
            this.lightboxCaption.textContent = image.caption;
            this.lightbox.classList.remove('lightbox-loading');
        };
        img.src = image.src;
        
        this.lightboxPrev.style.display = this.images.length > 1 ? 'block' : 'none';
        this.lightboxNext.style.display = this.images.length > 1 ? 'block' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new MinimalPersonalLightbox();
});